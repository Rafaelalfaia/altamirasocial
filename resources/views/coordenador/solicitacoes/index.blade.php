@extends('layouts.app')

@section('title', 'Solicitações Enviadas')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow">
    <h1 class="text-2xl font-bold text-green-800 mb-6">📤 Solicitações Enviadas</h1>

    <div class="mb-4 text-right">
        <a href="{{ route('coordenador.solicitacoes.create') }}"
           class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 transition">
            ➕ Nova Solicitação
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @forelse ($solicitacoes as $solicitacao)
        <div class="border-l-4 border-green-700 pl-4 py-4 mb-4 bg-gray-50 rounded-lg shadow-sm">
            <div class="flex justify-between items-center mb-1">
                <h2 class="text-base font-semibold text-gray-800">
                    📄 {{ $solicitacao->titulo }}
                </h2>
                <div class="flex gap-2 text-sm">
                    {{-- Ver --}}
                    <a href="{{ route('coordenador.solicitacoes.show', $solicitacao) }}"
                       class="text-blue-600 hover:underline">Ver</a>

                    {{-- Editar --}}
                    <a href="{{ route('coordenador.solicitacoes.edit', $solicitacao) }}"
                    class="text-yellow-600 hover:underline">Editar</a>

                    
                    {{-- exluir) --}}
                    <form action="{{ route('coordenador.solicitacoes.destroy', $solicitacao->id) }}"
                          method="POST"
                          onsubmit="return confirm('Tem certeza que deseja excluir esta solicitação?');"
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">🗑️ Excluir</button>
                    </form>
                </div>
            </div>

            {{-- Informações de status --}}
            <p class="text-xs text-gray-500">
                Status: <strong>{{ ucfirst($solicitacao->status) }}</strong> |
                Envio: <strong>{{ ucfirst($solicitacao->status_envio ?? 'ativo') }}</strong> |
                Criado em: {{ $solicitacao->created_at->format('d/m/Y H:i') }}
            </p>

            {{-- Destinatário --}}
            <p class="text-sm text-gray-700 mt-1">
                Destinatário:
                @if($solicitacao->destinatario_id)
                    {{ $solicitacao->destinatario->name ?? 'Usuário não encontrado' }} ({{ ucfirst($solicitacao->destinatario_tipo) }})
                @else
                    Todos os {{ ucfirst($solicitacao->destinatario_tipo) }}s
                @endif
            </p>

            {{-- Mensagem ou descrição resumida --}}
            <p class="text-sm text-gray-600 mt-2">
                {{ Str::limit($solicitacao->mensagem ?? '-', 100) }}
            </p>
        </div>
    @empty
        <p class="text-gray-600 italic">Nenhuma solicitação registrada ainda.</p>
    @endforelse
</div>
@endsection
