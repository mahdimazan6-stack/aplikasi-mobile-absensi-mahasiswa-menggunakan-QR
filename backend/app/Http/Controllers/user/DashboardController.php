<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $today = now()->toDateString();

        return view('pages.user.dashboard', [
            'todayAttendance' => $user->role === 'mahasiswa'
                ? Attendance::query()
                    ->with(['session.course'])
                    ->where('student_id', $user->id)
                    ->whereHas('session', fn ($query) => $query->whereDate('created_at', $today))
                    ->latest('created_at')
                    ->get()
                : collect(),
            'todaySessions' => $user->role === 'dosen'
                ? AttendanceSession::query()
                    ->with('course')
                    ->where('lecturer_id', $user->id)
                    ->whereDate('created_at', $today)
                    ->latest()
                    ->get()
                : collect(),
            'courses' => $user->role === 'mahasiswa'
                ? $user->enrolledCourses()->orderBy('name')->get()
                : $user->teachingCourses()->orderBy('name')->get(),
        ]);
    }

    public function izin(Request $request): View
    {
        abort_unless($request->user()->role === 'mahasiswa', 403);

        return view('pages.user.izin', [
            'courses' => $request->user()->enrolledCourses()->orderBy('name')->get(),
            'izin' => LeaveRequest::query()
                ->with('course:id,name,code')
                ->where('student_id', $request->user()->id)
                ->latest()
                ->paginate(20),
        ]);
    }

    public function storeIzin(Request $request): RedirectResponse
    {
        abort_unless($request->user()->role === 'mahasiswa', 403);

        $validated = $request->validate([
            'course_id' => [
                'required',
                'integer',
                Rule::exists('course_enrollments', 'course_id')
                    ->where('student_id', $request->user()->id),
            ],
            'request_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:2000'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $duplicatePendingRequest = LeaveRequest::query()
            ->where('student_id', $request->user()->id)
            ->where('course_id', $validated['course_id'])
            ->whereDate('request_date', $validated['request_date'])
            ->where('status', 'pending')
            ->exists();

        if ($duplicatePendingRequest) {
            return back()
                ->withErrors(['request_date' => 'Pengajuan untuk mata kuliah dan tanggal ini masih menunggu keputusan.'])
                ->withInput();
        }

        $attachmentPath = null;
        if ($request->hasFile('file')) {
            $attachmentPath = $request->file('file')->store('leave-requests', 'local');
            if (! $attachmentPath) {
                return back()
                    ->withErrors(['file' => 'Lampiran gagal disimpan. Coba lagi.'])
                    ->withInput();
            }
        }

        LeaveRequest::create([
            'student_id' => $request->user()->id,
            'course_id' => $validated['course_id'],
            'request_date' => $validated['request_date'],
            'reason' => $validated['reason'],
            'attachment_path' => $attachmentPath,
        ]);

        return to_route('user.izin')->with('success', 'Pengajuan izin berhasil dikirim.');
    }

    public function downloadAttachment(Request $request, LeaveRequest $leaveRequest)
    {
        abort_unless($leaveRequest->student_id === $request->user()->id, 404);
        abort_unless($leaveRequest->attachment_path, 404);
        abort_unless(Storage::disk('local')->exists($leaveRequest->attachment_path), 404);

        return Storage::disk('local')->download($leaveRequest->attachment_path);
    }

    public function laporan(Request $request): View
    {
        abort_unless($request->user()->role === 'mahasiswa', 403);

        $attendance = Attendance::query()
            ->with(['session.course'])
            ->where('student_id', $request->user()->id)
            ->latest('created_at')
            ->paginate(50);

        return view('pages.user.laporan', compact('attendance'));
    }
}
