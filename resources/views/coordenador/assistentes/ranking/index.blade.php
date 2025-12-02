@extends('layouts.app')

@section('title', 'Ranking de Assistentes')

@section('content')
<div class="max-w-6xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-green-800 mb-6">🏆 Ranking Completo de Assistentes</h1>

    {{-- Filtro por período --}}
    <form method="GET" class="mb-6 flex flex-wrap gap-4 items-center">
        <label for="periodo" class="text-sm font-semibold text-gray-700">Período:</label>
        <select name="periodo" id="periodo"
                class="rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
            <option value="1_mes" {{ $periodo === '1_mes' ? 'selected' : '' }}>Último mês</option>
            <option value="3_meses" {{ $periodo === '3_meses' ? 'selected' : '' }}>Últimos 3 meses</option>
            <option value="6_meses" {{ $periodo === '6_meses' ? 'selected' : '' }}>Últimos 6 meses</option>
            <option value="1_ano" {{ $periodo === '1_ano' ? 'selected' : '' }}>Último ano</option>
        </select>

        <button type="submit"
                class="bg-green-700 hover:bg-green-800 text-white font-bold py-2 px-4 rounded transition">
            Aplicar
        </button>
    </form>

    {{-- Lista completa --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="w-full table-auto text-left border border-gray-200">
            <thead class="bg-green-100 text-green-900 text-sm">
                <tr>
                    <th class="p-3">#</th>
                    <th class="p-3">Foto</th>
                    <th class="p-3">Nome</th>
                    <th class="p-3">Total de Visitas</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                @forelse ($ranking as $index => $assistente)
                    <tr>
                        <td class="p-3 font-semibold">{{ $index + 1 }}</td>
                        <td class="p-3">
                            <img src="{{ $assistente->foto_url ?? asset('default-avatar.png') }}"
                                 class="w-10 h-10 rounded-full object-cover border border-gray-300" alt="{{ $assistente->name }}">
                        </td>
                        <td class="p-3">{{ $assistente->name }}</td>
                        <td class="p-3 font-semibold">{{ $assistente->total_evolucoes ?? 0 }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-500">Nenhum registro encontrado para o período selecionado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
