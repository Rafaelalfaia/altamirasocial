@extends('layouts.app')

@section('title', 'Solicitações SEMAPS')

@section('content')
<div class="max-w-6xl mx-auto p-6 bg-white rounded-xl shadow">
    <h1 class="text-2xl font-bold text-green-800 mb-6">📥 Minhas Solicitações</h1>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @forelse ($solicitacoes as $solicitacao)
        <div class="border-l-4 border-green-600 bg-gray-50 p-5 rounded-lg mb-5 shadow-sm">
            {{-- Cabeçalho com título e status --}}
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-lg font-semibold text-gray-800">
                    📄 {{ $solicitacao->titulo }}
                </h2>
                <span class="text-sm font-medium px-3 py-1 rounded-full 
                    {{ $solicitacao->status === 'pendente' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                    {{ ucfirst($solicitacao->status) }}
                </span>
            </div>

            {{-- Nome do Coordenador --}}
            <p class="text-sm text-gray-600 mb-1">
                👤 <strong>Destinatário:</strong> {{ $solicitacao->destinatario->name ?? 'Todos os ' . $solicitacao->destinatario_tipo }}
            </p>

            {{-- Mensagem --}}
            <p class="text-gray-700 whitespace-pre-line text-sm">
                <strong>📩 Mensagem:</strong><br>{{ $solicitacao->mensagem }}
            </p>

            {{-- Resposta ou formulário de resposta --}}
            @if($solicitacao->resposta)
                <p class="mt-2 text-green-800 text-sm"><strong>💬 Sua Resposta:</strong><br> {{ $solicitacao->resposta }}</p>
            @else
                <form action="{{ route('cidadao.solicitacoes.responder', $solicitacao->id) }}" method="POST" class="mt-3">
                    @csrf
                    <label for="resposta" class="block text-sm font-medium text-gray-700">Responder:</label>
                    <textarea name="resposta" rows="2" class="w-full border rounded px-3 py-2 mt-1 text-sm" placeholder="Digite sua resposta..." required></textarea>
                    <button type="submit"
                        class="mt-2 bg-green-700 text-white px-4 py-1 rounded hover:bg-green-800 transition">
                        Enviar Resposta
                    </button>
                </form>
            @endif

            {{-- Botões de ação --}}
            <div class="flex gap-3 mt-4">
                @if($solicitacao->status === 'pendente')
                    <form method="POST" action="{{ route('cidadao.solicitacoes.concluir', $solicitacao->id) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 transition">
                            ✅ Concluir
                        </button>
                    </form>
                @else
                    <button disabled
                        class="bg-green-600 text-white px-4 py-1 rounded opacity-70 cursor-not-allowed">
                        ✅ Concluído
                    </button>
                @endif
            </div>

        </div>
    @empty
        <div class="text-gray-600">Nenhuma solicitação recebida até o momento.</div>
    @endforelse
</div>
@endsection
