@props(['color' => 'gray'])

@php
    $colors = [
        'green' => 'bg-green-600 hover:bg-green-700 text-white',
        'red' => 'bg-red-600 hover:bg-red-700 text-white',
        'blue' => 'bg-blue-600 hover:bg-blue-700 text-white',
        'gray' => 'bg-gray-200 hover:bg-gray-300 text-black',
    ];
@endphp

<button type="{{ $attributes->get('type', 'button') }}" {{ $attributes->merge([
    'class' => $colors[$color] . ' px-4 py-2 rounded-lg text-sm transition'
]) }}>
    {{ $slot }}
</button>