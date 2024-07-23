<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/sass/main.scss', 'resources/js/app.js', 'resources/js/main.js'])
    <title>Главная</title>
</head>

<body class="d-flex flex-column min-vh-100">
    @include('templates.header')
    <main class="main d-flex flex-grow-1">
        @include('templates.nav')
        <div class="content col-10 bg-light">
            @yield('main')
        </div>
    </main>
    @include('templates.footer')
</body>

</html>
