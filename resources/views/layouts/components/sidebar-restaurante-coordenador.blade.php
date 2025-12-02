@php
    $bgSidebar = 'bg-green-900 border-green-800';
@endphp

<div class="h-full w-64 {{ $bgSidebar }} text-white shadow-lg border-r">
    {{-- Topo --}}
    <div class="p-6 bg-green-950 border-b border-green-800 flex flex-col justify-center items-center h-24">
        <img src="{{ asset('logosistema.png') }}" alt="Logo do Sistema" class="h-12 object-contain mb-1">
        <span class="text-xs uppercase tracking-wider text-gray-300">Coordenador Restaurante</span>
    </div>

    <nav class="p-4 space-y-2">
        <ul class="space-y-1 text-sm font-medium">

            {{-- Dashboard --}}
            <li>
                <a href="{{ route('restaurante.coordenador.dashboard') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800 {{ request()->routeIs('restaurante.coordenador.dashboard') ? 'bg-green-700 font-semibold' : '' }}">
                    🏠 Dashboard
                </a>
            </li>

            {{-- Gestão --}}
            <li class="mt-4 text-xs text-gray-300 uppercase tracking-wider px-4">Gestão</li>
            <li>
                <a href="{{ route('restaurante.coordenador.restaurantes.index') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800">
                    🏢 Restaurantes
                </a>
            </li>
            <li>
                <a href="{{ route('restaurante.coordenador.atendentes.index') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800">
                    👨‍🍳 Atendentes
                </a>
            </li>

            {{-- Atendimento --}}
            <li class="mt-4 text-xs text-gray-300 uppercase tracking-wider px-4">Atendimento</li>
            <li>
                <a href="{{ route('restaurante.coordenador.cidadaos.index') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800">
                    🧑‍🤝‍🧑 Cidadãos
                </a>
            </li>
            <li>
                <a href="{{ route('restaurante.coordenador.temporarios.index') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800">
                    ⏳ Temporários
                </a>
            </li>

            {{-- Vendas --}}
            <li>
                <a href="{{ route('restaurante.coordenador.vendas.index') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800">
                    💳 Registrar Venda
                </a>
            </li>

            {{-- Relatórios --}}
            <li class="mt-4 text-xs text-gray-300 uppercase tracking-wider px-4">Relatórios</li>
            <li>
                <a href="{{ route('restaurante.coordenador.relatorios.index') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800">
                    📊 Relatórios Gerais
                </a>
            </li>
        </ul>
    </nav>

    {{-- Perfil e Logout --}}
    <div class="absolute bottom-20 left-4 w-[calc(100%-2rem)]">
        <a href="{{ route('profile.edit') }}"
           class="w-full flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 font-medium">
            ⚙️ Perfil
        </a>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="absolute bottom-6 left-4 w-[calc(100%-2rem)]">
        @csrf
        <button type="submit"
                class="w-full flex items-center gap-2 px-4 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 font-medium">
            ⏻ Sair
        </button>
    </form>
</div>
