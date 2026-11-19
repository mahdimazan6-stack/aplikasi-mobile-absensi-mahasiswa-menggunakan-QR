<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KehadiranController extends Controller
{
    public function index()
    {
        $kehadiran = DB::table('attendances')
            ->join('users', 'users.id', '=', 'attendances.student_id')
            ->join('attendance_sessions', 'attendance_sessions.id', '=', 'attendances.attendance_session_id')
            ->join('courses', 'courses.id', '=', 'attendance_sessions.course_id')
            ->select(
                'users.name',
                'users.nim',
                'courses.name as mata_kuliah',
                'attendances.attended_at',
                'attendances.status',
            )
            ->latest('attendances.attended_at')
            ->get();
        return view('pages.admin.kehadiran', compact('kehadiran'));
    }
}