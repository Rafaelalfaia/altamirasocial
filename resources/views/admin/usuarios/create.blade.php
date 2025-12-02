@extends('layouts.app')

@section('title', 'Criar Usuário')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-green-800 mb-6">➕ Novo Usuário</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-2 mb-4 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.usuarios.store') }}" method="POST" class="grid gap-4">
        @csrf

        {{-- Nome --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Nome</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full border-gray-300 rounded shadow text-sm mt-1">
        </div>

        {{-- E-mail --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full border-gray-300 rounded shadow text-sm mt-1">
        </div>

        {{-- CPF --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">CPF</label>
            <input
                type="text"
                name="cpf"
                value="{{ old('cpf') }}"
                required
                inputmode="numeric"
                autocomplete="off"
                placeholder="000.000.000-00 ou 00000000000"
                class="w-full border-gray-300 rounded shadow text-sm mt-1"
            >
            <p class="text-xs text-gray-500 mt-1">
                Dica: pode digitar com ou sem pontuação. O sistema salva só os números.
            </p>
        </div>


        {{-- Senha --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Senha</label>
            <input type="password" name="password" required
                   class="w-full border-gray-300 rounded shadow text-sm mt-1">
        </div>

        {{-- Perfil --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Perfil (Role)</label>
            <select name="role" required class="w-full border-gray-300 rounded shadow text-sm mt-1">
                <option value="">Selecione</option>
                @foreach($roles as $role => $label)
                    <option value="{{ $role }}" @selected(old('role') === $role)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Botões --}}
        <div class="flex justify-between items-center mt-4">
            <a href="{{ route('admin.usuarios.index') }}"
               class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
                ⬅ Voltar
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800 transition">
                💾 Criar Usuário
            </button>
        </div>
    </form>
</div>
@endsection
