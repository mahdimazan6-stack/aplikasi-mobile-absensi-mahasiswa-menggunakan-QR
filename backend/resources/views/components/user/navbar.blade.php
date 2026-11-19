<nav class="bg-white border-b px-8 py-4 flex justify-between items-center">

    <div>
        <h1 class="text-lg font-semibold tracking-wide">
            Dashboard User
        </h1>
        <p class="text-sm text-slate-500">
            Sistem Manajemen Kehadiran
        </p>
    </div>

    <div class="flex items-center gap-4">
        <div class="text-right">
            <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
            <p class="text-xs text-slate-500">{{ ucfirst(auth()->user()->role) }}</p>
        </div>

        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-300 font-semibold text-slate-700">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
    </div>

</nav>