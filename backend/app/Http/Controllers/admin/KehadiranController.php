<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\View\View;

class KehadiranController extends Controller
{
    public function index(): View
    {
        $kehadiran = Attendance::query()
            ->with(['student:id,name,nim', 'session.course:id,name,code'])
            ->latest('created_at')
            ->paginate(50);

        return view('pages.admin.kehadiran', compact('kehadiran'));
    }
}
