<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceApiController extends Controller
{
    public function courses(): JsonResponse
    {
        return response()->json([
            'courses' => Course::orderBy('name')->get(),
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

        $session = AttendanceSession::create([
            'course_id' => $validated['course_id'],
            'lecturer_id' => $user->id,
            'qr_token' => Str::random(64),
            'expires_at' => now()->addSeconds($validated['duration_seconds'] ?? 300),
        ]);

        return response()->json([
            'session_id' => $session->id,
            'qr_token' => $session->qr_token,
            'expires_at' => $session->expires_at->toIso8601String(),
        ], 201);
    }

    public function sessionAttendances(AttendanceSession $session): JsonResponse
    {
        $attendances = $session->attendances()->with('student')->get();
        return response()->json(['attendances' => $attendances]);
    }

    public function courseAttendances(Course $course): JsonResponse
    {
        $attendances = Attendance::with(['student', 'session.course'])
            ->whereHas('session', fn ($query) => $query->where('course_id', $course->id))
            ->latest('attended_at')
            ->get();

        return response()->json(['attendances' => $attendances]);
    }

    public function endSession(AttendanceSession $session): JsonResponse
    {
        $session->update(['ended_at' => now(), 'expires_at' => now()]);
        return response()->json(['message' => 'Sesi absensi telah berakhir']);
    }

    public function history(Request $request): JsonResponse
    {
        $history = Attendance::with(['session.course'])
            ->where('student_id', $request->user()->id)
            ->latest('attended_at')
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

        $session = AttendanceSession::where('qr_token', $validated['qr_token'])->first();
        if (!$session || $session->ended_at || $session->expires_at->isPast()) {
            return response()->json(['message' => 'QR tidak valid atau sudah kedaluwarsa'], 422);
        }

        $attendance = Attendance::where('attendance_session_id', $session->id)
            ->where('student_id', $user->id)
            ->first();

        if ($attendance) {
            return response()->json(['message' => 'Mahasiswa sudah melakukan absensi'], 409);
        }

        $attendance = Attendance::create([
            'attendance_session_id' => $session->id,
            'student_id' => $user->id,
            'status' => 'hadir',
            'attended_at' => now(),
        ]);

        return response()->json([
            'message' => 'Absensi berhasil',
            'attendance' => $attendance->load(['session.course', 'student']),
        ], 201);
    }
}
