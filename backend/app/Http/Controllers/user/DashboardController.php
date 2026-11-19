<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // ======================
    // DASHBOARD ABSENSI
    // ======================
    public function dashboard()
    {
        $attendance = session()->get('attendance', []);

        $today = null;

        foreach ($attendance as $item) {
            if ($item['tanggal'] === date('Y-m-d')) {
                $today = $item;
            }
        }

        // 🔥 TAMBAHAN PENTING
        if (!$today) {
            $today = [
                'nama' => 'Yanuar Abdullah Ammardian',
                'divisi' => 'IT',
                'tanggal' => date('Y-m-d'),
                'check_in' => null,
                'check_out' => null,
                'status' => 'alpha',
                'jam_masuk' => '07:00:00', // ✅ biar selalu ada
            ];
        }

        return view('pages.user.dashboard', compact('today'));
    }

    public function izin()
    {
        $izin = session('izin', []);

        return view('pages.user.izin', compact('izin'));
    }

    public function storeIzin(Request $request)
    {
        $izin = session('izin', []);

        $fileName = null;

        if ($request->hasFile('file')) {
            $fileName = $request->file('file')->getClientOriginalName();
        }

        $izin[] = [
            'id' => uniqid(),
            'nama' => 'User Demo',
            'tanggal' => date('Y-m-d'),
            'alasan' => $request->alasan ?? '-',
            'file' => $fileName, //
            'status' => 'Pending',
        ];

        session(['izin' => $izin]);

        return back()->with('success', 'Pengajuan izin berhasil dikirim');
    }

    public function laporan()
    {
        $attendance = session()->get('attendance', []);

        // kalau belum ada data sama sekali (biar ga kosong banget)
        if (empty($attendance)) {
            $attendance[] = [
                'tanggal' => date('Y-m-d'),
                'check_in' => null,
                'check_out' => null,
                'status' => 'alpha'
            ];
        }

        return view('pages.user.laporan', compact('attendance'));
    }
    public function checkIn()
    {
        $data = session()->get('attendance', []);

        foreach ($data as $item) {
            if ($item['tanggal'] === date('Y-m-d')) {
                return back()->with('error', 'Sudah check in hari ini');
            }
        }

        // 🔥 SET JAM KERJA
        $jamMasuk = '07:00:00';
        $toleransi = '07:10:00';

        // waktu sekarang
        # $nowTime = '06:05:00'; // Untuk Testing Tepat Waktu
        # $now = strtotime($nowTime);
        $nowTime = now()->format('H:i:s');   // Sesuai Timezone Asia/Jakarta
        $now = strtotime($nowTime);          // Untuk Total Jam Kerja
        $toleransiTime = strtotime($toleransi);

        // tentukan status
        $status = $now > $toleransiTime ? 'telat' : 'hadir';

        $data[] = [
            'nama' => 'Yanuar Abdullah Ammardian',
            'divisi' => 'IT',
            'tanggal' => date('Y-m-d'),
            'check_in' => $nowTime,
            'check_out' => null,
            'status' => $status,
            'jam_masuk' => $jamMasuk, // 🔥 tambahan penting
        ];

        session()->put('attendance', $data);

        return back()->with('success', 'Check In berhasil');
    }

    public function checkOut()
    {
        $data = session()->get('attendance', []);

        foreach ($data as &$item) {
            if ($item['tanggal'] === date('Y-m-d')) {
                $item['check_out'] = date('H:i:s');
                $item['is_done'] = true; // penanda aja
            }
        }

        session()->put('attendance', $data);

        return back()->with('success', 'Check Out berhasil');
    }

    public function resetAbsen()
    {
        session()->forget('attendance');

        return back()->with('success', 'Absensi berhasil direset');
    }
    public function resetIzin()
    {
        session()->forget('izin');

        return back()->with('success', 'Data izin berhasil direset');
    }
}