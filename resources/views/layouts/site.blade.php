<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

<title>@yield('title', 'SEMAPS')</title>

    {{-- Favicons --}}

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('imagens/logosistema.png') }}">

    <meta name="theme-color" content="#15803d"> {{-- verde da sua identidade --}}

    {{-- Estilos externos --}}
    @stack('styles')
</head>
<body class="antialiased">

    {{-- Conteúdo da página --}}
    @yield('content')

    {{-- Scripts finais --}}
    @stack('scripts')
</body>
</html>
