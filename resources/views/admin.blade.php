<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/main.js'])
    <title>Главная</title>
</head>

<body class="flex flex-col min-h-screen">
    @include('templates.header')
    <main class="main flex grow container mx-auto gap-6">
        @include('templates.nav')
        <div class="content flex w-full">
            @yield('main')
        </div>
    </main>
    @include('templates.footer')
</body>

</html>
