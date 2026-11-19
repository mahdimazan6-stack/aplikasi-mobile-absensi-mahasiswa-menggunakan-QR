<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class IzinController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.izin', [
            'izin' => LeaveRequest::query()
                ->with(['student:id,name,nim', 'course:id,name,code'])
                ->latest()
                ->paginate(25),
        ]);
    }

    public function approve(LeaveRequest $leaveRequest): RedirectResponse
    {
        return $this->review($leaveRequest, 'approved');
    }

    public function reject(LeaveRequest $leaveRequest): RedirectResponse
    {
        return $this->review($leaveRequest, 'rejected');
    }

    public function downloadAttachment(LeaveRequest $leaveRequest)
    {
        abort_unless($leaveRequest->attachment_path, 404);
        abort_unless(Storage::disk('local')->exists($leaveRequest->attachment_path), 404);

        return Storage::disk('local')->download($leaveRequest->attachment_path);
    }

    private function review(LeaveRequest $leaveRequest, string $status): RedirectResponse
    {
        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Permohonan ini sudah diproses.');
        }

        DB::transaction(function () use ($leaveRequest, $status): void {
            $leaveRequest->update([
                'status' => $status,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            if ($status === 'approved') {
                Attendance::query()
                    ->where('student_id', $leaveRequest->student_id)
                    ->where('status', 'alpa')
                    ->whereHas('session', fn ($query) => $query
                        ->where('course_id', $leaveRequest->course_id)
                        ->whereDate('created_at', $leaveRequest->request_date))
                    ->update(['status' => 'izin']);
            }
        });

        return back()->with('success', $status === 'approved' ? 'Permohonan disetujui.' : 'Permohonan ditolak.');
    }
}
