@props(['label', 'type' => 'text'])

<label class="text-sm">{{ $label }}</label>
<input type="{{ $type }}" class="w-full border rounded-lg px-3 py-2 mt-1">