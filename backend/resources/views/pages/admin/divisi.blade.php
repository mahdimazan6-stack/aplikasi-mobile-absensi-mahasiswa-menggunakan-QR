@extends('layouts.admin')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Divisi</h1>
        <p class="text-slate-500 text-sm">Mengatur Divisi</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-6">

        <div class="flex justify-between mb-4">
            <h2 class="font-semibold">List Divisi</h2>
            <x-admin.button color="blue">+ Tambah</x-admin.button>
        </div>

        <table class="w-full text-sm">

            <thead>
                <tr class="text-left border-b">
                    <th class="py-3">No</th>
                    <th class="py-3">Nama Divisi</th>
                    <th class="py-3 text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach($divisi as $index => $item)
                    <tr class="border-b hover:bg-slate-50">
                        <td class="py-3">{{ $index + 1 }}</td>
                        <td class="py-3">{{ $item['nama'] }}</td>
                        <td class="py-3 text-right space-x-2">
                            <x-admin.button color="blue">Edit</x-admin.button>
                            <x-admin.button color="red">Hapus</x-admin.button>
                        </td>
                        <x-admin.modal id="modal-edit-divisi">
                            <h2 class="text-xl font-black mb-6">Edit Divisi</h2>
                            <form id="form-edit-divisi" method="POST">
                                @csrf @method('PUT')
                                <div class="space-y-4">
                                    <input type="text" id="edit_nama_divisi" name="nama_divisi" required
                                        class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-slate-900">
                                    <button type="submit"
                                        class="w-full bg-slate-900 text-white py-3 rounded-lg font-bold">Update</button>
                                    <button type="button" onclick="closeModal('modal-edit-divisi')"
                                        class="w-full text-slate-400 font-medium hover:text-slate-600">Batal</button>
                                </div>
                            </form>
                        </x-admin.modal>

                        <script>
                            function editDivisi(id, nama_divisi) {
                                document.getElementById('form-edit-divisi').action = '/admin/divisi/' + id;
                                document.getElementById('edit_nama_divisi').value = nama_divisi;
                                openModal('modal-edit-divisi');
                            }
                        </script>
                    </tr>
                @endforeach
            </tbody>

        </table>

    </div>

@endsection