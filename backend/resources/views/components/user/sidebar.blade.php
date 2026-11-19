<aside class="w-64 bg-white border-r min-h-screen p-6">

    <h2 class="text-xl font-bold mb-8 tracking-wide">
        présent
    </h2>

    <nav class="space-y-2 text-sm">

        {{-- ABSEN (HOME) --}}
        <a href="{{ route('user.dashboard') }}" class="block px-4 py-2 rounded-lg
           {{ request()->routeIs('user.dashboard') ? 'bg-slate-800 text-white' : 'hover:bg-slate-100' }}">
            Absen
        </a>

        {{-- LAPORAN --}}
        <a href="{{ route('user.laporan') }}" class="block px-4 py-2 rounded-lg
           {{ request()->routeIs('user.laporan') ? 'bg-slate-800 text-white' : 'hover:bg-slate-100' }}">
            Laporan Absensi
        </a>

        {{-- PERIZINAN --}}
        <a href="{{ route('user.izin') }}" class="block px-4 py-2 rounded-lg
           {{ request()->routeIs('user.izin') ? 'bg-slate-800 text-white' : 'hover:bg-slate-100' }}">
            Perizinan
        </a>

        {{-- LOGOUT (PALING BAWAH) --}}
        <form method="POST" action="/logout" class="pt-6">
            @csrf
            <button class="w-full text-left px-4 py-2 rounded-lg text-red-500 hover:bg-red-50">
                Logout
            </button>
        </form>

    </nav>

</aside>