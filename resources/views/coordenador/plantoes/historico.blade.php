@extends('layouts.app')

@section('title', 'Histórico de Plantões')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <h1 class="text-2xl font-bold text-green-800 flex items-center gap-2">
        📚 Histórico de Plantões – Últimos 6 Meses
    </h1>

    {{-- Filtros --}}
    <form method="GET" class="mb-6 flex gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700">Nome</label>
            <input type="text" name="nome" value="{{ request('nome') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Data</label>
            <input type="date" name="data" value="{{ request('data') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <button type="submit"
                    class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 text-sm">
                Filtrar
            </button>
        </div>
    </form>

    @if ($historicoPlantoes->isEmpty())
        <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 p-4 rounded shadow">
            Nenhum registro de plantão encontrado.
        </div>
    @else
        <div class="overflow-x-auto bg-white rounded-xl shadow p-4">
            <table class="min-w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-gray-800 font-semibold">
                    <tr>
                        <th class="px-4 py-2">👤 Nome</th>
                        <th class="px-4 py-2">📞 Telefone</th> {{-- Novo --}}
                        <th class="px-4 py-2">📅 Início</th>
                        <th class="px-4 py-2">📅 Término</th>
                        <th class="px-4 py-2">⏱ Duração</th>
                        <th class="px-4 py-2">📌 Status</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach ($historicoPlantoes as $plantao)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-4 py-2 font-medium">{{ $plantao->user->name }}</td>
                            <td class="px-4 py-2">{{ $plantao->user->telefone ?? '—' }}</td> {{-- Novo --}}
                            <td class="px-4 py-2">{{ $plantao->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2">
                                @if (!$plantao->ativo)
                                    {{ $plantao->updated_at->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-gray-400 italic">Em andamento</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                @if (!$plantao->ativo)
                                    {{ $plantao->created_at->diff($plantao->updated_at)->format('%dd %Hh %Im') }}
                                @else
                                    <span class="text-gray-400 italic">--</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                @if ($plantao->ativo)
                                    <span class="inline-block px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Ativo</span>
                                @else
                                    <span class="inline-block px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded-full">Finalizado</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                
            </table>
        </div>
    @endif

</div>
@endsection
