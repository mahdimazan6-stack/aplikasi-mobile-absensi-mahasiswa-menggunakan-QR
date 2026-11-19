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
                    <th class="py-3">Waktu</th>
                    <th class="py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kehadiran as $item)
                    <tr class="border-b">
                        <td class="py-3">{{ $item->name }}</td>
                        <td class="py-3">{{ $item->nim }}</td>
                        <td class="py-3">{{ $item->mata_kuliah }}</td>
                        <td class="py-3">{{ \Carbon\Carbon::parse($item->attended_at)->format('d M Y H:i') }}</td>
                        <td class="py-3">{{ $item->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-6 text-slate-500">Belum ada data kehadiran</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
