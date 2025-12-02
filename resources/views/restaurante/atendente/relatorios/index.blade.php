@extends('layouts.app')

@section('title', '📊 Relatórios de Vendas do Atendente')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- Cabeçalho com título e botões --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-green-800">📊 Relatórios de Vendas</h1>
            <p class="text-sm text-gray-600">
                Veja suas vendas registradas por dia, semana ou mês, com métricas e tabelas detalhadas.
            </p>
        </div>

        {{-- Botões de exportação --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('restaurante.atendente.relatorios.pdf', ['periodo' => 'dia']) }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm shadow">📥 PDF do Dia</a>

            <a href="{{ route('restaurante.atendente.relatorios.pdf', ['periodo' => 'semana']) }}"
               class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 text-sm shadow">📥 PDF da Semana</a>

            <a href="{{ route('restaurante.atendente.relatorios.pdf', ['periodo' => 'mes']) }}"
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm shadow">📥 PDF do Mês</a>


        </div>
    </div>
    {{-- Métricas agrupadas por período --}}
    @foreach ([
        ['Dia', $metricasDia],
        ['Semana', $metricasSemana],
        ['Mês', $metricasMes]
    ] as [$titulo, $metricas])
    <div class="bg-white p-6 rounded shadow space-y-6">
        <h2 class="text-lg font-bold text-gray-700">📅 Métricas do {{ $titulo }}</h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 text-sm text-gray-800">
            <div class="bg-gray-100 p-3 rounded shadow-sm">
                <strong>Total de Vendas:</strong><br>{{ $metricas['total_vendas'] }}
            </div>
            <div class="bg-gray-100 p-3 rounded shadow-sm">
                <strong>Pratos Servidos:</strong><br>{{ $metricas['total_pratos'] }}
            </div>
            <div class="bg-gray-100 p-3 rounded shadow-sm">
                <strong>Clientes Normais:</strong><br>{{ $metricas['clientes_normais'] }}
            </div>
            <div class="bg-gray-100 p-3 rounded shadow-sm">
                <strong>Temporários:</strong><br>{{ $metricas['clientes_temporarios'] }}
            </div>
            <div class="bg-gray-100 p-3 rounded shadow-sm">
                <strong>PIX:</strong><br>{{ $metricas['pagamento_pix'] }}
            </div>
            <div class="bg-gray-100 p-3 rounded shadow-sm">
                <strong>Dinheiro:</strong><br>{{ $metricas['pagamento_dinheiro'] }}
            </div>
            <div class="bg-gray-100 p-3 rounded shadow-sm">
                <strong>Consumo Local:</strong><br>{{ $metricas['consumo_local'] }}
            </div>
            <div class="bg-gray-100 p-3 rounded shadow-sm">
                <strong>Retirada:</strong><br>{{ $metricas['consumo_retirada'] }}
            </div>
            <div class="bg-gray-100 p-3 rounded shadow-sm">
                <strong>Estudantes:</strong><br>{{ $metricas['estudantes'] }}
            </div>
            <div class="bg-gray-100 p-3 rounded shadow-sm">
                <strong>Doações:</strong><br>{{ $metricas['doacoes'] }}
            </div>
        </div>
        {{-- Tabela detalhada de vendas --}}
        @php
            $vendas = match($titulo) {
                'Dia' => $vendasDia,
                'Semana' => $vendasSemana,
                'Mês' => $vendasMes,
            };
        @endphp

        @if ($vendas->isEmpty())
            <p class="text-gray-500">Nenhuma venda registrada neste período.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-700 border mt-4">
                    <thead class="bg-gray-200 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2">Data</th>
                            <th class="px-4 py-2">Cliente</th>
                            <th class="px-4 py-2">CPF</th>
                            <th class="px-4 py-2">Tipo</th>
                            <th class="px-4 py-2">Pratos</th>
                            <th class="px-4 py-2">Estudante</th>
                            <th class="px-4 py-2">Doação</th>
                            <th class="px-4 py-2">Consumo</th>
                            <th class="px-4 py-2">Pagamento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($vendas as $venda)
                            <tr>
                                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($venda->data_venda)->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-2">{{ $venda->cidadao->nome ?? $venda->cidadaoTemporario->nome }}</td>
                                <td class="px-4 py-2">{{ $venda->cidadao->cpf ?? $venda->cidadaoTemporario->cpf }}</td>
                                <td class="px-4 py-2">{{ ucfirst($venda->tipo_cliente) }}</td>
                                <td class="px-4 py-2">{{ $venda->numero_pratos }}</td>
                                <td class="px-4 py-2">{{ $venda->estudante ? 'Sim' : 'Não' }}</td>
                                <td class="px-4 py-2">{{ $venda->doacao ? 'Sim' : 'Não' }}</td>
                                <td class="px-4 py-2">{{ ucfirst($venda->tipo_consumo) }}</td>
                                <td class="px-4 py-2">{{ ucfirst($venda->forma_pagamento) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

                {{-- Gráficos com QuickChart --}}
        @isset($graficos[$titulo])
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <div class="bg-white border p-4 rounded shadow text-center">
                <p class="font-semibold text-gray-700 mb-2">📊 Formas de Pagamento</p>
                <img src="{{ $graficos[$titulo]['pagamentos'] }}" alt="Gráfico de Pagamentos" class="mx-auto max-w-full">
            </div>
            <div class="bg-white border p-4 rounded shadow text-center">
                <p class="font-semibold text-gray-700 mb-2">👥 Tipos de Cliente</p>
                <img src="{{ $graficos[$titulo]['clientes'] }}" alt="Gráfico de Clientes" class="mx-auto max-w-full">
            </div>
        </div>
        @endisset

    </div>
    @endforeach
@endsection
