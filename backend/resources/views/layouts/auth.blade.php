<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-slate-100 flex items-center justify-center min-h-screen">

    @yield('content')

</body>

</html>