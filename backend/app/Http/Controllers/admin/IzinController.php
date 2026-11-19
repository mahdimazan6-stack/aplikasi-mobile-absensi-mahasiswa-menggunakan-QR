<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IzinController extends Controller
{
    // lihat semua izin
    public function index()
    {
        $izin = session('izin', []);

        return view('pages.admin.izin', [
            'izin' => $this->sanitizeIzin($izin)
        ]);
    }

    // approve izin
    public function approve($id)
    {
        $izin = session('izin', []);

        foreach ($izin as &$item) {

            if (($item['id'] ?? null) == $id) {
                $item['status'] = 'Disetujui';
            }

        }

        session()->put('izin', $izin);

        return back();
    }
    public function reject($id)
    {
        $izin = session('izin', []);

        foreach ($izin as &$item) {

            if (($item['id'] ?? null) == $id) {
                $item['status'] = 'Ditolak';
            }

        }

        session()->put('izin', $izin);

        return back();
    }
    private function sanitizeIzin($data)
    {
        return array_map(function ($item) {
            return [
                'id' => $item['id'] ?? uniqid(),
                'nama' => $item['nama'] ?? 'Unknown',
                'tanggal' => $item['tanggal'] ?? date('Y-m-d'),
                'alasan' => $item['alasan'] ?? '-',
                'file' => $item['file'] ?? null,
                'status' => $item['status'] ?? 'Pending',
            ];
        }, $data);
    }
}