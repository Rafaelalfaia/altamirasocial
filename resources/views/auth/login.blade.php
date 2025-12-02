<x-guest-layout>
    <div class="min-h-screen w-full flex flex-col md:flex-row">

        {{-- Lado esquerdo: Formulário --}}
        <div class="w-full md:w-1/2 flex flex-col justify-center items-center bg-white px-6 py-10">
            {{-- Logo --}}
            <div class="mb-6">
                <img src="{{ asset('logosistema1.png') }}" alt="Logo do Sistema" class="h-20 drop-shadow-md">
            </div>

            {{-- Caixa de login --}}
            <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg border border-gray-200">
                <h1 class="text-2xl font-bold text-center text-gray-800 mb-4">Entrar</h1>
                <p class="text-center text-gray-500 mb-6 text-sm">Digite seu e-mail ou CPF e senha para entrar</p>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5" id="login-form">
                    @csrf

                    {{-- Alternador --}}
                    <div>
                        <x-input-label :value="__('Entrar com:')" />
                        <div class="flex gap-4 mt-1 text-sm text-gray-600">
                            <label class="flex items-center">
                                <input type="radio" name="login_type" value="cpf" checked onclick="toggleLoginField('cpf')" class="accent-green-700">
                                <span class="ml-2">CPF</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="login_type" value="email" onclick="toggleLoginField('email')" class="accent-green-700">
                                <span class="ml-2">E-mail</span>
                            </label>
                        </div>
                    </div>

                    {{-- CPF --}}
                    <div id="cpf-field">
                        <x-input-label for="cpf" :value="__('CPF')" />
                        <x-text-input id="cpf" class="block mt-1 w-full" type="text" name="login" value="{{ old('login') }}" maxlength="14" oninput="formatarCPF(this)" autofocus />
                        <x-input-error :messages="$errors->get('login')" class="mt-2" />
                    </div>

                    {{-- Email --}}
                    <div id="email-field" class="hidden">
                        <x-input-label for="email" :value="__('E-mail')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" value="{{ old('login') }}" />
                        <x-input-error :messages="$errors->get('login')" class="mt-2" />
                    </div>

                    {{-- Senha --}}
                    <div>
                        <x-input-label for="password" :value="__('Senha')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    {{-- Opções --}}
                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center text-sm text-gray-700">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-green-800 shadow-sm focus:ring-green-700" name="remember">
                            <span class="ml-2">Lembrar de mim</span>
                        </label>
                        <a class="text-sm text-green-800 hover:underline transition" href="{{ route('cidadao.recuperar.form') }}">
                            Esqueceu a senha?
                        </a>
                        
                        
                    </div>

                    {{-- Botão --}}
                    <x-primary-button class="w-full bg-green-800 hover:bg-green-900 text-white font-semibold py-2 rounded-md shadow-md text-base">
                        Entrar
                    </x-primary-button>

                    {{-- Link para registro --}}
                    <p class="text-center text-sm text-gray-600">
                        Não tem uma conta?
                        <a href="{{ route('register') }}" class="text-green-800 hover:underline">Inscrição</a>
                    </p>
                </form>
            </div>
        </div>

        {{-- Lado direito: Imagem --}}
        <div class="hidden md:flex md:w-1/2 bg-cover bg-center relative" style="background-image: url('{{ asset('imagens/login.png') }}')">
            <div class="absolute inset-0 bg-green-950 bg-opacity-70 flex items-center justify-center p-10 text-center text-white">
               
            </div>
        </div>
    </div>

    <script>
        function toggleLoginField(type) {
            const cpfField = document.getElementById('cpf-field');
            const emailField = document.getElementById('email-field');
            const cpfInput = document.getElementById('cpf');
            const emailInput = document.getElementById('email');

            cpfInput.removeAttribute('name');
            emailInput.removeAttribute('name');

            if (type === 'cpf') {
                cpfField.classList.remove('hidden');
                emailField.classList.add('hidden');
                cpfInput.setAttribute('name', 'login');
            } else {
                cpfField.classList.add('hidden');
                emailField.classList.remove('hidden');
                emailInput.setAttribute('name', 'login');
            }
        }

        function formatarCPF(campo) {
            let valor = campo.value.replace(/\D/g, '');
            if (valor.length > 11) valor = valor.slice(0, 11);
            valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
            valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
            valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            campo.value = valor;
        }

        window.onload = () => toggleLoginField('cpf');
    </script>
</x-guest-layout>
