<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\LeaveRequest;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $weekStart = now()->subDays(6)->startOfDay();
        $todayAttendance = Attendance::query()
            ->whereHas('session', fn ($query) => $query->whereDate('created_at', $today));
        $weekAttendance = Attendance::query()
            ->whereHas('session', fn ($query) => $query->whereDate('created_at', '>=', $weekStart));

        $stats = [
            'total_mahasiswa' => User::where('role', 'mahasiswa')->where('is_active', true)->count(),
            'total_dosen' => User::where('role', 'dosen')->where('is_active', true)->count(),
            'mata_kuliah' => Course::count(),
            'hadir_hari_ini' => (clone $todayAttendance)->where('status', 'hadir')->count(),
            'izin_hari_ini' => (clone $todayAttendance)->where('status', 'izin')->count(),
            'alpa_hari_ini' => (clone $todayAttendance)->where('status', 'alpa')->count(),
        ];

        $dailyAttendance = Attendance::query()
            ->join('attendance_sessions', 'attendance_sessions.id', '=', 'attendances.attendance_session_id')
            ->whereDate('attendance_sessions.created_at', '>=', $weekStart)
            ->selectRaw('DATE(attendance_sessions.created_at) as attendance_date, COUNT(*) as attendance_count')
            ->where('attendances.status', 'hadir')
            ->groupBy('attendance_date')
            ->pluck('attendance_count', 'attendance_date');

        $traffic = collect(range(6, 0))->map(function (int $daysAgo) use ($dailyAttendance): array {
            $date = now()->subDays($daysAgo);

            return [
                'hari' => $date->translatedFormat('D'),
                'jumlah' => (int) $dailyAttendance->get($date->toDateString(), 0),
            ];
        });

        $summary = [
            'hadir' => (clone $weekAttendance)->where('status', 'hadir')->count(),
            'izin' => (clone $weekAttendance)->where('status', 'izin')->count(),
            'alpa' => (clone $weekAttendance)->where('status', 'alpa')->count(),
            'izin_menunggu' => LeaveRequest::where('status', 'pending')->count(),
        ];

        return view('pages.admin.dashboard', compact('stats', 'traffic', 'summary'));
    }
}
