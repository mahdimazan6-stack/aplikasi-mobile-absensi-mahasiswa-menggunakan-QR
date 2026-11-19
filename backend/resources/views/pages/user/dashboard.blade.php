@extends('layouts.user')

@section('title', 'Dashboard')
@if(session('success'))
    <x-toast type="success" :message="session('success')" />
@endif
@if(session('error'))
    <x-toast type="error" :message="session('error')" />
@endif
@section('content')

    <div class="max-w-xl mx-auto">

        <h1 class="text-2xl font-semibold mb-6">Absensi Hari Ini</h1>

        <div class="bg-white border rounded-2xl p-6 shadow-sm">

            {{-- STATUS --}}
            <div class="mb-6">
                <p class="text-sm text-slate-500">Status Hari Ini</p>

                <div class="mt-2">
                    <x-status :status="$today['status']" />
                </div>

                <p class="text-sm text-slate-500 mt-2">

                    Jam Masuk:
                    {{ \Carbon\Carbon::parse($today['jam_masuk'])->format('H:i') }}
                    <br>

                    Check In:
                    {{ $today['check_in']
        ? \Carbon\Carbon::parse($today['check_in'])->format('H:i')
        : '-' 
            }}
                    <br>

                    Check Out:
                    {{ $today['check_out']
        ? \Carbon\Carbon::parse($today['check_out'])->format('H:i')
        : '-' 
            }}

                </p>

                @if($today['status'] === 'alpha')
                    <p class="text-xs text-slate-400 mt-1">
                        Belum melakukan absensi hari ini
                    </p>
                @endif

            </div>

            {{-- BUTTONS --}}
            <form method="POST" action="{{ route('user.checkin') }}" class="mb-3">
                @csrf
                <button onclick="return confirm('Yakin mau check in?')"
                    class="w-full bg-slate-900 text-white py-2 rounded-lg hover:bg-slate-800">
                    Check In
                </button>
            </form>

            <form method="POST" action="{{ route('user.checkout') }}" class="mb-3">
                @csrf
                <button onclick="return confirm('Yakin mau check out?')"
                    class="mt-3 w-full bg-slate-900 text-white py-2 rounded-lg hover:bg-slate-800">
                    Check Out
                </button>
            </form>

            <form method="POST" action="{{ route('user.reset') }}">
                @csrf

                <button class="w-full mt-4 bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition">
                    Reset Absensi
                </button>

            </form>

        </div>

    </div>

@endsection