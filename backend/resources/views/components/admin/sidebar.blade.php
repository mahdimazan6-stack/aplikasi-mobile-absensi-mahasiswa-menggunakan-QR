<aside class="w-64 bg-white border-r min-h-screen p-6">

    <h2 class="text-xl font-bold mb-8 tracking-wide">
        présent
    </h2>

    <nav class="space-y-2 text-sm">

        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded-lg 
           {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-white' : 'hover:bg-slate-100' }}">
            Dashboard
        </a>

        <a href="{{ route('admin.karyawan.index') }}" class="block px-4 py-2 rounded-lg 
           {{ request()->routeIs('admin.karyawan.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-100' }}">
            Karyawan
        </a>

        <a href="{{ route('admin.divisi.index') }}" class="block px-4 py-2 rounded-lg 
           {{ request()->routeIs('admin.divisi.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-100' }}">
            Divisi
        </a>

        <a href="{{ route('admin.kehadiran.index') }}" class="block px-4 py-2 rounded-lg 
           {{ request()->routeIs('admin.kehadiran.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-100' }}">
            Kehadiran
        </a>

        <a href="{{ route('admin.izin.index') }}" class="block px-4 py-2 rounded-lg 
       {{ request()->routeIs('admin.izin.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-100' }}">
            Izin
        </a>
        <form method="POST" action="/logout" class="pt-6">
            @csrf
            <button class="w-full text-left px-4 py-2 rounded-lg text-red-500 hover:bg-red-50">
                Logout
            </button>
        </form>

    </nav>

</aside>