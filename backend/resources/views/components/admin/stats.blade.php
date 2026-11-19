@props(['stats'])

<div class="grid grid-cols-2 gap-4 mb-8 lg:grid-cols-3">

    <x-admin.card title="Mahasiswa Aktif" :value="$stats['total_mahasiswa']" />
    <x-admin.card title="Dosen Aktif" :value="$stats['total_dosen']" />
    <x-admin.card title="Mata Kuliah" :value="$stats['mata_kuliah']" />
    <x-admin.card title="Hadir Hari Ini" :value="$stats['hadir_hari_ini']" />
    <x-admin.card title="Izin Hari Ini" :value="$stats['izin_hari_ini']" />
    <x-admin.card title="Tidak Hadir Hari Ini" :value="$stats['alpa_hari_ini']" />

</div>