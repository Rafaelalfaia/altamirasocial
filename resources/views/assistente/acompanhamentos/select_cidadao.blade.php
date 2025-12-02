@extends('layouts.app')

@section('title', 'Selecionar Cidadão para Acompanhamento')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-green-700 mb-6">👥 Selecionar Cidadão</h1>

    {{-- Filtro --}}
    <form action="{{ route('assistente.evolucoes.selecionar') }}" method="GET" class="mb-6">
        <div class="flex gap-2">
            <input
                type="text"
                name="search"
                placeholder="Digite nome ou CPF"
                value="{{ $search ?? request('search') }}"
                class="flex-1 px-4 py-2 border rounded shadow focus:outline-none focus:ring"
            />
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                Buscar
            </button>
            @if(($search ?? '') !== '')
                <a href="{{ route('assistente.evolucoes.selecionar') }}"
                   class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">
                    Limpar
                </a>
            @endif
        </div>
    </form>

    {{-- Lista paginada (50 por página) --}}
    @php
        // Proteção caso algum caller não envie
        $acompPorCidadao = $acompPorCidadao ?? [];
    @endphp

    @if($cidadaos->count())
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-gray-600">
                        <th class="px-4 py-3 font-medium">Cidadão</th>
                        <th class="px-4 py-3 font-medium">CPF</th>
                        <th class="px-4 py-3 font-medium text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($cidadaos as $cidadao)
                        @php
                            $acompId = $acompPorCidadao[$cidadao->id] ?? null;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-800">{{ $cidadao->nome }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $cidadao->cpf }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($acompId)
                                    <a
                                        href="{{ route('assistente.evolucoes.index', $acompId) }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                        Ver evoluções
                                    </a>
                                @else
                                    <a
                                        href="{{ route('assistente.evolucoes.iniciar', $cidadao->id) }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 transition">
                                        Iniciar acompanhamento
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        <div class="mt-6">
            {{ $cidadaos->links() }}
        </div>
    @else
        <div class="border border-amber-200 bg-amber-50 text-amber-800 rounded px-4 py-3 text-sm">
            Nenhum cidadão encontrado{{ ($search ?? '') ? " para \"{$search}\"" : '' }}.
        </div>
    @endif
</div>
@endsection
