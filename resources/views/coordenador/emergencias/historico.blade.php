@extends('layouts.app')
@section('title', 'Histórico de Ocorrências')
@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-indigo-700 mb-6">📚 Histórico Completo de Ocorrências</h1>

    <a href="{{ route('coordenador.emergencias.index') }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm px-4 py-2 rounded shadow mb-4 inline-block">
        🔙 Voltar para últimas 48h
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
                        <td class="px-4 py-2">
                            <a href="{{ route('coordenador.emergencias.show', $emergencia->id) }}"
                               class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs">🔍 Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-gray-500 px-4 py-4">Nenhuma ocorrência encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection