<div 
    x-data="{
        current: 1,
        total: 2,
        init() {
            setInterval(() => {
                this.current = this.current === this.total ? 1 : this.current + 1;
            }, 6000);
        }
    }" 
    x-init="init"
    class="fixed top-0 left-0 w-full h-screen z-0"
>
    <!-- Slides -->
    <template x-for="i in total" :key="i">
        <div
            x-show="current === i"
            x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="absolute inset-0 bg-cover bg-center"
            :style="`background-image: url('/imagens/slide${i}.png')`"
        >
            <div class="w-full h-full flex items-center px-6 md:px-16 bg-gradient-to-r from-black/60 to-transparent">
                <div class="text-white max-w-xl text-left space-y-4">
                    <h2 class="text-4xl md:text-6xl font-bold leading-snug">
                        Faça parte dos<br>programas sociais de Altamira
                    </h2>
                    <p class="text-base md:text-lg font-light">
                        Cadastre-se gratuitamente e tenha acesso aos benefícios oferecidos pela prefeitura.
                    </p>
                    <a href="{{ route('register') }}" class="inline-block bg-yellow-400 text-green-900 px-6 py-3 rounded-md text-base font-semibold hover:bg-yellow-300 transition">
                        Criar Conta
                    </a>
                </div>
            </div>
        </div>
    </template>

    <!-- Botões de navegação -->
    <button 
        @click="current = current === 1 ? total : current - 1"
        class="absolute left-3 top-1/2 transform -translate-y-1/2 bg-white/40 hover:bg-white/70 text-green-900 p-2 rounded-full transition"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </button>
    <button 
        @click="current = current === total ? 1 : current + 1"
        class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-white/40 hover:bg-white/70 text-green-900 p-2 rounded-full transition"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <!-- Dots -->
    <div class="absolute bottom-5 left-1/2 transform -translate-x-1/2 flex space-x-2">
        <template x-for="i in total" :key="i">
            <div
                @click="current = i"
                :class="current === i ? 'bg-white' : 'bg-white/50'"
                class="w-3 h-1.5 rounded-full cursor-pointer transition-all"
            ></div>
        </template>
    </div>
</div>
