<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_karyawan' => max(User::count() - 1, 0),
            'hadir' => DB::table('attendances')->count(),
            'izin' => 0,
            'terlambat' => 0,
            'alpa' => 0,
        ];

        $traffic = [
            ['hari' => 'Sen', 'jumlah' => 30],
            ['hari' => 'Sel', 'jumlah' => 45],
            ['hari' => 'Rab', 'jumlah' => 25],
            ['hari' => 'Kam', 'jumlah' => 50],
            ['hari' => 'Jum', 'jumlah' => 40],
            ['hari' => 'Sab', 'jumlah' => 20],
            ['hari' => 'Min', 'jumlah' => 10],
        ];

        $summary = [
            'hadir' => DB::table('attendances')->count(),
            'terlambat' => 0,
            'izin' => 0,
            'alpa' => 0,
        ];

        return view('pages.admin.dashboard', compact('stats', 'traffic', 'summary'));
    }
}

