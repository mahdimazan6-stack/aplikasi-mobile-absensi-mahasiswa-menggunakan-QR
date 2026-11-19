@props(['summary'])

<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">

    <div class="bg-white p-4 rounded-xl border">
        <p class="text-sm text-slate-500">Total Hadir</p>
        <h2 class="text-2xl font-semibold">{{ $summary['hadir'] }}</h2>
    </div>

    <div class="bg-white p-4 rounded-xl border">
        <p class="text-sm text-slate-500">Total Izin</p>
        <h2 class="text-2xl font-semibold">{{ $summary['izin'] }}</h2>
    </div>

    <div class="bg-white p-4 rounded-xl border">
        <p class="text-sm text-slate-500">Total Tidak Hadir</p>
        <h2 class="text-2xl font-semibold">{{ $summary['alpa'] }}</h2>
    </div>

    <div class="bg-white p-4 rounded-xl border">
        <p class="text-sm text-slate-500">Permohonan Izin Menunggu</p>
        <h2 class="text-2xl font-semibold">{{ $summary['izin_menunggu'] }}</h2>
    </div>

</div>