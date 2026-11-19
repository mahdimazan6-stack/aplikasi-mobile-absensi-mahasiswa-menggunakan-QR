@props(['id'])

<div id="{{ $id }}" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

    <div class="bg-white w-[500px] rounded-2xl p-6">

        {{ $slot }}

    </div>

</div>