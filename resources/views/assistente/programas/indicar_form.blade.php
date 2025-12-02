@extends('layouts.app')

@section('title', 'Indicar — ' . $cidadao->nome)

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold text-green-700 mb-4 flex items-center gap-2">
        ✅ Indicar – {{ $cidadao->nome }}
    </h1>

    <p class="text-gray-600 text-sm mb-6">
        Você está indicando este cidadão para o programa:
        <a href="#" class="text-blue-700 font-semibold hover:underline">{{ $programa->nome }}</a>.
    </p>

    <form method="POST" action="{{ route('assistente.programas.indicar.acao', [$programa->id, $cidadao->id]) }}">
        @csrf

        <label for="justificativa" class="block font-medium text-sm text-gray-700 mb-1">
            Justificativa da Indicação
        </label>
        <textarea name="justificativa" id="justificativa" rows="5" required
            placeholder="Descreva o motivo da indicação..."
            class="w-full p-3 border border-gray-300 rounded shadow-sm focus:ring-green-600 focus:border-green-600">{{ old('justificativa') }}</textarea>

        <div class="mt-6 flex justify-end">
            <button type="submit"
                class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded shadow inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 13l4 4L19 7" />
                </svg>
                Enviar Indicação
            </button>
        </div>
    </form>
</div>
@endsection
