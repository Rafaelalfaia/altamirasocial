@extends('layouts.app')

@section('title', 'Histórico de Ações nos Programas')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-indigo-700">🗂️ Histórico de Indicações e Denúncias</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- Indicações --}}
        <div class="bg-white p-4 shadow rounded">
            <h2 class="text-lg font-semibold text-green-700 mb-3">✅ Indicações Realizadas</h2>

            @if ($indicacoes->isEmpty())
                <p class="text-gray-500 text-sm">Nenhuma indicação registrada.</p>
            @else
                <ul class="divide-y divide-gray-200 text-sm">
                    @foreach ($indicacoes as $item)
                        <li class="py-3">
                            <p>
                                <span class="font-medium text-indigo-600">{{ $item->cidadao->nome }}</span>
                                foi indicado para o programa
                                <span class="font-medium text-green-600">{{ $item->programa->nome }}</span>
                            </p>
                            <p class="text-xs text-gray-500 mb-1">{{ $item->created_at->format('d/m/Y H:i') }}</p>
                            <p class="text-sm">
                                <strong>Status:</strong>
                                @if($item->status === 'aprovada')
                                    <span class="text-green-600 font-semibold">Pedido aprovado, o cidadão entrou no programa Social</span>
                                @elseif($item->status === 'reprovada')
                                    <span class="text-red-600 font-semibold">Pedido recusado, o cidadão não foi aprovado no programa social</span>
                                @else
                                    <span class="text-yellow-600 font-semibold">Em Análise</span>
                                @endif
                            </p>
                            @if($item->resposta_coordenador)
                                <p class="text-gray-700 text-sm mt-1"><strong>Resposta do Coordenador:</strong> {{ $item->resposta_coordenador }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Denúncias --}}
        <div class="bg-white p-4 shadow rounded">
            <h2 class="text-lg font-semibold text-red-700 mb-3">⚠️ Denúncias Realizadas</h2>

            @if ($denuncias->isEmpty())
                <p class="text-gray-500 text-sm">Nenhuma denúncia registrada.</p>
            @else
                <ul class="divide-y divide-gray-200 text-sm">
                    @foreach ($denuncias as $item)
                        <li class="py-3">
                            <p>
                                <span class="font-medium text-indigo-600">{{ $item->cidadao->nome }}</span>
                                foi denunciado a sair do programa
                                <span class="font-medium text-red-600">{{ $item->programa->nome }}</span>
                            </p>
                            <p class="text-sm"><strong>Motivo:</strong> "{{ $item->motivo }}"</p>
                            <p class="text-xs text-gray-500 mb-1">{{ $item->created_at->format('d/m/Y H:i') }}</p>
                            <p class="text-sm">
                                <strong>Status:</strong>
                                @if($item->status === 'aprovada')
                                    <span class="text-green-600 font-semibold">Pedido aprovado, o cidadão saiu do programa</span>
                                @elseif($item->status === 'reprovada')
                                    <span class="text-red-600 font-semibold">Pedido recusado, o cidadão continuará no programa</span>
                                @else
                                    <span class="text-yellow-600 font-semibold">Em Análise</span>
                                @endif
                            </p>
                            @if($item->resposta_coordenador)
                                <p class="text-gray-700 text-sm mt-1"><strong>Resposta do Coordenador:</strong> {{ $item->resposta_coordenador }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
