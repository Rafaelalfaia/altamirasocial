<header class="fixed top-0 left-0 w-full bg-green-900 text-white shadow z-50" x-data="{ open: false }">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo e Nome -->
            <div class="flex items-center space-x-3">
                <img src="{{ asset('logosistema.png') }}" alt="Logo" class="h-10 w-10 rounded-full object-cover">
                <span class="text-xl font-bold tracking-wide whitespace-nowrap">SEMAPS</span>
            </div>

            <!-- Menu Desktop -->
            <nav class="hidden md:flex items-center space-x-6 text-sm font-medium">
                <a href="#sobre" class="hover:text-yellow-300 transition">Sobre Nós</a>
                <a href="#cartao" class="hover:text-yellow-300 transition">Cartão Cidadão</a>
                <a href="#programas" class="hover:text-yellow-300 transition">Programas Sociais</a>
                <a href="{{ route('login') }}" class="hover:text-yellow-300 transition">Entrar</a>
                <a href="{{ route('register') }}" class="bg-yellow-400 text-green-900 px-4 py-2 rounded-lg font-semibold hover:bg-yellow-300 transition">Criar Conta</a>
            </nav>

            <!-- Botão Mobile -->
            <div class="md:hidden">
                <button @click="open = !open" class="text-white focus:outline-none">
                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menu Mobile -->
    <div x-show="open" x-transition class="md:hidden bg-green-800 border-t border-green-700">
        <div class="px-4 py-4 flex flex-col space-y-3 text-sm font-medium">
            <a href="#sobre" class="text-white hover:text-yellow-300 transition">Sobre Nós</a>
            <a href="#cartao" class="text-white hover:text-yellow-300 transition">Cartão Cidadão</a>
            <a href="#programas" class="text-white hover:text-yellow-300 transition">Programas Sociais</a>
            <a href="{{ route('login') }}" class="text-white hover:text-yellow-300 transition">Entrar</a>
            <a href="{{ route('register') }}" class="bg-yellow-400 text-green-900 px-4 py-2 text-center rounded-lg font-semibold hover:bg-yellow-300 transition">Criar Conta</a>
        </div>
    </div>
</header>
