@extends('layouts.admin')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Permohonan Izin</h1>
        <p class="text-slate-500 text-sm">Kelola pengajuan izin mahasiswa</p>
    </div>

    <div class="bg-white rounded-2xl border shadow-sm p-6">

        <table class="w-full text-sm table-fixed">

            <thead>
                <tr class="border-b text-slate-500 text-left">
                    <th class="py-3 w-[15%]">Mahasiswa</th>
                    <th class="py-3 w-[15%]">Mata Kuliah / Tanggal</th>
                    <th class="py-3 w-[25%]">Alasan</th>
                    <th class="py-3 w-[12%]">Lampiran</th>
                    <th class="py-3 w-[15%]">Status</th>
                    <th class="py-3 w-[15%] text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse($izin as $item)

                    <tr class="border-b hover:bg-slate-50 transition">

                        {{-- NAMA --}}
                        <td class="py-3 font-medium">
                            {{ $item->student->name }}<br>
                            <span class="text-xs text-slate-500">{{ $item->student->nim }}</span>
                        </td>

                        {{-- TANGGAL --}}
                        <td class="py-3 text-slate-600">
                            {{ $item->course->name }}<br>
                            <span class="text-xs text-slate-500">{{ $item->request_date->format('d M Y') }}</span>
                        </td>

                        {{-- ALASAN --}}
                        <td class="py-3 text-slate-700">
                            {{ $item->reason }}
                        </td>

                        {{-- FILE --}}
                        <td class="py-3">
                            @if($item->attachment_path)
                                <a href="{{ route('admin.izin.attachment', $item) }}" class="text-blue-600 text-sm underline">
                                    Unduh lampiran
                                </a>
                            @else
                                <span class="text-slate-400 text-xs">Tidak ada</span>
                            @endif
                        </td>

                        {{-- STATUS --}}
                        <td class="py-3">
                            <x-status :status="$item->status" />
                        </td>

                        {{-- AKSI --}}
                        <td class="py-3 text-right space-x-2">

                            @if($item->status === 'pending')

                                {{-- APPROVE --}}
                                <form method="POST" action="{{ route('admin.izin.approve', $item) }}" class="inline">
                                    @csrf

                                    <x-admin.button type="submit" color="green">
                                        Setuju
                                    </x-admin.button>
                                </form>

                                {{-- REJECT --}}
                                <form method="POST" action="{{ route('admin.izin.reject', $item) }}" class="inline">
                                    @csrf

                                    <x-admin.button type="submit" color="red">
                                        Tolak
                                    </x-admin.button>
                                </form>

                            @else
                                <span class="text-xs text-slate-400">
                                    Selesai
                                </span>
                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="text-center py-6 text-slate-500">
                            Belum ada pengajuan izin
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>
        <div class="mt-4">{{ $izin->links() }}</div>

    </div>

@endsection