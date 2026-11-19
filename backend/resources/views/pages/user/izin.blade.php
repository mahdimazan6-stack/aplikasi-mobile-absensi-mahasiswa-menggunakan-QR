@extends('layouts.user')

@section('content')

    <div class="max-w-2xl mx-auto">

        <h1 class="text-xl font-semibold mb-6">Riwayat Izin</h1>

        {{-- FORM IZIN --}}
        <form method="POST" action="{{ route('user.izin.store') }}" enctype="multipart/form-data"
            class="mb-6 bg-white p-5 rounded-xl border shadow-sm">

            @csrf

            <label class="text-sm font-medium">Mata Kuliah</label>
            <select name="course_id" required class="mt-2 w-full rounded-lg border p-3">
                <option value="">Pilih mata kuliah</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>{{ $course->code }} - {{ $course->name }}</option>
                @endforeach
            </select>

            <label class="mt-3 block text-sm font-medium">Tanggal izin</label>
            <input type="date" name="request_date" value="{{ old('request_date') }}" required class="mt-2 w-full rounded-lg border p-3">

            <label class="mt-3 block text-sm font-medium">Alasan izin</label>
            <textarea name="reason" required maxlength="2000"
                class="w-full mt-2 border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
                placeholder="Tulis alasan izin...">{{ old('reason') }}</textarea>

            {{-- FILE SURAT IZIN --}}
            <div class="mt-3">
                <label class="text-sm font-medium">Surat Izin (opsional)</label>

                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full mt-2 text-sm border rounded-lg p-2">
                <p class="mt-1 text-xs text-slate-500">PDF, JPG, atau PNG; maksimal 5 MB.</p>
            </div>

            @if($errors->any())
                <div class="mt-3 text-sm text-red-600">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
            @endif
            @if(session('success'))
                <p class="mt-3 text-sm text-green-700">{{ session('success') }}</p>
            @endif

            <button class="mt-3 w-full bg-slate-900 text-white py-2 rounded-lg text-sm hover:bg-slate-800 transition">
                Kirim Izin
            </button>

        </form>
        

        {{-- LIST IZIN --}}
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">

            @forelse($izin as $item)

                <div class="p-4 border-b flex justify-between items-center">

                    {{-- LEFT --}}
                    <div>
                        <p class="font-medium text-sm">{{ $item->course->name }} · {{ $item->request_date->format('d M Y') }}</p>

                        <p class="text-xs text-gray-500">
                            {{ $item->reason }}
                        </p>

                        {{-- FILE --}}
                        @if($item->attachment_path)
                            <a href="{{ route('user.izin.attachment', $item) }}" class="text-xs text-blue-500 mt-1 block">
                                Unduh lampiran
                            </a>
                        @else
                            <p class="text-xs text-gray-400 mt-1">
                                Tidak ada file
                            </p>
                        @endif
                    </div>

                    {{-- STATUS --}}
                    <x-status :status="$item->status" />

                </div>
                <div class="mt-4">{{ $izin->links() }}</div>


            @empty

                <div class="p-6 text-center text-sm text-gray-500">
                    Belum ada pengajuan izin
                </div>

            @endforelse

        </div>

    </div>

@endsection