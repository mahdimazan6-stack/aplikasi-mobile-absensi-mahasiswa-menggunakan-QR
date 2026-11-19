@extends('layouts.user')

@section('title', 'Laporan')

@section('content')

    <div class="max-w-4xl mx-auto">

        <h1 class="text-2xl font-semibold mb-6">Laporan Absensi</h1>

        <div class="bg-white border rounded-2xl p-6">

            <table class="w-full text-sm">

                <thead>
                    <tr class="border-b text-left">
                        <th class="py-2">Tanggal</th>
                        <th class="py-2">Check In</th>
                        <th class="py-2">Check Out</th>
                        <th class="py-2">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($attendance as $item)
                        <tr class="border-b">
                            <td class="py-2">{{ $item['tanggal'] }}</td>
                            <td class="py-2">{{ $item['check_in'] }}</td>
                            <td class="py-2">{{ $item['check_out'] ?? '-' }}</td>
                            <td class="py-2">
                                <x-status :status="$item['status']" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>

@endsection