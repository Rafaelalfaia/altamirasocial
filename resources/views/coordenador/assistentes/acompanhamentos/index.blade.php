@extends('layouts.app')

@section('title', 'Acompanhamentos do Assistente')

@section('content')
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold text-green-700 mb-6">
            📋 Acompanhamentos de {{ $assistente->name }}
        </h1>

        <p class="text-sm text-gray-600 mb-4">Total: {{ $acompanhamentos->total() }} registros</p>

        @if ($acompanhamentos->isEmpty())
            <p class="text-gray-500">Nenhum acompanhamento registrado ainda para este assistente.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border rounded">
                    <thead class="bg-gray-100 text-gray-700 font-semibold">
                        <tr>
                            <th class="px-4 py-2 text-left">Cidadão</th>
                            <th class="px-4 py-2 text-left">CPF</th>
                            <th class="px-4 py-2 text-left">Data</th>
                            <th class="px-4 py-2 text-left">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($acompanhamentos as $acomp)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2">{{ $acomp->cidadao->nome }}</td>
                                <td class="px-4 py-2">{{ $acomp->cidadao->cpf }}</td>
                                <td class="px-4 py-2">{{ $acomp->data->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 space-y-1">
                                    <a href="{{ route('coordenador.assistentes.acompanhamentos.show', [$assistente->id, $acomp->id]) }}"
                                       class="text-blue-600 hover:underline block">
                                        📖 Ver 1º Atendimento
                                    </a>

                                    <a href="{{ route('coordenador.assistentes.acompanhamentos.evolucoes.index', [$assistente->id, $acomp->id]) }}"
                                       class="text-green-600 hover:underline block">
                                        📈 Ver Evoluções
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginação --}}
            <div class="mt-4">
                {{ $acompanhamentos->links() }}
            </div>
        @endif
    </div>
@endsection
