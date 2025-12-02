<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Cartão do Cidadão')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Tailwind via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-800">
    <main class="min-h-screen flex items-center justify-center p-4">
        @yield('content')
    </main>
</body>

</html>