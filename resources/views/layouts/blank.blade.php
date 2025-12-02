<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cartão do Cidadão')</title>

    {{-- Tailwind via CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css" rel="stylesheet">

    {{-- Ícone no WhatsApp/compartilhamento futuramente --}}
    <meta property="og:title" content="Cartão do Cidadão">
    <meta property="og:image" content="{{ asset('imagens/logo.png') }}">
    <meta property="og:description" content="Confira as informações do cidadão.">
    <meta name="theme-color" content="#4F46E5">
</head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />



<body class="min-h-screen bg-gray-100 flex items-center justify-center">

    @yield('content')

</body>

</html>