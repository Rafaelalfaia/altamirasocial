@extends('layouts.app')

@section('title', 'Novo Cidadão Temporário')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-green-700 mb-6">➕ Novo Cidadão Temporário</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('restaurante.coordenador.temporarios.store') }}" method="POST">
        @csrf

        {{-- Nome --}}
        <div class="mb-4">
            <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo</label>
            <input type="text" name="nome" id="nome" value="{{ old('nome') }}" required
                   class="w-full border rounded px-3 py-2 mt-1">
        </div>

        {{-- Motivo --}}
        <div class="mb-4">
            <label for="motivo" class="block text-sm font-medium text-gray-700">Motivo da Criação</label>
            <textarea name="motivo" id="motivo" rows="3" required
                      class="w-full border rounded px-3 py-2 mt-1">{{ old('motivo') }}</textarea>
        </div>

        {{-- Prazo de Validade --}}
        <div class="mb-4">
            <label for="prazo_validade" class="block text-sm font-medium text-gray-700">Prazo de Validade (meses)</label>
            <select name="prazo_validade" id="prazo_validade" required class="w-full border rounded px-3 py-2 mt-1">
                <option value="" disabled {{ old('prazo_validade') ? '' : 'selected' }}>Selecione</option>
                <option value="1" {{ old('prazo_validade') == 1 ? 'selected' : '' }}>1 mês</option>
                <option value="2" {{ old('prazo_validade') == 2 ? 'selected' : '' }}>2 meses</option>
                <option value="3" {{ old('prazo_validade') == 3 ? 'selected' : '' }}>3 meses</option>
            </select>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                💾 Salvar Temporário
            </button>
        </div>
    </form>
</div>
@endsection
