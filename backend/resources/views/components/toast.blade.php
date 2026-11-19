@props(['type' => 'success', 'message'])

@php
    $styles = match ($type) {
        'success' => 'bg-green-500',
        'error' => 'bg-red-500',
        'warning' => 'bg-yellow-500',
        default => 'bg-slate-800',
    };
@endphp

<div id="toast" class="fixed top-6 left-1/2 -translate-x-1/2
     w-[340px] {{ $styles }} text-white rounded-xl shadow-lg z-50
     px-5 py-3 text-center">

    {{ $message }}

</div>

<script>
    setTimeout(() => {
        const el = document.getElementById('toast');
        if (el) el.remove();
    }, 2000);
</script>