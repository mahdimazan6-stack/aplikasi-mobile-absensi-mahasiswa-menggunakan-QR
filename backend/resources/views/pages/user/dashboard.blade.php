@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
    <div class="mx-auto max-w-5xl">
        <h1 class="text-2xl font-semibold">Halo, {{ auth()->user()->name }}</h1>
        <p class="mb-6 text-sm text-slate-500">{{ ucfirst(auth()->user()->role) }} · {{ auth()->user()->nim }}</p>

        @if(auth()->user()->role === 'mahasiswa')
            <section class="mb-6 rounded-2xl border bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold">Absensi hari ini</h2>
                @forelse($todayAttendance as $item)
                    <div class="flex items-center justify-between border-b py-3 last:border-0">
                        <div>
                            <p class="font-medium">{{ $item->session->course->name }}</p>
                            <p class="text-xs text-slate-500">{{ $item->attended_at?->format('H:i') ?? 'Tidak ada waktu scan' }}</p>
                        </div>
                        <x-status :status="$item->status" />
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada sesi absensi yang ditutup hari ini.</p>
                @endforelse
                <p class="mt-4 text-xs text-slate-500">Untuk mencatat kehadiran, pindai QR sesi dosen melalui aplikasi mobile.</p>
            </section>

            <section class="rounded-2xl border bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold">Mata kuliah terdaftar</h2>
                <div class="grid gap-3 md:grid-cols-2">
                    @forelse($courses as $course)
                        <div class="rounded-xl border p-4">
                            <p class="font-medium">{{ $course->name }}</p>
                            <p class="text-sm text-slate-500">{{ $course->code }} · {{ $course->day }}, {{ $course->time }}</p>
                            <p class="text-sm text-slate-500">{{ $course->room }} {{ $course->kelas ? '· '.$course->kelas : '' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum didaftarkan ke mata kuliah. Hubungi administrator.</p>
                    @endforelse
                </div>
            </section>
        @else
            <section class="mb-6 rounded-2xl border bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold">Sesi yang dibuat hari ini</h2>
                @forelse($todaySessions as $session)
                    <div class="flex items-center justify-between border-b py-3 last:border-0">
                        <div>
                            <p class="font-medium">{{ $session->course->name }}</p>
                            <p class="text-xs text-slate-500">Dibuat {{ $session->created_at->format('H:i') }}</p>
                        </div>
                        <x-status :status="$session->ended_at ? 'selesai' : ($session->expires_at->isPast() ? 'kedaluwarsa' : 'aktif')" />
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada sesi absensi hari ini. Buat sesi melalui aplikasi mobile.</p>
                @endforelse
            </section>

            <section class="rounded-2xl border bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold">Mata kuliah diampu</h2>
                @forelse($courses as $course)
                    <div class="border-b py-3 last:border-0">
                        <p class="font-medium">{{ $course->name }}</p>
                        <p class="text-sm text-slate-500">{{ $course->code }} · {{ $course->day }}, {{ $course->time }} · {{ $course->room }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada mata kuliah yang ditugaskan.</p>
                @endforelse
            </section>
        @endif
    </div>
@endsection
