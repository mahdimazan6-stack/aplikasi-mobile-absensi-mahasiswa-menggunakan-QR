<div class="bg-white rounded-2xl p-6 shadow-sm border mb-8">

    <div class="flex flex-wrap gap-4 items-end">

        <div>
            <label class="text-sm text-slate-500">Dari Tanggal</label>
            <input type="date" class="border rounded-lg px-3 py-2">
        </div>

        <div>
            <label class="text-sm text-slate-500">Sampai Tanggal</label>
            <input type="date" class="border rounded-lg px-3 py-2">
        </div>

        <div>
            <label class="text-sm text-slate-500">Status Kehadiran</label>
            <select class="border rounded-lg px-3 py-2">
                <option>Semua</option>
                <option>Hadir</option>
                <option>Terlambat</option>
                <option>Izin</option>
            </select>
        </div>

        <x-admin.button color="blue">Terapkan</x-admin.button>

    </div>

</div>