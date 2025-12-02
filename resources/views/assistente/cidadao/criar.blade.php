@extends('layouts.app')

@section('title', 'Novo Cidadão')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold text-green-700 mb-6">👤 Criar Novo Cidadão</h1>

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('assistente.cidadao.salvar') }}" method="POST" onsubmit="return validarCPF()">
            @csrf

            <div class="mb-4">
                <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                <input type="text" name="nome" id="nome" value="{{ old('nome') }}"
                       class="w-full border rounded px-3 py-2 mt-1" required>
            </div>

            <div class="mb-4">
                <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                <input type="text" name="cpf" id="cpf" value="{{ old('cpf') }}" class="w-full border rounded px-3 py-2 mt-1"
                       maxlength="14" required oninput="mascararCPF(this)">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                       class="w-full border rounded px-3 py-2 mt-1">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2 mt-1" required>
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                       class="w-full border rounded px-3 py-2 mt-1" required>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                    💾 Criar Cidadão
                </button>
            </div>
        </form>
    </div>

    {{-- Scripts de máscara e validação --}}
    <script>
        function mascararCPF(input) {
            let valor = input.value.replace(/\D/g, '');
            if (valor.length > 11) valor = valor.slice(0, 11);
            input.value = valor.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, function (_, p1, p2, p3, p4) {
                return `${p1}.${p2}.${p3}${p4 ? '-' + p4 : ''}`;
            });
        }

        function validarCPF() {
            let cpf = document.getElementById('cpf').value.replace(/\D/g, '');
            if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) {
                alert('CPF inválido. Verifique e tente novamente.');
                return false;
            }

            let soma = 0;
            for (let i = 0; i < 9; i++) soma += parseInt(cpf.charAt(i)) * (10 - i);
            let digito1 = (soma * 10) % 11;
            if (digito1 === 10 || digito1 === 11) digito1 = 0;
            if (digito1 !== parseInt(cpf.charAt(9))) {
                alert('CPF inválido. Verifique e tente novamente.');
                return false;
            }

            soma = 0;
            for (let i = 0; i < 10; i++) soma += parseInt(cpf.charAt(i)) * (11 - i);
            let digito2 = (soma * 10) % 11;
            if (digito2 === 10 || digito2 === 11) digito2 = 0;
            if (digito2 !== parseInt(cpf.charAt(10))) {
                alert('CPF inválido. Verifique e tente novamente.');
                return false;
            }

            return true;
        }
    </script>
@endsection
