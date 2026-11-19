@extends('layouts.user')

@section('title', 'Laporan')

@section('content')
    <div class="mx-auto max-w-4xl">
        <h1 class="mb-6 text-2xl font-semibold">Laporan Absensi</h1>
        <div class="overflow-x-auto rounded-2xl border bg-white p-6">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-left">
                        <th class="py-3">Tanggal Sesi</th>
                        <th class="py-3">Mata Kuliah</th>
                        <th class="py-3">Waktu Hadir</th>
                        <th class="py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendance as $item)
                        <tr class="border-b">
                            <td class="py-3">{{ $item->session->created_at->format('d M Y') }}</td>
                            <td class="py-3">{{ $item->session->course->name }} ({{ $item->session->course->code }})</td>
                            <td class="py-3">{{ $item->attended_at?->format('H:i') ?? '-' }}</td>
                            <td class="py-3"><x-status :status="$item->status" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-slate-500">Belum ada riwayat absensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $attendance->links() }}</div>
        </div>
    </div>
@endsection
