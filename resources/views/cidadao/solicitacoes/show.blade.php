@extends('layouts.app')

@section('title', '📥 Detalhes da Solicitação')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow space-y-6">

    {{-- Título --}}
    <div class="flex justify-between items-start">
        <h1 class="text-2xl font-bold text-green-800 flex items-center gap-2">
            🗂️ {{ $solicitacao->titulo }}
        </h1>
        <span class="px-3 py-1 rounded-full text-sm font-medium
            {{ $solicitacao->status === 'pendente' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
            {{ ucfirst($solicitacao->status) }}
        </span>
    </div>

    {{-- Informações básicas --}}
    <div class="text-sm text-gray-700 space-y-2">
        <p><strong>👤 Coordenador:</strong> {{ $solicitacao->coordenador->name ?? 'Desconhecido' }}</p>
        <p><strong>📨 Destinatário:</strong>
            {{ $solicitacao->destinatario->name ?? 'Todos os ' . ucfirst($solicitacao->destinatario_tipo) . 's' }}
        </p>
        <p><strong>🕓 Enviada em:</strong> {{ $solicitacao->created_at->format('d/m/Y H:i') }}</p>
    </div>

    {{-- Mensagem --}}
    <div class="bg-gray-100 p-4 rounded shadow text-gray-800 whitespace-pre-line">
        <strong>📄 Mensagem:</strong><br>
        {{ $solicitacao->mensagem }}
    </div>

    {{-- Resposta --}}
    @if($solicitacao->resposta)
        <div class="bg-green-50 border-l-4 border-green-600 p-4 rounded shadow text-green-900">
            <strong>💬 Sua Resposta:</strong><br>
            {{ $solicitacao->resposta }}
        </div>
    @endif

    {{-- Formulário de Resposta --}}
    @if($solicitacao->status === 'pendente')
        <form method="POST" action="{{ route('cidadao.solicitacoes.responder', $solicitacao->id) }}" class="space-y-3">
            @csrf
            <label for="resposta" class="block font-medium text-sm text-gray-700">Responder:</label>
            <textarea name="resposta" rows="4" class="w-full border rounded px-3 py-2 shadow-sm focus:ring-green-600 focus:border-green-600" placeholder="Digite sua resposta..."></textarea>
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded shadow">Enviar Resposta</button>
        </form>
    @endif

    {{-- Botões de Ação --}}
    <div class="flex gap-2">
        @if($solicitacao->status === 'pendente')
            <form method="POST" action="{{ route('cidadao.solicitacoes.concluir', $solicitacao->id) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                    ✅ Concluir
                </button>
            </form>
        @elseif($solicitacao->status === 'concluida')
            <button disabled class="bg-green-600 text-white px-4 py-2 rounded shadow opacity-70 cursor-not-allowed">
                ✅ Concluído
            </button>
        @endif
    </div>

</div>
@endsection
