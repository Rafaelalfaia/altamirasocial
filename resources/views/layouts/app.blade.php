<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('Sistema e App', config('app.name', 'SEMAPS'))</title>

    {{-- Fontes --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Estilos e Scripts via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js e Chart.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">


    {{-- Estilo para x-cloak --}}
    <style>[x-cloak] { display: none !important; }</style>

    {{-- Script para formulário dinâmico (estados/cidades) --}}
    @isset($estados)
    <script>
        function bairrosForm() {
            return {
                estados: @json($estados),
                estadoSelecionado: '',
                cidadesFiltradas() {
                    const estado = this.estados.find(e => e.id == this.estadoSelecionado);
                    return estado ? estado.cidades : [];
                }
            };
        }
    </script>
    @endisset

</head>

<style>
  :root { --vh: 1vh; }
  /* Utilitários universais baseados em viewport real */
  .min-h-mobile { min-height: calc(var(--vh) * 100) !important; }
  .h-mobile     { height:     calc(var(--vh) * 100) !important; }
</style>
<script>
  // mede a viewport real (área visível) e grava em --vh
  function setVh() {
    document.documentElement.style.setProperty('--vh', (window.innerHeight * 0.01) + 'px');
  }
  window.addEventListener('resize', setVh);
  window.addEventListener('orientationchange', setVh);
  setVh();
</script>


<body
    x-data="{
        sidebarOpen: window.innerWidth >= 1024,
        handleResize() {
            this.sidebarOpen = window.innerWidth >= 1024;
        }
    }"
    x-init="() => {
        handleResize();
        window.addEventListener('resize', handleResize);
    }"
    class="bg-gray-100 text-gray-800 min-h-mobile font-sans antialiased"
>

@php
    use App\Models\ModoPlantao;
    $modoPlantao = Auth::check() && Auth::user()->hasRole('Assistente')
        ? ModoPlantao::where('user_id', Auth::id())->first()
        : null;
    $plantaoAtivo = $modoPlantao && $modoPlantao->ativo;
@endphp

    {{-- Botão Hamburguer --}}
    <button @click="sidebarOpen = !sidebarOpen"
        class="lg:hidden fixed top-4 left-4 z-50 p-2 {{ $plantaoAtivo ? 'bg-black' : 'bg-green-900' }} text-white rounded shadow focus:outline-none">
        ☰
    </button>

    {{-- Sidebar --}}
    <aside
        x-show="sidebarOpen"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-300 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        x-cloak
        class="fixed top-0 left-0 w-64 h-mobile z-40 shadow-lg transform text-white lg:border-r overflow-y-auto {{ $plantaoAtivo ? 'bg-black border-black' : 'bg-green-900 border-green-800' }}"

    >
        {{-- Conteúdo dinâmico da sidebar --}}
        @auth
            @switch(Auth::user()->getRoleNames()->first())
                @case('Admin')
                    @include('layouts.components.sidebar')
                    @break
                @case('Coordenador')
                    @include('layouts.components.sidebar-coordenador')
                    @break
                @case('Assistente')
                    @include('layouts.components.sidebar-assistente', ['plantaoAtivo' => $plantaoAtivo])
                    @break
                @case('Cidadao')
                    @include('layouts.components.sidebar-cidadao')
                    @break
                @case('Restaurante')
                    @include('layouts.components.sidebar-restaurante')
                    @break
                    @case('Coordenador Restaurante')
                @include('layouts.components.sidebar-restaurante-coordenador')
                @break

                @case('Atendente Restaurante')
                    @include('layouts.components.sidebar-restaurante-atendente')
                    @break
                @endswitch
        @endauth
    </aside>

    {{-- Conteúdo Principal --}}

    <div class="flex min-h-mobile">
        <main :class="{ 'ml-64': sidebarOpen }" class="flex-1 p-6 transition-all">

            {{-- Botão de retorno do Coordenador --}}
            @if (session()->has('impersonate_coordenador_id') && !session()->has('impersonate_admin_id'))
            <div class="mb-6">
                <a href="{{ route('voltar.coordenador') }}"
                class="inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                    ⬅ Voltar ao Coordenador
                </a>
            </div>
            @endif

            {{-- Botão de retorno ao Admin (universal) --}}
            @if (session()->has('impersonate_admin_id'))
            <div class="mb-6">
                <a href="{{ route('voltar.admin') }}"
                class="inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                    ⬅ Voltar ao Admin
                </a>
            </div>
            @endif

            {{-- Conteúdo da Página --}}
            @yield('content')
        </main>
    </div>






    {{-- Scripts adicionais --}}
    @yield('scripts')
    @stack('scripts')

</body>
</html>
