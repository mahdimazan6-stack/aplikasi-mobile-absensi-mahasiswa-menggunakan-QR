@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Mata Kuliah</h1>
            <p class="text-sm text-slate-500">Kelola dosen pengampu dan daftar mahasiswa terdaftar.</p>
        </div>
        <a href="{{ route('admin.mata-kuliah.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm text-white">Tambah mata kuliah</a>
    </div>

    @if(session('success'))
        <p class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ session('success') }}</p>
    @endif
    @if(session('error'))
        <p class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</p>
    @endif

    <div class="overflow-x-auto rounded-2xl border bg-white p-6 shadow-sm">
        <table class="w-full text-left text-sm">
            <thead><tr class="border-b text-slate-500">
                <th class="py-3">Kode / Mata Kuliah</th><th class="py-3">Jadwal</th><th class="py-3">Dosen</th><th class="py-3">Mahasiswa</th><th class="py-3">Sesi</th><th class="py-3">Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($courses as $course)
                    <tr class="border-b align-top">
                        <td class="py-3">{{ $course->code }}<br>{{ $course->name }}<br><span class="text-xs text-slate-500">{{ $course->room }} {{ $course->kelas ? '· '.$course->kelas : '' }}</span></td>
                        <td class="py-3">{{ $course->day }}<br>{{ $course->time }}</td>
                        <td class="py-3">{{ $course->lecturers->pluck('name')->join(', ') }}</td>
                        <td class="py-3">{{ $course->students_count }}</td>
                        <td class="py-3">{{ $course->attendance_sessions_count }}</td>
                        <td class="py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.mata-kuliah.edit', $course) }}" class="rounded bg-blue-50 px-3 py-2 text-blue-700">Edit / peserta</a>
                                <form method="POST" action="{{ route('admin.mata-kuliah.destroy', $course) }}" onsubmit="return confirm('Hapus mata kuliah ini?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded bg-red-50 px-3 py-2 text-red-700">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-6 text-center text-slate-500">Belum ada mata kuliah.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $courses->links() }}</div>
    </div>
@endsection
