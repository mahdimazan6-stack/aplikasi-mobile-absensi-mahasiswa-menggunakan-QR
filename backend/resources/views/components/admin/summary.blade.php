@props(['summary'])

<div class="grid grid-cols-4 gap-6">

    <div class="bg-white p-4 rounded-xl border">
        <p class="text-sm text-slate-500">Total Hadir</p>
        <h2 class="text-2xl font-semibold">{{ $summary['hadir'] }}</h2>
    </div>

    <div class="bg-white p-4 rounded-xl border">
        <p class="text-sm text-slate-500">Total Terlambat</p>
        <h2 class="text-2xl font-semibold">{{ $summary['terlambat'] }}</h2>
    </div>

    <div class="bg-white p-4 rounded-xl border">
        <p class="text-sm text-slate-500">Total Izin</p>
        <h2 class="text-2xl font-semibold">{{ $summary['izin'] }}</h2>
    </div>

    <div class="bg-white p-4 rounded-xl border">
        <p class="text-sm text-slate-500">Total Tidak Hadir</p>
        <h2 class="text-2xl font-semibold">{{ $summary['alpa'] }}</h2>
    </div>

</div>