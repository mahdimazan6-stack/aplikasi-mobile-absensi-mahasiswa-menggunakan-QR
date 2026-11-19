<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-slate-100 text-slate-800">

    <div class="flex">

        <x-admin.sidebar />

        <div class="flex-1 min-h-screen">

            <x-admin.navbar />

            <main class="p-8">
                @yield('content')
            </main>

        </div>

    </div>
    <script>
        function openModal() {
            document.getElementById('modal-karyawan').classList.remove('hidden');
            document.getElementById('modal-karyawan').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('modal-karyawan').classList.add('hidden');
            document.getElementById('modal-karyawan').classList.remove('flex');
        }
    </script>

</body>

</html>