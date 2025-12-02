@extends('layouts.app')

@section('title', 'Indicar Cidadãos')

@section('content')
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow">
        <h1 class="text-2xl font-bold text-green-700 mb-6">
            ✅ Indicar para o Programa – {{ $programa->nome }}
        </h1>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($inscricoes->isEmpty())
            <p class="text-gray-500">Nenhum cidadão disponível para indicação.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700 font-semibold">
                        <tr>
                            <th class="px-4 py-3">Nome</th>
                            <th class="px-4 py-3">CPF</th>
                            <th class="px-4 py-3">Telefone</th>
                            <th class="px-4 py-3 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inscricoes as $inscricao)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-3 text-indigo-700">
                                    {{ $inscricao->cidadao->nome }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $inscricao->cidadao->cpf }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $inscricao->cidadao->telefone ?? 'Não informado' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('assistente.programas.indicar.form', [$programa->id, $inscricao->cidadao_id]) }}"
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm inline-block">
                                        ➕ Indicar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
