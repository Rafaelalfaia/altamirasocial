@extends('layouts.app')

@section('title', 'Análise de Indicações e Denúncias')

@section('content')
<div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow">
    <h1 class="text-2xl font-bold text-yellow-700 mb-6">
        🕵️ Análises Pendentes de Indicações e Denúncias
    </h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Indicações pendentes --}}
        <div>
            <h2 class="text-lg font-semibold mb-2">📌 Indicações Pendentes</h2>
            @forelse ($indicacoesPendentes as $item)
                <div class="border p-4 rounded mb-3 shadow-sm bg-yellow-50">
                    <p><strong>Programa:</strong> {{ $item->programa->nome }}</p>
                    <p><strong>Indicado:</strong> {{ $item->cidadao->nome }}</p>
                    <p><strong>Assistente:</strong> {{ $item->assistente->name ?? '-' }}</p>
                    <div class="mt-3 flex flex-col sm:flex-row gap-2">
                        <form method="POST"
                              action="{{ route('coordenador.analises.aceitar', ['tipo' => 'indicacao', 'id' => $item->id]) }}"
                              onsubmit="return confirm('Tem certeza que deseja aceitar? Ao aceitar o cidadão entrará no programa social automaticamente.')">
                            @csrf
                            <button type="submit" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">Aceitar</button>
                        </form>

                        <form method="POST"
                              action="{{ route('coordenador.analises.recusar', ['tipo' => 'indicacao', 'id' => $item->id]) }}"
                              onsubmit="return confirm('Tem certeza que deseja recusar? Ao recusar o cidadão continuará fora do programa social.')">
                            @csrf
                            <input type="text" name="motivo_rejeicao" required class="border px-2 py-1 rounded text-sm" placeholder="Motivo da recusa">
                            <button type="submit" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">Recusar</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">Nenhuma indicação pendente.</p>
            @endforelse
        </div>

        {{-- Denúncias pendentes --}}
        <div>
            <h2 class="text-lg font-semibold mb-2">🚨 Denúncias Pendentes</h2>
            @forelse ($denunciasPendentes as $item)
                <div class="border p-4 rounded mb-3 shadow-sm bg-red-50">
                    <p><strong>Programa:</strong> {{ $item->programa->nome }}</p>
                    <p><strong>Denunciado:</strong> {{ $item->cidadao->nome }}</p>
                    <p><strong>Assistente:</strong> {{ $item->assistente->name ?? '-' }}</p>
                    <div class="mt-3 flex flex-col sm:flex-row gap-2">
                        <form method="POST"
                              action="{{ route('coordenador.analises.aceitar', ['tipo' => 'denuncia', 'id' => $item->id]) }}"
                              onsubmit="return confirm('Tem certeza que deseja aceitar o pedido de denúncia? Ao aceitar o cidadão sairá do programa social automaticamente.')">
                            @csrf
                            <button type="submit" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">Aceitar</button>
                        </form>

                        <form method="POST"
                              action="{{ route('coordenador.analises.recusar', ['tipo' => 'denuncia', 'id' => $item->id]) }}"
                              onsubmit="return confirm('Tem certeza que deseja recusar? Ao recusar o cidadão continuará participando do programa social.')">
                            @csrf
                            <input type="text" name="motivo_rejeicao" required class="border px-2 py-1 rounded text-sm" placeholder="Motivo da recusa">
                            <button type="submit" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">Recusar</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">Nenhuma denúncia pendente.</p>
            @endforelse
        </div>
    </div>

    <div class="mt-6 text-right">
        <a href="{{ route('coordenador.analises.historico') }}"
           class="text-sm text-blue-600 hover:underline">📜 Ver Histórico de Análises</a>
    </div>
</div>
@endsection
