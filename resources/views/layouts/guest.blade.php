<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Fontes --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- CSS e JS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-green-900 font-sans antialiased text-gray-900 m-0 p-0">
    <div class="w-screen h-screen flex items-center justify-center">
        {{ $slot }}
    </div>
</body>

</html>