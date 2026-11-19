@extends('layouts.admin')

@section('content')

    <div class="mb-8">
        <h1 class="text-3xl font-semibold mb-1">Dashboard</h1>
        <p class="text-slate-500">Ringkasan kehadiran mahasiswa dan dosen</p>
    </div>

    <x-admin.stats :stats="$stats" />

    <x-admin.traffic :traffic="$traffic" />

    <x-admin.summary :summary="$summary" />

@endsection