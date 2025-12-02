@extends('layouts.app')

@section('title', 'Alterar Senha')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow-md">
        {{-- Título --}}
        <h1 class="text-2xl font-bold text-yellow-600 mb-6 flex items-center gap-2">
            🔒 Alterar Senha do Cidadão
        </h1>

        {{-- Mensagem de sucesso --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Mensagens de erro --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-2 rounded mb-4 text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulário --}}
        <form action="{{ route('assistente.cidadao.senha.atualizar', $usuario->id) }}" method="POST" class="space-y-6">
            @csrf

            {{-- Linha 1: CPF e E-mail --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- CPF --}}
                <div>
                    <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                    <input type="text" name="cpf" id="cpf" value="{{ old('cpf', $cidadao->user->cpf ?? $cidadao->cpf) }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500 text-gray-800">
                </div>

                {{-- E-mail --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">E-mail (opcional)</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $cidadao->user->email ?? '') }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500">
                </div>
            </div>

            {{-- Linha 2: Senha e confirmação --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nova senha --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Nova Senha</label>
                    <input type="password" name="password" id="password"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500"
                        required>
                </div>

                {{-- Confirmar senha --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Nova
                        Senha</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500"
                        required>
                </div>
            </div>

            {{-- Botão --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-yellow-600 text-white px-6 py-2 rounded-md hover:bg-yellow-700 transition font-medium shadow-sm">
                    🔐 Atualizar Senha
                </button>
            </div>
        </form>
    </div>
@endsection