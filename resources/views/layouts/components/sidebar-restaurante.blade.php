<body x-data="{
    open: window.innerWidth >= 1024,
    handleResize() {
        this.open = window.innerWidth >= 1024;
    }
}"
x-init="handleResize(); window.addEventListener('resize', () => handleResize())"
class="bg-gray-100 text-gray-800 min-h-screen font-sans antialiased">

{{-- Botão Hamburguer --}}
<button @click="open = !open"
    class="md:hidden fixed top-4 left-4 z-50 p-2 bg-green-900 text-white rounded-lg shadow-lg focus:outline-none">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
         viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>

{{-- Sidebar --}}
<aside x-show="open"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-300 transform"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    x-cloak
    class="fixed top-0 left-0 h-full w-64 bg-green-900 text-white shadow-xl border-r border-green-800 z-40 transform"
    @click.outside="if (window.innerWidth < 768) open = false">

    {{-- Logo --}}
    <div class="p-6 bg-green-950 border-b border-green-800 flex justify-center items-center h-24">
        <img src="{{ asset('logosistema.png') }}" alt="Logo do Sistema" class="h-16 object-contain">
    </div>

    {{-- Menu de Navegação --}}
    <nav class="p-4 space-y-2 overflow-y-auto h-[calc(100%-12rem)]">
        <ul class="space-y-1 text-sm font-medium">
            <li>
                <a href="{{ route('restaurante.dashboard') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800 {{ request()->routeIs('restaurante.dashboard') ? 'bg-green-700 font-semibold' : '' }}">
                    🏠 Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('restaurante.vendas.index') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800 {{ request()->routeIs('restaurante.vendas.*') ? 'bg-green-700 font-semibold' : '' }}">
                    🍛 Vendas do Dia
                </a>
            </li>
                            
            <li>
                <a href="{{ route('restaurante.cidadaos-temporarios.index') }}"
                class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800 {{ request()->routeIs('restaurante.cidadaos-temporarios.*') ? 'bg-green-700 font-semibold' : '' }}">
                👥 Cartão Temporário
                </a>

            </li>
            <li>
                <a href="{{ route('restaurante.relatorios.index') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-green-800 {{ request()->routeIs('restaurante.relatorios.*') ? 'bg-green-700 font-semibold' : '' }}">
                    📊 Relatórios
                </a>
            </li>
        </ul>
    </nav>

    {{-- Perfil --}}
    <div class="absolute bottom-20 left-4 w-[calc(100%-2rem)]">
        <a href="{{ route('profile.edit') }}"
           class="w-full flex items-center gap-2 px-4 py-2 rounded-lg bg-green-800 text-white hover:bg-green-700 font-medium">
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
</aside>
