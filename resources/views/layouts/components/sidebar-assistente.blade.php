@php
    $plantaoAtivo = $plantaoAtivo ?? false;
    $bgSidebar = $plantaoAtivo ? 'bg-black border-black' : 'bg-green-900 border-green-800';
    $bgHover = $plantaoAtivo ? 'hover:bg-gray-800' : 'hover:bg-green-800';
    $bgActive = $plantaoAtivo ? 'bg-gray-900' : 'bg-green-700';
@endphp

<div class="h-full w-64 {{ $bgSidebar }} text-white shadow-xl border-r">

    {{-- Topo / Logo --}}
    <div class="p-6 bg-green-950 border-b border-green-800 flex justify-center items-center h-24">
        <img src="{{ asset('logosistema.png') }}" alt="Logo do Sistema" class="h-16 object-contain">
    </div>

    {{-- Navegação --}}
    <nav class="p-4 space-y-2 overflow-y-auto h-[calc(100%-12rem)]">
        <ul class="space-y-1 text-sm font-medium">
            <li>
                <a href="{{ route('assistente.dashboard') }}"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg {{ $bgHover }} {{ request()->routeIs('assistente.dashboard') ? $bgActive . ' font-semibold' : '' }}">
                    🏠 Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('assistente.usuarios.index') }}"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg {{ $bgHover }} {{ request()->routeIs('assistente.usuarios.*') ? $bgActive . ' font-semibold' : '' }}">
                    👥 Cidadãos
                </a>
            </li>
            <li>
                <a href="{{ route('assistente.programas.index') }}"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg {{ $bgHover }} {{ request()->routeIs('assistente.programas.*') ? $bgActive . ' font-semibold' : '' }}">
                    📋 Programas Sociais
                </a>
            </li>
            <li>
                <a href="{{ route('assistente.solicitacoes.index') }}"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg {{ $bgHover }} {{ request()->routeIs('assistente.solicitacoes.*') ? $bgActive . ' font-semibold' : '' }}">
                    📥 Solicitações SEMAPS
                </a>
            </li>
            <li>
                <a href="{{ route('assistente.acompanhamentos.index') }}"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg {{ $bgHover }} {{ request()->routeIs('assistente.acompanhamentos.*') ? $bgActive . ' font-semibold' : '' }}">
                    📌 Acompanhamentos
                </a>
            </li>

            <li>
                <a href="{{ route('assistente.relatorios.index') }}"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800 {{ request()->routeIs('assistente.relatorios.index') ? 'bg-green-900 text-white font-semibold' : 'text-white' }}">
                    📄 Relatórios
                </a>

            </li>
        </ul>
    </nav>

    {{-- Perfil --}}
    <div class="absolute bottom-20 left-4 w-[calc(100%-2rem)]">
        <a href="{{ route('profile.edit') }}"
            class="w-full flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 font-medium">
            ⚙️ Perfil
        </a>
    </div>

    {{-- Sair --}}
    <form method="POST" action="{{ route('logout') }}" class="absolute bottom-6 left-4 w-[calc(100%-2rem)]">
        @csrf
        <button type="submit"
            class="w-full flex items-center gap-2 px-4 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 font-medium">
            ⏻ Sair
        </button>
    </form>
</div>
