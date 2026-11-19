<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>

    @vite('resources/css/app.css')
</head>

<body class="bg-slate-50">

    <div class="flex">

        {{-- SIDEBAR --}}   
        <x-user.sidebar />
        {{-- MAIN --}}
        <div class="flex-1">

            {{-- NAVBAR --}}
            <x-user.navbar />
            {{-- CONTENT --}}
            <main class="p-8 min-h-screen">
                @yield('content')
            </main>

        </div>

    </div>

</body>

</html>