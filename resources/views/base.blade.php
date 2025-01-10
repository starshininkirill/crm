<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Главная</title>
</head>

<body id="app" class="flex flex-col min-h-screen m-0">
    @include('templates.header')
    <main class="main flex grow container mx-auto gap-6">
        <div class="content flex w-full">
            @yield('main')
        </div>
    </main>
    @include('templates.footer')
</body>

</html>
