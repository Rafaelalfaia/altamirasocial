@extends('layouts.app')

@section('title', 'Visualizar Solicitação')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow space-y-6">
    <h1 class="text-2xl font-bold text-green-800 flex items-center gap-2">
        📄 Detalhes da Solicitação
    </h1>

    {{-- Título --}}
    <div>
        <p class="text-sm text-gray-500">Título:</p>
        <p class="text-lg font-semibold text-gray-800">{{ $solicitacao->titulo }}</p>
    </div>

    {{-- Mensagem --}}
    <div>
        <p class="text-sm text-gray-500">Mensagem:</p>
        <div class="bg-gray-100 border rounded p-4 text-gray-800 whitespace-pre-line">
            {{ $solicitacao->mensagem }}
        </div>
    </div>

    {{-- Resposta (se houver) --}}
    @if ($solicitacao->resposta)
        <div>
            <p class="text-sm text-gray-500">Resposta do Destinatário:</p>
            <div class="bg-green-100 border rounded p-4 text-gray-800 whitespace-pre-line">
                {{ $solicitacao->resposta }}
            </div>
        </div>
    @endif

    {{-- Destinatário --}}
    <div>
        <p class="text-sm text-gray-500">Destinatário:</p>
        <p class="text-base text-gray-800">
            @php
                $destinatario = $solicitacao->destinatario;
                $tipo = strtolower($solicitacao->destinatario_tipo);
            @endphp

            @if (is_null($solicitacao->destinatario_id))
                Todos os {{ ucfirst($tipo) }}s
            @elseif ($destinatario)
                {{ $destinatario->name }} — {{ $destinatario->telefone ?? 'sem telefone' }}
            @else
                <span class="text-red-600">Destinatário não encontrado</span>
            @endif
        </p>
    </div>

    {{-- Status --}}
    <div>
        <p class="text-sm text-gray-500">Status:</p>
        <span class="inline-block px-3 py-1 rounded-full text-sm
            {{ $solicitacao->status === 'concluida' ? 'bg-green-200 text-green-800' :
               ($solicitacao->status === 'cancelado' ? 'bg-red-200 text-red-800' : 'bg-yellow-100 text-yellow-700') }}">
            {{ ucfirst($solicitacao->status) }}
        </span>
    </div>

    {{-- Criado em --}}
    <div>
        <p class="text-sm text-gray-500">Criado em:</p>
        <p class="text-base text-gray-800">{{ $solicitacao->created_at->format('d/m/Y H:i') }}</p>
    </div>

    {{-- Botão de Voltar --}}
    <div class="text-right mt-6">
        <a href="{{ route('coordenador.solicitacoes.index') }}"
           class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
            ⬅️ Voltar
        </a>
    </div>
</div>
@endsection
