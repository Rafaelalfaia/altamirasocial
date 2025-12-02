@php
    $bgSidebar = 'bg-green-900 border-green-800';
@endphp

<div class="h-full w-64 {{ $bgSidebar }} text-white shadow-lg border-r">

    {{-- Topo com logo --}}
    <div class="p-6 bg-green-950 border-b border-green-800 flex justify-center items-center h-24">
        <img src="{{ asset('logosistema.png') }}" alt="Logo do Sistema" class="h-16 object-contain">
    </div>

    {{-- Navegação --}}
    <nav class="p-4 space-y-2">
        <ul class="space-y-1 text-sm font-medium">

            {{-- Dashboard --}}
            <li>
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800 {{ request()->routeIs('admin.dashboard') ? 'bg-green-700 font-semibold' : '' }}">
                    🏠 Dashboard
                </a>
            </li>

            {{-- Gerenciar Usuários --}}
            <li>
                <a href="{{ route('admin.usuarios.index') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800 {{ request()->routeIs('admin.usuarios.*') ? 'bg-green-700 font-semibold' : '' }}">
                    👥 Gerenciar Usuários
                </a>
            </li>

            {{-- Perfis do Sistema --}}
            <li class="mt-4 text-xs text-gray-300 uppercase tracking-wider px-4">Perfis</li>
            <li>
                <a href="{{ route('admin.cidadaos') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800">
                    🧑‍🤝‍🧑 Cidadãos
                </a>
            </li>
            <li>
                <a href="{{ route('admin.assistentes') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800">
                    🤝 Assistentes
                </a>
            </li>
            <li>
                <a href="{{ route('admin.coordenadores.index') }}"

                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800">
                    🧑‍💼 Coordenadores
                </a>
            </li>

            {{-- Programas e Inscrições --}}
            <li class="mt-4 text-xs text-gray-300 uppercase tracking-wider px-4">Programas</li>
            <li>
                 <a href="{{ route('admin.programas.index') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800">
                    📦 Programas Sociais
                </a>
            </li>

           <li>
                <a href="{{ route('admin.cidadaos-temporarios.index') }}"
                class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800 {{ request()->routeIs('admin.cidadaos-temporarios.*') ? 'bg-green-700 font-semibold' : '' }}">
                    🕒 Cidadãos Temporários
                </a>
            </li>


            {{-- Restaurante Popular --}}
            <li class="mt-4 text-xs text-gray-300 uppercase tracking-wider px-4">Restaurante</li>
            <li>
                <a href="{{ route('admin.restaurante.index') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800">
                    🍽️ Vendas Restaurante
                </a>
            </li>

            {{-- Relatórios --}}
            <li class="mt-4 text-xs text-gray-300 uppercase tracking-wider px-4">Relatórios</li>
            <li>
                 <a href="{{ route('admin.relatorios.index') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800">
                    📊 Relatórios Gerais
                </a>
            </li>

        </ul>
    </nav>

    {{-- Botão de Perfil --}}
    <div class="absolute bottom-20 left-4 w-[calc(100%-2rem)]">
        <a href="{{ route('profile.edit') }}"
            class="w-full flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 font-medium">
            ⚙️ Perfil
        </a>
    </div>

    {{-- Botão de Sair --}}
    <form method="POST" action="{{ route('logout') }}" class="absolute bottom-6 left-4 w-[calc(100%-2rem)]">
        @csrf
        <button type="submit"
            class="w-full flex items-center gap-2 px-4 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 font-medium">
            ⏻ Sair
        </button>
    </form>
</div>
