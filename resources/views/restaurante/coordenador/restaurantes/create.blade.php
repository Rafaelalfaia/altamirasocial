@extends('layouts.app')

@section('title', 'Novo Restaurante')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <h1 class="text-2xl font-bold text-green-800">➕ Novo Restaurante</h1>

    <form action="{{ route('restaurante.coordenador.restaurantes.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded shadow">
        @csrf

        {{-- Nome --}}
        <div>
            <label for="nome" class="block text-sm font-medium text-gray-700">Nome *</label>
            <input type="text" name="nome" id="nome" required value="{{ old('nome') }}"
                   class="w-full border-gray-300 rounded shadow-sm text-sm">
            @error('nome') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Endereço --}}
        <div>
            <label for="endereco" class="block text-sm font-medium text-gray-700">Endereço</label>
            <input type="text" name="endereco" id="endereco" value="{{ old('endereco') }}"
                   class="w-full border-gray-300 rounded shadow-sm text-sm">
            @error('endereco') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Ativo --}}
        <div class="flex items-center gap-2">
            <input type="checkbox" name="ativo" id="ativo" value="1" class="rounded text-green-600" checked>
            <label for="ativo" class="text-sm text-gray-700">Restaurante Ativo</label>
        </div>

        {{-- Botões --}}
        <div class="pt-4 flex justify-between">
            <a href="{{ route('restaurante.coordenador.restaurantes.index') }}"
               class="text-sm text-gray-600 hover:underline">⬅ Voltar</a>
            <button type="submit"
                    class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">
                Salvar
            </button>
        </div>
    </form>
</div>
@endsection
