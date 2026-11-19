<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttendanceApiController extends Controller
{
    public function courses(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless(in_array($user->role, ['mahasiswa', 'dosen'], true), 403);

        $courses = match ($user->role) {
            'mahasiswa' => $user->enrolledCourses(),
            'dosen' => $user->teachingCourses(),
        };

        return response()->json([
            'courses' => $courses->orderBy('name')->get(),
        ]);
    }

    public function createSession(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->role !== 'dosen') {
            return response()->json(['message' => 'Akses hanya untuk dosen'], 403);
        }

        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'duration_seconds' => ['sometimes', 'integer', 'min:30', 'max:86400'],
        ]);
        $course = Course::findOrFail($validated['course_id']);

        abort_unless($course->lecturers()->whereKey($user->id)->exists(), 403);

        $session = DB::transaction(function () use ($course, $user, $validated): AttendanceSession {
            $studentIds = $course->students()
                ->where('users.is_active', true)
                ->pluck('users.id');

            abort_if($studentIds->isEmpty(), 422, 'Mata kuliah belum memiliki mahasiswa terdaftar');

            $session = AttendanceSession::create([
                'course_id' => $course->id,
                'lecturer_id' => $user->id,
                'qr_token' => Str::random(64),
                'expires_at' => now()->addSeconds($validated['duration_seconds'] ?? 300),
            ]);
            $session->students()->attach($studentIds);

            return $session;
        });

        return response()->json([
            'session_id' => $session->id,
            'qr_token' => $session->qr_token,
            'expires_at' => $session->expires_at->toIso8601String(),
        ], 201);
    }

    public function sessionAttendances(Request $request, AttendanceSession $session): JsonResponse
    {
        abort_unless(
            $request->user()->role === 'dosen'
            && $session->lecturer_id === $request->user()->id,
            403
        );

        $attendances = $session->attendances()->with('student')->get();

        return response()->json(['attendances' => $attendances]);
    }

    public function courseAttendances(Request $request, Course $course): JsonResponse
    {
        abort_unless($request->user()->role === 'dosen', 403);
        abort_unless($course->lecturers()->whereKey($request->user()->id)->exists(), 403);

        $attendances = Attendance::with(['student', 'session.course'])
            ->whereHas('session', fn ($query) => $query
                ->where('course_id', $course->id)
                ->where('lecturer_id', $request->user()->id))
            ->latest('created_at')
            ->get();

        return response()->json(['attendances' => $attendances]);
    }

    public function endSession(Request $request, AttendanceSession $session): JsonResponse
    {
        abort_unless(
            $request->user()->role === 'dosen'
            && $session->lecturer_id === $request->user()->id,
            403
        );

        DB::transaction(function () use ($session): void {
            $lockedSession = AttendanceSession::query()
                ->lockForUpdate()
                ->findOrFail($session->id);

            if ($lockedSession->ended_at) {
                return;
            }

            $closedAt = now();
            $lockedSession->update(['ended_at' => $closedAt, 'expires_at' => $closedAt]);

            $enrolledStudentIds = $lockedSession->students()->pluck('users.id');

            $alreadyMarkedIds = $lockedSession->attendances()->pluck('student_id');
            $missingStudentIds = $enrolledStudentIds->diff($alreadyMarkedIds);

            if ($missingStudentIds->isEmpty()) {
                return;
            }

            $sessionDate = $lockedSession->created_at->toDateString();
            $approvedLeaveIds = DB::table('leave_requests')
                ->where('course_id', $lockedSession->course_id)
                ->whereDate('request_date', $sessionDate)
                ->where('status', 'approved')
                ->whereIn('student_id', $missingStudentIds)
                ->pluck('student_id')
                ->map(fn ($studentId): int => (int) $studentId)
                ->all();

            $timestamp = $closedAt->toDateTimeString();
            $lockedSession->attendances()->insert(
                $missingStudentIds->map(fn ($studentId): array => [
                    'attendance_session_id' => $lockedSession->id,
                    'student_id' => (int) $studentId,
                    'status' => in_array((int) $studentId, $approvedLeaveIds, true) ? 'izin' : 'alpa',
                    'attended_at' => null,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ])->all()
            );
        });

        return response()->json(['message' => 'Sesi absensi telah berakhir']);
    }

    public function history(Request $request): JsonResponse
    {
        abort_unless($request->user()->role === 'mahasiswa', 403);

        $history = Attendance::with(['session.course'])
            ->where('student_id', $request->user()->id)
            ->latest('created_at')
            ->get();

        return response()->json(['history' => $history]);
    }

    public function scan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_token' => ['required', 'string'],
        ]);

        $user = $request->user();
        if ($user->role !== 'mahasiswa') {
            return response()->json(['message' => 'Akses hanya untuk mahasiswa'], 403);
        }

        $result = DB::transaction(function () use ($validated, $user) {
            $session = AttendanceSession::query()
                ->where('qr_token', $validated['qr_token'])
                ->lockForUpdate()
                ->first();

            if (! $session || $session->ended_at || $session->expires_at->isPast()) {
                return null;
            }

            abort_unless(
                $session->students()->whereKey($user->id)->exists(),
                403,
                'Mahasiswa tidak terdaftar pada mata kuliah ini'
            );

            $now = now();
            $inserted = DB::table('attendances')->insertOrIgnore([
                'attendance_session_id' => $session->id,
                'student_id' => $user->id,
                'status' => 'hadir',
                'attended_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if (! $inserted) {
                return false;
            }

            return Attendance::query()
                ->where('attendance_session_id', $session->id)
                ->where('student_id', $user->id)
                ->firstOrFail()
                ->load(['session.course', 'student']);
        });

        if ($result === null) {
            return response()->json(['message' => 'QR tidak valid atau sudah kedaluwarsa'], 422);
        }

        if ($result === false) {
            return response()->json(['message' => 'Mahasiswa sudah melakukan absensi'], 409);
        }

        return response()->json([
            'message' => 'Absensi berhasil',
            'attendance' => $result,
        ], 201);
    }
}
