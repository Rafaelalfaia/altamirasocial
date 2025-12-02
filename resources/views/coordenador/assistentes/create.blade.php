@extends('layouts.app')

@section('title', 'Cadastrar Assistente Social')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold text-green-700 mb-6">👩‍⚕️ Novo Assistente Social</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('coordenador.assistentes.store') }}" class="space-y-4">
            @csrf

            {{-- Nome --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nome completo *</label>
                <input type="text" name="name" id="name" required
                    value="{{ old('name') }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200 text-sm">
            </div>

            {{-- CPF --}}
            <div>
                <label for="cpf" class="block text-sm font-medium text-gray-700">CPF *</label>
                <input type="text" name="cpf" id="cpf" required maxlength="14"
                    value="{{ old('cpf') }}"
                    oninput="this.value = this.value.replace(/\D/g,'').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d{1,2})$/,'$1-$2')"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200 text-sm">
            </div>

            {{-- Telefone --}}
            <div>
                <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                <input type="text" name="telefone" id="telefone" maxlength="15"
                    value="{{ old('telefone') }}"
                    oninput="this.value = this.value.replace(/\D/g,'').replace(/^(\d{2})(\d)/,'($1) $2').replace(/(\d{4,5})(\d{4})$/,'$1-$2')"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200 text-sm">
            </div>

            {{-- E-mail (opcional) --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">E-mail (opcional)</label>
                <input type="email" name="email" id="email"
                    value="{{ old('email') }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200 text-sm">
            </div>

            {{-- Senha --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Senha *</label>
                    <input type="password" name="password" id="password" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200 text-sm">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirme a senha *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200 text-sm">
                </div>
            </div>

            {{-- Botão --}}
            <div class="flex justify-end mt-6">
                <button type="submit"
                    class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition text-sm">
                    💾 Salvar Assistente
                </button>
            </div>
        </form>
    </div>
@endsection
