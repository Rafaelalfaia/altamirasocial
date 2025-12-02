@php
    $bgSidebar = 'bg-green-900 border-green-800';
@endphp

<div class="h-full w-64 {{ $bgSidebar }} text-white shadow-2xl border-r">

    {{-- Logo --}}
    <div class="p-6 bg-green-950 border-b border-green-800 flex justify-center items-center h-24">
        <img src="{{ asset('logosistema.png') }}" alt="Logo do Sistema" class="h-16 object-contain">
    </div>

    {{-- Navegação --}}
    <nav class="p-4 space-y-4 text-sm">
        <ul class="space-y-1 font-medium">
            <li>
                <a href="{{ route('coordenador.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-800 hover:pl-5 transition-all {{ request()->routeIs('coordenador.dashboard') ? 'bg-green-700 font-semibold' : '' }}">
                    🏠 Dashboard
                </a>
            </li>

            <li>
                <a href="{{ route('coordenador.cidadaos.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-800 hover:pl-5 transition-all {{ request()->routeIs('coordenador.cidadaos.*') ? 'bg-green-700 font-semibold' : '' }}">
                    👥 Cidadãos
                </a>
            </li>

            <li>
                <a href="{{ route('coordenador.assistentes.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-800 hover:pl-5 transition-all {{ request()->routeIs('coordenador.assistentes.*') ? 'bg-green-700 font-semibold' : '' }}">
                    👨🏻‍⚕️ Equipe Técnica
                </a>
            </li>

            <li>
                <a href="{{ route('coordenador.moradia.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-800 hover:pl-5 transition-all {{ request()->routeIs('coordenador.moradia.*') ? 'bg-green-700 font-semibold' : '' }}">
                    🏘️ Bairros Cadastrados
                </a>
            </li>

            <li>
                <a href="{{ route('coordenador.programas.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-800 hover:pl-5 transition-all {{ request()->routeIs('coordenador.programas.*') ? 'bg-green-700 font-semibold' : '' }}">
                    📋 Programas Sociais
                </a>
            </li>

            <li>
                <a href="{{ route('coordenador.analises.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-800 hover:pl-5 transition-all {{ request()->routeIs('coordenador.analises.*') ? 'bg-green-700 font-semibold' : '' }}">
                    🕵️ Indicações & Denúncias
                </a>
            </li>

            <li>
                <a href="{{ route('coordenador.recebimentos.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-800 hover:pl-5 transition-all {{ request()->routeIs('coordenador.recebimentos.*') ? 'bg-green-700 font-semibold' : '' }}">
                    🔄 Recebimentos & Encaminhamentos
                </a>
            </li>

            <li>
                <a href="{{ route('coordenador.solicitacoes.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-800 hover:pl-5 transition-all {{ request()->routeIs('coordenador.solicitacoes.*') ? 'bg-green-700 font-semibold' : '' }}">
                    📤 Solicitações
                </a>
            </li>

            <li>
                <a href="{{ route('coordenador.lotes.index') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-800 hover:pl-5 transition-all {{ request()->routeIs('coordenador.lotes.*') ? 'bg-green-700 font-semibold' : '' }}">
                    💰 Lotes de Pagamento
                </a>
            </li>

            <li>
                <a href="{{ route('coordenador.relatorios.index') }}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-800 hover:pl-5 transition-all">
                    📑 Relatórios
                </a>
            </li>
        </ul>
    </nav>

    {{-- Perfil e Logout --}}
    <div class="absolute bottom-20 left-4 w-[calc(100%-2rem)]">
        <a href="{{ route('profile.edit') }}"
            class="w-full flex items-center gap-2 px-4 py-2 rounded-lg bg-green-100 text-green-900 hover:bg-green-200 font-medium transition">
            ⚙️ Perfil
        </a>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="absolute bottom-6 left-4 w-[calc(100%-2rem)]">
        @csrf
        <button type="submit"
            class="w-full flex items-center gap-2 px-4 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 font-medium transition">
            ⏻ Sair
        </button>
    </form>
</div>
