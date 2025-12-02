<x-guest-layout>
    <div class="min-h-screen w-full flex flex-col md:flex-row">

        {{-- Lado esquerdo: Formulário --}}
        <div class="w-full md:w-1/2 flex flex-col justify-center items-center bg-white px-6 py-10">
            {{-- Logo --}}
            <div class="mb-6">
                <img src="{{ asset('logosistema1.png') }}" alt="Logo do Sistema" class="h-20 drop-shadow-md">
            </div>

            {{-- Caixa de registro --}}
            <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg border border-gray-200">
                <h1 class="text-2xl font-bold text-center text-gray-800 mb-4">Criar Conta</h1>
                <p class="text-center text-gray-500 mb-6 text-sm">Preencha os dados abaixo para se inscrever</p>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    {{-- Nome --}}
                    <div>
                        <x-input-label for="name" :value="__('Nome completo')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                      :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    {{-- Alternador --}}
                    <div>
                        <x-input-label :value="__('Deseja criar conta com:')" />
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
                        <x-text-input id="cpf" class="block mt-1 w-full" type="text" name="cpf"
                                      :value="old('cpf')" maxlength="14" oninput="formatarCPF(this)" />
                        <x-input-error :messages="$errors->get('cpf')" class="mt-2" />
                    </div>

                    {{-- Email --}}
                    <div id="email-field" class="hidden">
                        <x-input-label for="email" :value="__('E-mail')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                      :value="old('email')" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Senha --}}
                    <div>
                        <x-input-label for="password" :value="__('Senha')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                                      required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    {{-- Confirmação de Senha --}}
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirmar senha')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                                      name="password_confirmation" required />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    {{-- Botão --}}
                    <x-primary-button class="w-full bg-green-800 hover:bg-green-900 text-white font-semibold py-2 rounded-md shadow-md text-base">
                        Criar Conta
                    </x-primary-button>

                    {{-- Link para login --}}
                    <p class="text-center text-sm text-gray-600">
                        Já possui uma conta?
                        <a href="{{ route('login') }}" class="text-green-800 hover:underline">Entrar</a>
                    </p>
                </form>
            </div>
        </div>

        {{-- Lado direito: Imagem --}}
        <div class="hidden md:flex md:w-1/2 bg-cover bg-center relative" style="background-image: url('{{ asset('imagens/login.png') }}')">
            <div class="absolute inset-0 bg-green-950 bg-opacity-70 flex items-center justify-center p-10 text-center text-white">
                {{-- Espaço para frases ou texto de boas-vindas, se desejar --}}
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
                cpfInput.setAttribute('name', 'cpf');
            } else {
                cpfField.classList.add('hidden');
                emailField.classList.remove('hidden');
                emailInput.setAttribute('name', 'email');
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
