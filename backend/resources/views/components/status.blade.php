@props(['status'])

@php
    $statusLower = strtolower($status);

    $classes = match ($statusLower) {
        'hadir' => 'bg-green-100 text-green-700',
        'telat' => 'bg-red-100 text-red-700',
        'selesai' => 'bg-blue-100 text-blue-700',
        'pending' => 'bg-yellow-100 text-yellow-700',
        'disetujui' => 'bg-green-100 text-green-700',
        'ditolak' => 'bg-red-100 text-red-700',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp

<span class="px-3 py-1 rounded-full text-xs {{ $classes }}">
    {{ ucfirst($status) }}
</span>