@props(['stats'])

<div class="grid grid-cols-5 gap-6 mb-8">

    <x-admin.card title="Total Karyawan" :value="$stats['total_karyawan']" />
    <x-admin.card title="Hadir Hari Ini" :value="$stats['hadir']" />
    <x-admin.card title="Sedang Izin" :value="$stats['izin']" />
    <x-admin.card title="Terlambat" :value="$stats['terlambat']" />
    <x-admin.card title="Tidak Hadir" :value="$stats['alpa']" />

</div>