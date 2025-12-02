@extends('layouts.app')

@section('title', 'Relatar Emergência à SEMED')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-xl shadow-md mt-6">
    <h1 class="text-2xl font-bold text-red-700 mb-4 flex items-center gap-2">
        📝 Relatar Emergência à SEMED
    </h1>

    <div class="mb-4 text-sm text-gray-600">
        <p><strong>Motivo:</strong> {{ $emergencia->motivo }}</p>
        <p><strong>Descrição da Situação:</strong> {{ $emergencia->descricao ?? 'Não informada.' }}</p>
    </div>

    <form action="{{ route('assistente.emergencias.enviar-relatorio', $emergencia->id) }}" method="POST">

        @csrf

        <div class="mb-4">
            <label for="conclusao" class="block text-sm font-medium text-gray-700 mb-1">
                Conclusão e Providências Tomadas
            </label>
            <textarea name="conclusao" id="conclusao" rows="6"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-600 focus:border-red-600 text-sm"
                required>{{ old('conclusao', $emergencia->conclusao) }}</textarea>

            @error('conclusao')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-4 mt-6">
            <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm shadow">
                📤 Enviar Relatório
            </button>

            <a href="{{ route('assistente.dashboard') }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm shadow">
                🔙 Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
