@extends('layouts.user')

@section('content')

    <div class="max-w-2xl mx-auto">

        <h1 class="text-xl font-semibold mb-6">Riwayat Izin</h1>

        {{-- FORM IZIN --}}
        <form method="POST" action="{{ route('user.izin.store') }}" enctype="multipart/form-data"
            class="mb-6 bg-white p-5 rounded-xl border shadow-sm">

            @csrf

            <label class="text-sm font-medium">Alasan Izin</label>

            <textarea name="alasan"
                class="w-full mt-2 border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
                placeholder="Tulis alasan izin..."></textarea>

            {{-- FILE SURAT IZIN --}}
            <div class="mt-3">
                <label class="text-sm font-medium">Surat Izin (opsional)</label>

                <input type="file" name="file" class="w-full mt-2 text-sm border rounded-lg p-2">
            </div>

            <button class="mt-3 w-full bg-slate-900 text-white py-2 rounded-lg text-sm hover:bg-slate-800 transition">
                Kirim Izin
            </button>

        </form>
        

        {{-- LIST IZIN --}}
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">

            @forelse($izin as $item)

                <div class="p-4 border-b flex justify-between items-center">

                    {{-- LEFT --}}
                    <div>
                        <p class="font-medium text-sm">
                            {{ $item['tanggal'] ?? '-' }}
                        </p>

                        <p class="text-xs text-gray-500">
                            {{ $item['alasan'] ?? '-' }}
                        </p>

                        {{-- FILE --}}
                        @if(!empty($item['file']))
                            <a href="#" class="text-xs text-blue-500 mt-1 block">
                                📎 {{ $item['file'] }}
                            </a>
                        @else
                            <p class="text-xs text-gray-400 mt-1">
                                Tidak ada file
                            </p>
                        @endif
                    </div>

                    {{-- STATUS --}}
                    <x-status :status="$item['status'] ?? 'Pending'" />

                </div>


            @empty

                <div class="p-6 text-center text-sm text-gray-500">
                    Belum ada pengajuan izin
                </div>

            @endforelse

        </div>

    </div>

@endsection