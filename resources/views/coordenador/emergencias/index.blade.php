@extends('layouts.app')
@section('title', 'Ocorrências Emergenciais - Últimas 48h')
@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-red-700 mb-6">🚨 Ocorrências Emergenciais (últimas 48h)</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('coordenador.emergencias.historico') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded shadow mb-4 inline-block">
        📚 Ver Histórico Completo
    </a>

    <div class="bg-white shadow rounded-xl overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-3">Cidadão</th>
                    <th class="px-4 py-3">Motivo</th>
                    <th class="px-4 py-3">Data</th>
                    <th class="px-4 py-3">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($emergencias as $emergencia)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $emergencia->cidadao->nome ?? 'Desconhecido' }}</td>
                        <td class="px-4 py-2">{{ $emergencia->motivo }}</td>
                        <td class="px-4 py-2">{{ $emergencia->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            <a href="{{ route('coordenador.emergencias.show', $emergencia->id) }}"
                               class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs">🔍 Ver</a>
                            <form action="{{ route('coordenador.emergencias.destroy', $emergencia->id) }}" method="POST" onsubmit="return confirm('Excluir ocorrência?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">
                                    🗑️ Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-gray-500 px-4 py-4">Nenhuma ocorrência nas últimas 48 horas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
