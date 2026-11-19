<form>

    <h2 class="text-lg font-semibold mb-4">Tambah Karyawan</h2>

    <div class="grid grid-cols-2 gap-4">

        <div class="col-span-2">
            <x-admin.input label="Nama" />
        </div>

        <div class="col-span-2">
            <x-admin.input label="Email" type="email" />
        </div>

        <div class="col-span-2">
            <x-admin.input label="Password" type="password" />
        </div>

        <div class="col-span-2">
            <label class="text-sm">Divisi</label>
            <select class="w-full border rounded-lg px-3 py-2 mt-1">
                <option>IT</option>
                <option>HR</option>
                <option>Finance</option>
            </select>
        </div>

        <div class="col-span-2">
            <label class="text-sm">Status</label>
            <select class="w-full border rounded-lg px-3 py-2 mt-1">
                <option>Aktif</option>
                <option>Nonaktif</option>
            </select>
        </div>

    </div>

    <div class="flex justify-end gap-2 mt-6">
        <x-admin.button color="gray" type="button" onclick="closeModal()">Batal</x-admin.button>
        <x-admin.button color="green">Simpan</x-admin.button>
    </div>

</form>