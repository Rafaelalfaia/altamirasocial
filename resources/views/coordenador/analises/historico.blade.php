@extends('layouts.app')

@section('title', 'Histórico de Análises')

@section('content')
<div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        📜 Histórico de Análises de Indicações e Denúncias
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Histórico de indicações --}}
        <div>
            <h2 class="text-lg font-semibold mb-2">📌 Indicações Analisadas</h2>
            @forelse ($indicacoes as $item)
                <div class="border p-4 rounded mb-3 shadow-sm">
                    <p><strong>Programa:</strong> {{ $item->programa->nome }}</p>
                    <p><strong>Cidadão:</strong> {{ $item->cidadao->nome }}</p>
                    <p><strong>Status:</strong> 
                        <span class="{{ $item->status === 'aprovada' ? 'text-green-600' : 'text-red-600' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </p>
                    @if ($item->resposta_coordenador)
                        <p><strong>Motivo:</strong> {{ $item->resposta_coordenador }}</p>
                    @endif
                    <p class="text-xs text-gray-500">Avaliado em: {{ \Carbon\Carbon::parse($item->avaliado_em)->format('d/m/Y H:i') }}</p>
                </div>
            @empty
                <p class="text-gray-500">Nenhuma indicação analisada.</p>
            @endforelse
        </div>

        {{-- Histórico de denúncias --}}
        <div>
            <h2 class="text-lg font-semibold mb-2">🚨 Denúncias Analisadas</h2>
            @forelse ($denuncias as $item)
                <div class="border p-4 rounded mb-3 shadow-sm">
                    <p><strong>Programa:</strong> {{ $item->programa->nome }}</p>
                    <p><strong>Cidadão:</strong> {{ $item->cidadao->nome }}</p>
                    <p><strong>Status:</strong> 
                        <span class="{{ $item->status === 'aprovada' ? 'text-green-600' : 'text-red-600' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </p>
                    @if ($item->resposta_coordenador)
                        <p><strong>Motivo:</strong> {{ $item->resposta_coordenador }}</p>
                    @endif
                    <p class="text-xs text-gray-500">Avaliado em: {{ \Carbon\Carbon::parse($item->avaliado_em)->format('d/m/Y H:i') }}</p>
                </div>
            @empty
                <p class="text-gray-500">Nenhuma denúncia analisada.</p>
            @endforelse
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('coordenador.analises.index') }}" class="text-sm text-indigo-600 hover:underline">🔙 Voltar para Análises Pendentes</a>
    </div>
</div>
@endsection
