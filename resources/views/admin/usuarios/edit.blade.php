@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-green-800 mb-4">Editar Usuário</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Nome --}}
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
            <input type="text" name="name" id="name" value="{{ old('name', $usuario->name) }}"
                class="w-full rounded border-gray-300 shadow-sm mt-1" required>
        </div>

        {{-- E-mail --}}
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
            <input type="email" name="email" id="email" value="{{ old('email', $usuario->email) }}"
                class="w-full rounded border-gray-300 shadow-sm mt-1" required>
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
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Nova Senha (opcional)</label>
            <input type="password" name="password" id="password"
                class="w-full rounded border-gray-300 shadow-sm mt-1">
        </div>

        {{-- Role --}}
        <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-gray-700">Função</label>
            <select name="role" id="role" class="w-full rounded border-gray-300 shadow-sm mt-1">
                @foreach($roles as $roleName => $label)
                    <option value="{{ $roleName }}" @if($usuario->roles->first()?->name === $roleName) selected @endif>
                        {{ $label }}
                    </option>
                @endforeach

            </select>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded shadow">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>
@endsection
