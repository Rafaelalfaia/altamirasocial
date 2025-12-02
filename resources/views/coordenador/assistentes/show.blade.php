@extends('layouts.app')

@section('title', 'Detalhes do Assistente Social')

@section('content')
    <div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow">
        <h1 class="text-2xl font-bold text-green-700 mb-6">🧾 Detalhes do Assistente Social</h1>

        <div class="space-y-4 text-sm">
            <div>
                <span class="font-semibold text-gray-700">Nome:</span>
                <span class="text-gray-800">{{ $assistente->name }}</span>
            </div>

            <div>
                <span class="font-semibold text-gray-700">CPF:</span>
                <span class="text-gray-800">{{ $assistente->cpf }}</span>
            </div>

            <div>
                <span class="font-semibold text-gray-700">E-mail:</span>
                <span class="text-gray-800">{{ $assistente->email ?? '—' }}</span>
            </div>

            <div>
                <span class="font-semibold text-gray-700">Criado em:</span>
                <span class="text-gray-800">{{ $assistente->created_at->format('d/m/Y H:i') }}</span>
            </div>

            @if ($assistente->coordenador)
                <div>
                    <span class="font-semibold text-gray-700">Criado pelo Coordenador:</span>
                    <span class="text-gray-800">{{ $assistente->coordenador->name }}</span>
                </div>
            @endif
        </div>

        <div class="mt-6 flex justify-between">
            <a href="{{ route('coordenador.assistentes.index') }}"
               class="text-sm text-gray-600 hover:underline">← Voltar para a lista</a>

            <a href="{{ route('coordenador.assistentes.entrar', $assistente->id) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded">
                🔑 Entrar como Assistente
            </a>
        </div>
    </div>
@endsection
