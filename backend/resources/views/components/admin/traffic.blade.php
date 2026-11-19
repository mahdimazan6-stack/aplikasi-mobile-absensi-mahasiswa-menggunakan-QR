@props(['traffic'])

<div class="bg-white rounded-2xl p-6 shadow-sm border mb-8">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Grafik Kehadiran</h2>
        <span class="text-sm text-slate-500">7 Hari Terakhir</span>
    </div>

    <div class="relative h-56">

        <div class="absolute inset-0 flex flex-col justify-between text-xs text-slate-300">
            <span>100</span>
            <span>75</span>
            <span>50</span>
            <span>25</span>
            <span>0</span>
        </div>

        <div class="flex items-end justify-between h-full ml-8">

            @foreach($traffic as $item)
                <div class="flex flex-col items-center">
                    <div class="w-10 bg-slate-700 rounded-t-lg hover:bg-slate-900 transition"
                        style="height: {{ $item['jumlah'] * 2 }}px">
                    </div>

                    <span class="text-xs mt-2 text-slate-500">
                        {{ $item['hari'] }}
                    </span>
                </div>
            @endforeach

        </div>

    </div>

</div>