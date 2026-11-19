<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = [
            [
                'id' => 1,
                'nama' => 'Budi',
                'divisi' => 'IT',
                'status' => 'Aktif',
            ],
            [
                'id' => 2,
                'nama' => 'Siti',
                'divisi' => 'HR',
                'status' => 'Aktif',
            ],
        ];

        $divisis = [];
        return view('pages.admin.karyawan', compact('karyawan', 'divisis'));
    }

    public function create()
    {
        return view('pages.admin.karyawan');
    }

    public function store(Request $request)
    {
        return back()->with('success', 'Data karyawan disimpan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if ($user) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email',
            ]);
            $user->update([
                'name' => $request->name,
                'email' => $request->email ?: $user->email,
            ]);
        }

        return back()->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function resetPassword(Request $request, $id)
    {
        $request->validate(['password' => 'required|min:6']);
        $user = User::findOrFail($id);
        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password berhasil direset.');
    }

    public function destroy($id)
    {
        return back()->with('success', 'Data karyawan berhasil dihapus.');
    }
}
