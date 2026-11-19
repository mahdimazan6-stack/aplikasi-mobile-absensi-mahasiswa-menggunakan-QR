@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">{{ $course->exists ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah' }}</h1>
        <p class="text-sm text-slate-500">Pilih minimal satu dosen pengampu dan kelola peserta kelas.</p>
    </div>

    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $course->exists ? route('admin.mata-kuliah.update', $course) : route('admin.mata-kuliah.store') }}" class="max-w-3xl space-y-5 rounded-2xl border bg-white p-6 shadow-sm">
        @csrf
        @if($course->exists) @method('PUT') @endif
        <div class="grid gap-4 md:grid-cols-2">
            <label class="text-sm">Kode
                <input name="code" value="{{ old('code', $course->code) }}" required maxlength="50" class="mt-1 w-full rounded-lg border p-2">
            </label>
            <label class="text-sm">Nama mata kuliah
                <input name="name" value="{{ old('name', $course->name) }}" required class="mt-1 w-full rounded-lg border p-2">
            </label>
            <label class="text-sm">Hari
                <select name="day" required class="mt-1 w-full rounded-lg border bg-white p-2">
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $day)
                        <option value="{{ $day }}" @selected(old('day', $course->day) === $day)>{{ $day }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm">Jam (mis. 08:00 - 10:00)
                <input name="time" value="{{ old('time', $course->time) }}" required maxlength="50" class="mt-1 w-full rounded-lg border p-2">
            </label>
            <label class="text-sm">Ruang
                <input name="room" value="{{ old('room', $course->room) }}" required maxlength="100" class="mt-1 w-full rounded-lg border p-2">
            </label>
            <label class="text-sm">Kelas / tautan
                <input name="kelas" value="{{ old('kelas', $course->kelas) }}" maxlength="100" class="mt-1 w-full rounded-lg border p-2">
            </label>
        </div>

        <label class="block text-sm">Dosen pengampu
            <select name="lecturer_ids[]" multiple required size="5" class="mt-1 w-full rounded-lg border bg-white p-2">
                @foreach($lecturers as $lecturer)
                    <option value="{{ $lecturer->id }}" @selected(collect(old('lecturer_ids', $course->lecturers->modelKeys()))->contains($lecturer->id))>{{ $lecturer->name }} ({{ $lecturer->nim }})</option>
                @endforeach
            </select>
            <span class="text-xs text-slate-500">Gunakan Ctrl atau Command untuk memilih lebih dari satu dosen.</span>
        </label>

        <label class="block text-sm">Mahasiswa peserta
            <select name="student_ids[]" multiple size="8" class="mt-1 w-full rounded-lg border bg-white p-2">
                @foreach($students as $student)
                    <option value="{{ $student->id }}" @selected(collect(old('student_ids', $course->students->modelKeys()))->contains($student->id))>{{ $student->name }} ({{ $student->nim }})</option>
                @endforeach
            </select>
            <span class="text-xs text-slate-500">Daftar ini menjadi dasar pencatatan otomatis tidak hadir saat sesi ditutup.</span>
        </label>

        <div class="flex gap-3">
            <button class="rounded-lg bg-slate-900 px-4 py-2 text-white">Simpan</button>
            <a href="{{ route('admin.mata-kuliah.index') }}" class="rounded-lg border px-4 py-2">Batal</a>
        </div>
    </form>
@endsection
