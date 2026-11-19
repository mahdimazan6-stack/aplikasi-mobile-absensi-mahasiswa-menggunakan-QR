<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.users.index', [
            'users' => User::query()
                ->whereIn('role', ['mahasiswa', 'dosen'])
                ->orderBy('role')
                ->orderBy('name')
                ->paginate(25),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'nim' => ['required', 'string', 'max:50', 'unique:users,nim'],
            'role' => ['required', Rule::in(['mahasiswa', 'dosen'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create($validated + ['is_active' => true]);

        return to_route('admin.pengguna.index')->with('success', 'Akun berhasil dibuat.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(in_array($user->role, ['mahasiswa', 'dosen'], true), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'nim' => ['required', 'string', 'max:50', Rule::unique('users', 'nim')->ignore($user->id)],
            'role' => ['required', Rule::in(['mahasiswa', 'dosen'])],
        ]);

        if (
            $validated['role'] !== $user->role
            && (
                $user->teachingCourses()->exists()
                || $user->enrolledCourses()->exists()
                || $user->attendanceSessions()->exists()
                || $user->attendances()->exists()
            )
        ) {
            return back()
                ->withErrors(['role' => 'Peran tidak dapat diubah karena akun sudah memiliki hubungan mata kuliah atau riwayat absensi.'])
                ->withInput();
        }

        $user->update($validated);

        return to_route('admin.pengguna.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        abort_unless(in_array($user->role, ['mahasiswa', 'dosen'], true), 404);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => $validated['password'],
            'api_token' => null,
        ]);

        return to_route('admin.pengguna.index')->with('success', 'Password diperbarui dan sesi mobile lama dicabut.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        abort_unless(in_array($user->role, ['mahasiswa', 'dosen'], true), 404);

        $active = ! $user->is_active;
        $user->update([
            'is_active' => $active,
            'api_token' => $active ? $user->api_token : null,
        ]);

        return to_route('admin.pengguna.index')->with(
            'success',
            $active ? 'Akun diaktifkan.' : 'Akun dinonaktifkan dan sesi mobile dicabut.'
        );
    }
}
