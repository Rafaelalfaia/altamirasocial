@extends('layouts.app')

@section('title', 'Nova Venda')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Título --}}
    <h1 class="text-2xl font-bold text-green-800">🍽️ Nova Venda</h1>

    {{-- Botões de Cadastro --}}
    <div class="mt-4 flex flex-col sm:flex-row gap-4">
        <a href="{{ route('restaurante.coordenador.cidadaos.create') }}"
           class="bg-green-800 hover:bg-green-900 text-white font-semibold px-4 py-2 rounded shadow text-center w-full sm:w-auto">
            ➕ Cadastrar Cidadão
        </a>

        <a href="{{ route('restaurante.coordenador.temporarios.create') }}"
           class="bg-green-700 hover:bg-green-800 text-white font-semibold px-4 py-2 rounded shadow text-center w-full sm:w-auto">
            ➕ Cadastrar Temporário
        </a>
    </div>

    {{-- Campo de Busca --}}
    <div class="mt-6">
        <input type="text" id="filtroCidadao"
               placeholder="Buscar nome ou CPF..."
               class="w-full sm:max-w-md px-4 py-2 border border-gray-300 rounded shadow-sm focus:ring-green-400 focus:outline-none"
               oninput="filtrarCidadaos()">
    </div>

    <div class="flex flex-col lg:flex-row gap-6 mt-4">

        {{-- Listagem de cidadãos --}}
        <div class="w-full lg:w-2/3 space-y-6">

            {{-- Cidadãos Normais --}}
            <div>
                <h2 class="text-lg font-semibold text-green-700 mb-2">👤 Cidadãos</h2>
                <div class="bg-white rounded shadow overflow-y-auto max-h-[300px] border border-gray-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr>
                                <th class="px-4 py-2 text-left">Nome</th>
                                <th class="px-2 py-2">CPF</th>
                                <th class="px-2 py-2 text-center">Ação</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaCidadaosNormais" class="divide-y divide-gray-200">
                            @foreach ($cidadaos->where('tipo', 'normal') as $c)
                                <tr>
                                    <td class="px-4 py-2">{{ $c->nome }}</td>
                                    <td class="px-2 py-2">{{ $c->cpf }}</td>
                                    <td class="px-2 py-2 text-center">
                                        <button type="button"
                                                onclick="selecionarCidadao({{ $c->id }}, '{{ $c->nome }}', 'normal')"
                                                class="bg-green-700 hover:bg-green-800 text-white px-3 py-1 rounded text-xs shadow">
                                            Selecionar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Temporários --}}
            <div>
                <h2 class="text-lg font-semibold text-green-700 mb-2">🕓 Temporários</h2>
                <div class="bg-white rounded shadow overflow-y-auto max-h-[300px] border border-gray-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr>
                                <th class="px-4 py-2 text-left">Nome</th>
                                <th class="px-2 py-2">CPF</th>
                                <th class="px-2 py-2 text-center">Ação</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaCidadaosTemporarios" class="divide-y divide-gray-200">
                            @foreach ($cidadaos->where('tipo', 'temporario') as $c)
                                <tr>
                                    <td class="px-4 py-2">{{ $c->nome }}</td>
                                    <td class="px-2 py-2">{{ $c->cpf ?? '—' }}</td>
                                    <td class="px-2 py-2 text-center">
                                        <button type="button"
                                                onclick="selecionarCidadao({{ $c->id }}, '{{ $c->nome }}', 'temporario')"
                                                class="bg-green-700 hover:bg-green-800 text-white px-3 py-1 rounded text-xs shadow">
                                            Selecionar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Painel lateral --}}
        <div class="w-full lg:w-1/3">
            <form method="POST" action="{{ route('restaurante.coordenador.vendas.store') }}"
                  class="bg-white rounded shadow p-6 space-y-5 border border-gray-100">
                @csrf

                <input type="hidden" name="tipo_cidadao" id="tipoCidadaoSelecionado">
                <input type="hidden" name="cidadao_id" id="cidadaoSelecionado">

                {{-- Info cidadão --}}
                <div id="infoCidadao" class="hidden">
                    <label class="block text-gray-700 text-sm mb-1">Cidadão Selecionado:</label>
                    <div id="nomeCidadaoSelecionado" class="text-base font-bold text-green-800"></div>
                    <hr class="my-3">
                </div>

                {{-- Tipo de Consumo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Consumo</label>
                    <select name="tipo_consumo" required
                            class="w-full border-gray-300 rounded shadow-sm text-sm">
                        <option value="local">Consumo no Local</option>
                        <option value="retirada">Retirada</option>
                    </select>
                </div>

                {{-- Quantidade de Pratos --}}
                <div>
                    <label for="numero_pratos" class="block text-sm font-medium text-gray-700 mb-1">Quantidade de Pratos</label>
                    <input type="number" name="numero_pratos" id="numero_pratos" min="1" max="10" value="1"
                        class="w-full border-gray-300 rounded shadow-sm text-sm" required>
                </div>

                {{-- Campos para Cidadão Normal --}}
                <div id="camposNormal" class="hidden space-y-3">
                    <label class="inline-flex items-center text-sm">
                        <input type="checkbox" name="estudante" class="mr-2">
                        Estudante
                    </label>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Forma de Pagamento</label>
                        <select name="forma_pagamento" class="w-full border-gray-300 rounded shadow-sm text-sm">
                            <option value="">Selecione</option>
                            <option value="pix">PIX</option>
                            <option value="debito">Débito</option>
                            <option value="credito">Crédito</option>
                            <option value="dinheiro">Dinheiro</option>
                        </select>
                    </div>
                </div>

                {{-- Campos para Temporário --}}
                <div id="camposTemporario" class="hidden">
                    <label class="inline-flex items-center text-sm">
                        <input type="checkbox" name="doacao" class="mr-2">
                        Esta refeição foi uma doação
                    </label>
                </div>

                {{-- Botão de Envio --}}
                <div class="pt-4">
                    <button type="submit"
                            class="w-full bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded text-sm font-semibold">
                        Finalizar Venda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script --}}
<script>
function filtrarCidadaos() {
    const filtro = document.getElementById('filtroCidadao').value.toLowerCase();
    const normalRows = document.querySelectorAll('#tabelaCidadaosNormais tr');
    const temporarioRows = document.querySelectorAll('#tabelaCidadaosTemporarios tr');

    [...normalRows, ...temporarioRows].forEach(row => {
        const texto = row.innerText.toLowerCase();
        row.style.display = texto.includes(filtro) ? '' : 'none';
    });
}

function selecionarCidadao(id, nome, tipo) {
    document.getElementById('cidadaoSelecionado').value = id;
    document.getElementById('tipoCidadaoSelecionado').value = tipo;
    document.getElementById('nomeCidadaoSelecionado').innerText = nome;

    document.getElementById('infoCidadao').classList.remove('hidden');
    document.getElementById('camposNormal').classList.toggle('hidden', tipo !== 'normal');
    document.getElementById('camposTemporario').classList.toggle('hidden', tipo !== 'temporario');
}
</script>
@endsection
