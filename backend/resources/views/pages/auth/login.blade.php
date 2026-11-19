@extends('layouts.auth')

@section('title', 'Login | Présent')

@section('content')
    <div class="flex items-center justify-center min-h-screen w-full px-4">
        <div class="flex flex-col md:flex-row items-center gap-8 lg:gap-12 max-w-4xl w-full">

            {{-- Brand di kiri --}}
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-6xl lg:text-7xl font-bold text-slate-800 tracking-wide">présent</h1>
                <p class="text-slate-500 mt-3 text-lg">Sistem Manajemen Kehadiran</p>
            </div>

            {{-- Form login di kanan --}}
            <div class="bg-white w-full md:w-[400px] p-8 rounded-2xl shadow-sm border">
                <form method="POST" action="/login">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-4">
                        <h1 class="text-center font-bold m-5">Silahkan Masuk Ke Akun Anda</h1>
                        <label class="text-sm font-medium text-slate-700 block mb-1">Email</label>
                        <input type="email" name="email" placeholder="Masukkan Email Anda" value="{{ old('email') }}" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 focus:border-slate-400 outline-none transition">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-6">
                        <label class="text-sm font-medium text-slate-700 block mb-1">Password</label>
                        <input type="password" name="password" placeholder="Masukkan Password Anda" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 focus:border-slate-400 outline-none transition">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-slate-800 text-white py-2 rounded-lg hover:bg-slate-900 transition text-sm font-medium">
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection