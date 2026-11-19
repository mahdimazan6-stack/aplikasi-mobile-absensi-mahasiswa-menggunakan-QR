<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DivisiController extends Controller
{
    public function index()
    {
        $divisi = [
            ['id' => 1, 'nama' => 'IT'],
            ['id' => 2, 'nama' => 'HR'],
            ['id' => 3, 'nama' => 'Finance'],
        ];

        return view('pages.admin.divisi', compact('divisi'));
    }

    public function create()
    {
        return view('admin.divisi_create');
    }
}