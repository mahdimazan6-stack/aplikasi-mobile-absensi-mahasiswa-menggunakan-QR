@extends('layouts.admin')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Karyawan</h1>
        <p class="text-slate-500 text-sm">Manage employee data</p>
    </div>

    <div class="bg-white p-6 rounded-2xl border shadow-sm">

        <!-- Search + Action -->
        <div class="flex justify-between mb-4">
            <input type="text" placeholder="Cari karyawan..."
                class="border px-4 py-2 rounded-lg w-1/3 focus:outline-none focus:ring-2 focus:ring-slate-300">

            <x-admin.button color="blue" onclick="openModal('modal-karyawan')">
                + Tambah
            </x-admin.button>
        </div>

        <!-- Table -->
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left">
                    <th class="py-3">Nama</th>
                    <th class="py-3">Divisi</th>
                    <th class="py-3">Status</th>
                    <th class="py-3 text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach($karyawan as $item)
                    <tr class="border-b hover:bg-slate-50">
                        <td class="py-3">{{ $item['nama'] }}</td>
                        <td class="py-3">{{ $item['divisi'] }}</td>
                        <td class="py-3">{{ $item['status'] }}</td>
                        <td class="py-3 text-right space-x-2">
                            <x-admin.button color="blue">Edit</x-admin.button>
                            <x-admin.button color="red">Hapus</x-admin.button>
                        </td>


                        {{-- TARUH MODAL INI DI BAWAH MODAL TAMBAH KARYAWAN LU --}}

                        <x-admin.modal id="modal-edit-karyawan">
                            <h2 class="text-xl font-black mb-6">Edit Data Karyawan</h2>
                            <form id="form-edit-karyawan" method="POST">
                                @csrf @method('PUT')
                                <div class="space-y-4">
                                    <input type="text" id="edit_name" name="name" required
                                        class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-slate-900">
                                    <input type="email" id="edit_email" name="email" required
                                        class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-slate-900">

                                    <select id="edit_divisi" name="divisi_id" required
                                        class="w-full border rounded-lg p-3 bg-white outline-none focus:ring-2 focus:ring-slate-900">
                                        @foreach($divisis as $d)
                                            <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>
                                        @endforeach
                                    </select>

                                    <button type="submit"
                                        class="w-full bg-slate-900 text-white py-3 rounded-lg font-bold">Update Data</button>
                                    <button type="button" onclick="closeModal('modal-edit-karyawan')"
                                        class="w-full text-slate-400 font-medium hover:text-slate-600">Batal</button>
                                </div>
                            </form>
                        </x-admin.modal>

                        <x-admin.modal id="modal-password">
                            <h2 class="text-xl font-black mb-2">Reset Password</h2>
                            <p id="nama_user_password" class="text-sm text-slate-500 mb-6 font-medium"></p>

                            <form id="form-password" method="POST">
                                @csrf @method('PUT')
                                <div class="space-y-4">
                                    <input type="password" name="password" placeholder="Masukkan password baru" required
                                        class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-slate-900">
                                    <button type="submit"
                                        class="w-full bg-amber-500 hover:bg-amber-600 text-white py-3 rounded-lg font-bold">Simpan
                                        Password Baru</button>
                                    <button type="button" onclick="closeModal('modal-password')"
                                        class="w-full text-slate-400 font-medium hover:text-slate-600">Batal</button>
                                </div>
                            </form>
                        </x-admin.modal>

                        <script>
                            function editKaryawan(id, name, email, divisi_id) {
                                document.getElementById('form-edit-karyawan').action = '/admin/karyawan/' + id;
                                document.getElementById('edit_name').value = name;
                                document.getElementById('edit_email').value = email;
                                document.getElementById('edit_divisi').value = divisi_id;
                                openModal('modal-edit-karyawan');
                            }

                            function editPassword(id, name) {
                                document.getElementById('form-password').action = '/admin/karyawan/' + id + '/reset-password';
                                document.getElementById('nama_user_password').innerText = 'Akun: ' + name;
                                openModal('modal-password');
                            }
                        </script>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    <!-- 🔥 MODAL -->
    <x-admin.modal id="modal-karyawan">
        <x-admin.form-karyawan />
    </x-admin.modal>

@endsection