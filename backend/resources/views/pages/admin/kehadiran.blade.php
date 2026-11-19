@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Kehadiran</h1>
        <p class="text-slate-500 text-sm">Rekap kehadiran mahasiswa</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left">
                    <th class="py-3">Nama</th>
                    <th class="py-3">NIM</th>
                    <th class="py-3">Mata Kuliah</th>
                    <th class="py-3">Tanggal Sesi</th>
                    <th class="py-3">Waktu Hadir</th>
                    <th class="py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kehadiran as $item)
                    <tr class="border-b">
                        <td class="py-3">{{ $item->student->name }}</td>
                        <td class="py-3">{{ $item->student->nim }}</td>
                        <td class="py-3">{{ $item->session->course->name }} ({{ $item->session->course->code }})</td>
                        <td class="py-3">{{ $item->session->created_at->format('d M Y') }}</td>
                        <td class="py-3">{{ $item->attended_at?->format('H:i') ?? '-' }}</td>
                        <td class="py-3">{{ ucfirst($item->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-6 text-slate-500">Belum ada data kehadiran</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $kehadiran->links() }}</div>
    </div>
@endsection
