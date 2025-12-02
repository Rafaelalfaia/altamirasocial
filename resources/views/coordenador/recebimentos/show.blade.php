@extends('layouts.app')

@section('title', 'Detalhes do Registro')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-green-700 mb-6">📄 Detalhes do Recebimento ou Encaminhamento</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <p class="text-gray-600">📌 <strong>Tipo:</strong></p>
            <p class="text-black">{{ ucfirst($registro->tipo) }}</p>
        </div>

        <div>
            <p class="text-gray-600">🙎 <strong>Cidadão:</strong></p>
            <p class="text-black">{{ $registro->nome_cidadao }}</p>
        </div>

        <div>
            <p class="text-gray-600">🏛 <strong>Órgão Público:</strong></p>
            <p class="text-black">{{ $registro->orgao->nome }}</p>
        </div>

        <div>
            <p class="text-gray-600">📅 <strong>Data de Registro:</strong></p>
            <p class="text-black">{{ $registro->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div>
            <p class="text-gray-600">📂 <strong>Programa Social:</strong></p>
            <p class="text-black">{{ $registro->programa->nome ?? '—' }}</p>
        </div>

        <div class="md:col-span-2">
            <p class="text-gray-600">📝 <strong>Descrição:</strong></p>
            <p class="text-black whitespace-pre-line">{{ $registro->descricao ?? '—' }}</p>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('coordenador.recebimentos.index') }}"
           class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
            ⬅️ Voltar
        </a>
    </div>
</div>
@endsection
