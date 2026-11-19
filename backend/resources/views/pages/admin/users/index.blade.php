@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Mahasiswa dan Dosen</h1>
        <p class="text-sm text-slate-500">Kelola akun, akses, dan kredensial pengguna.</p>
    </div>

    @if(session('success'))
        <p class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ session('success') }}</p>
    @endif
    @if(session('error'))
        <p class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</p>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.pengguna.store') }}" class="mb-6 rounded-2xl border bg-white p-6 shadow-sm">
        @csrf
        <h2 class="mb-4 text-lg font-semibold">Tambah akun</h2>
        <div class="grid gap-4 md:grid-cols-2">
            <label class="text-sm">Nama
                <input name="name" value="{{ old('name') }}" required maxlength="255" class="mt-1 w-full rounded-lg border p-2">
            </label>
            <label class="text-sm">NIM / NIDN
                <input name="nim" value="{{ old('nim') }}" required maxlength="50" class="mt-1 w-full rounded-lg border p-2">
            </label>
            <label class="text-sm">Email
                <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-lg border p-2">
            </label>
            <label class="text-sm">Peran
                <select name="role" required class="mt-1 w-full rounded-lg border bg-white p-2">
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen" @selected(old('role') === 'dosen')>Dosen</option>
                </select>
            </label>
            <label class="text-sm">Password
                <input type="password" name="password" required minlength="8" autocomplete="new-password" class="mt-1 w-full rounded-lg border p-2">
            </label>
            <label class="text-sm">Konfirmasi password
                <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="mt-1 w-full rounded-lg border p-2">
            </label>
        </div>
        <button class="mt-4 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white">Buat akun</button>
    </form>

    <div class="overflow-x-auto rounded-2xl border bg-white p-6 shadow-sm">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b text-slate-500">
                    <th class="py-3">Nama / identitas</th>
                    <th class="py-3">Email</th>
                    <th class="py-3">Peran</th>
                    <th class="py-3">Status</th>
                    <th class="py-3">Perbarui akun</th>
                    <th class="py-3">Reset password</th>
                    <th class="py-3">Akses</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-b align-top">
                        <td class="py-3">{{ $user->name }}<br><span class="text-xs text-slate-500">{{ $user->nim }}</span></td>
                        <td class="py-3">{{ $user->email }}</td>
                        <td class="py-3">{{ ucfirst($user->role) }}</td>
                        <td class="py-3">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        <td class="py-3">
                            <form method="POST" action="{{ route('admin.pengguna.update', $user) }}" class="grid min-w-48 gap-2">
                                @csrf @method('PUT')
                                <input name="name" value="{{ $user->name }}" required class="rounded border p-2">
                                <input name="nim" value="{{ $user->nim }}" required class="rounded border p-2">
                                <input type="email" name="email" value="{{ $user->email }}" required class="rounded border p-2">
                                <select name="role" class="rounded border bg-white p-2">
                                    <option value="mahasiswa" @selected($user->role === 'mahasiswa')>Mahasiswa</option>
                                    <option value="dosen" @selected($user->role === 'dosen')>Dosen</option>
                                </select>
                                <button class="rounded bg-blue-700 px-3 py-2 text-white">Simpan</button>
                            </form>
                        </td>
                        <td class="py-3">
                            <form method="POST" action="{{ route('admin.pengguna.password', $user) }}" class="grid min-w-48 gap-2">
                                @csrf @method('PUT')
                                <input type="password" name="password" required minlength="8" placeholder="Password baru" autocomplete="new-password" class="rounded border p-2">
                                <input type="password" name="password_confirmation" required minlength="8" placeholder="Ulangi password" autocomplete="new-password" class="rounded border p-2">
                                <button class="rounded bg-amber-600 px-3 py-2 text-white">Reset</button>
                            </form>
                        </td>
                        <td class="py-3">
                            <form method="POST" action="{{ route('admin.pengguna.status', $user) }}">
                                @csrf @method('PATCH')
                                <button class="rounded px-3 py-2 {{ $user->is_active ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }}">
                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-6 text-center text-slate-500">Belum ada akun mahasiswa atau dosen.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $users->links() }}</div>
    </div>
@endsection
