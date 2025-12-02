@extends('layouts.app')

@section('title', 'Vendas do Dia')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-green-800">🧾 Vendas de Hoje</h1>

        <div class="flex gap-2">
            <a href="{{ route('restaurante.coordenador.vendas.create') }}"
               class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded text-sm shadow">
                ➕ Nova Venda
            </a>

            <form action="{{ route('restaurante.coordenador.vendas.finalizar-dia') }}" method="POST"
                onsubmit="return confirm('Deseja realmente finalizar o dia?')">
                @csrf
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm shadow">
                    🛑 Finalizar Dia
                </button>
            </form>


        </div>
    </div>

    {{-- Mensagem de sucesso --}}
    @if(session('mensagem'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded shadow-sm">
            {{ session('mensagem') }}
        </div>
    @endif

    @if($vendas->isEmpty())
        <div class="bg-white border border-gray-200 text-center text-gray-600 p-6 rounded shadow">
            Nenhuma venda registrada para hoje.
        </div>
    @else
        {{-- Tabela --}}
        <div class="overflow-x-auto bg-white rounded shadow border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-100 text-sm text-gray-700">
                        <th class="px-4 py-2">Cliente</th>
                        <th class="px-2 py-2">Tipo</th>
                        <th class="px-2 py-2 text-center">Pratos</th>
                        <th class="px-2 py-2 text-center">Consumo</th>
                        <th class="px-2 py-2 text-center">Pagamento</th>
                        <th class="px-2 py-2 text-center">Doação</th>
                        <th class="px-2 py-2 text-center">Estudante</th>
                        <th class="px-2 py-2">Registrado por</th>
                        <th class="px-2 py-2 text-center">Data</th>
                        <th class="px-2 py-2 text-center">Ações</th>
                    </tr>
                </thead>

                <tbody class="text-sm divide-y divide-gray-200">
                    @foreach($vendas as $venda)
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-800 whitespace-nowrap">
                                {{ $venda->tipo_cliente === 'cidadao' ? optional($venda->cidadao)->nome : optional($venda->cidadaoTemporario)->nome }}
                            </td>

                            <td class="px-2 py-2 capitalize text-center">{{ $venda->tipo_cliente }}</td>

                            <td class="px-2 py-2 text-center">{{ $venda->numero_pratos }}</td>

                            <td class="px-2 py-2 text-center">
                                {{ $venda->tipo_consumo === 'local' ? 'Local' : 'Retirada' }}
                            </td>

                            <td class="px-2 py-2 text-center">
                                {{ $venda->forma_pagamento ? strtoupper($venda->forma_pagamento) : '—' }}
                            </td>

                            <td class="px-2 py-2 text-center">
                                @if($venda->doacao)
                                    <span class="text-green-600 font-semibold">✔</span>
                                @else
                                    —
                                @endif
                            </td>

                            <td class="px-2 py-2 text-center">
                                @if($venda->estudante)
                                    <span class="text-blue-600 font-semibold">✔</span>
                                @else
                                    —
                                @endif
                            </td>

                            <td class="px-2 py-2 whitespace-nowrap">
                                {{ optional($venda->user)->name ?? '—' }}
                            </td>

                            <td class="px-2 py-2 text-xs text-gray-500 text-center">
                                {{ \Carbon\Carbon::parse($venda->data_venda)->format('d/m/Y H:i') }}
                            </td>

                            {{-- Ações --}}
                            <td class="px-2 py-2 text-center">
                                <form action="{{ route('restaurante.coordenador.vendas.destroy', $venda->id) }}"
                                      method="POST" onsubmit="return confirm('Deseja realmente apagar esta venda?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-800 text-xs font-medium">
                                        🗑️ Apagar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
