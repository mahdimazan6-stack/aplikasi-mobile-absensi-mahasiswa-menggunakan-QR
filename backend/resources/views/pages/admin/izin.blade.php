@extends('layouts.admin')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Permohonan Izin</h1>
        <p class="text-slate-500 text-sm">Kelola pengajuan izin karyawan</p>
    </div>

    <div class="bg-white rounded-2xl border shadow-sm p-6">

        <table class="w-full text-sm table-fixed">

            <thead>
                <tr class="border-b text-slate-500 text-left">
                    <th class="py-3 w-[18%]">Nama</th>
                    <th class="py-3 w-[15%]">Tanggal</th>
                    <th class="py-3 w-[25%]">Alasan</th>
                    <th class="py-3 w-[12%]">File</th>
                    <th class="py-3 w-[15%]">Status</th>
                    <th class="py-3 w-[15%] text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse($izin as $item)

                    <tr class="border-b hover:bg-slate-50 transition">

                        {{-- NAMA --}}
                        <td class="py-3 font-medium">
                            {{ $item['nama'] }}
                        </td>

                        {{-- TANGGAL --}}
                        <td class="py-3 text-slate-600">
                            {{ $item['tanggal'] }}
                        </td>

                        {{-- ALASAN --}}
                        <td class="py-3 text-slate-700">
                            {{ $item['alasan'] }}
                        </td>

                        {{-- FILE --}}
                        <td class="py-3">
                            @if(!empty($item['file']))
                                <span class="text-blue-600 text-sm">
                                    {{ $item['file'] }}
                                </span>
                            @else
                                <span class="text-slate-400 text-xs">Tidak ada</span>
                            @endif
                        </td>

                        {{-- STATUS --}}
                        <td class="py-3">
                            <x-status :status="$item['status']" />
                        </td>

                        {{-- AKSI --}}
                        <td class="py-3 text-right space-x-2">

                            @if($item['status'] == 'Pending')

                                {{-- APPROVE --}}
                                <form method="POST" action="{{ route('admin.izin.approve', $item['id']) }}" class="inline">
                                    @csrf

                                    <x-admin.button type="submit" color="green">
                                        Setuju
                                    </x-admin.button>
                                </form>

                                {{-- REJECT --}}
                                <form method="POST" action="{{ route('admin.izin.reject', $item['id']) }}" class="inline">
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

    </div>

@endsection