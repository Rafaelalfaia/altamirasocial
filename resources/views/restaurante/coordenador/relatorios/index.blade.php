@extends('layouts.app')

@section('title', '📈 Relatórios dos Restaurantes')

@section('content')
<div class="max-w-7xl mx-auto space-y-10">

    {{-- Botões de Período --}}
    @php
        $periodos = [
            'dia' => 'Últimas 24h',
            'semana' => 'Últimos 7 dias',
            '30dias' => 'Últimos 30 dias',
            'mes' => 'Este mês',
            '6meses' => 'Últimos 6 meses',
        ];
        $periodoAtual = request('periodo', 'dia');
    @endphp

    <div class="flex flex-wrap gap-2 mb-6">
        @foreach ($periodos as $key => $label)
            <a href="{{ route('restaurante.coordenador.relatorios.index', ['periodo' => $key]) }}"
            class="px-4 py-2 rounded text-sm font-medium shadow transition
                    {{ $periodoAtual === $key
                        ? 'bg-green-800 text-white'
                        : 'bg-white text-green-800 border border-green-800 hover:bg-green-100' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>



    {{-- Cabeçalho --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-green-800">📈 Relatórios Gerais</h1>
            <p class="text-sm text-gray-600">Acompanhe o desempenho dos restaurantes e atendentes.</p>
            <p class="text-xs text-gray-400">Período: {{ $dataInicial }} até {{ $dataFinal }}</p>
        </div>



        {{-- Exportação --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('restaurante.coordenador.relatorios.pdf', ['periodo' => 'dia']) }}"
               class="bg-blue-600 text-white px-4 py-2 rounded text-sm shadow hover:bg-blue-700">PDF do Dia</a>
            <a href="{{ route('restaurante.coordenador.relatorios.pdf', ['periodo' => 'semana']) }}"
               class="bg-yellow-600 text-white px-4 py-2 rounded text-sm shadow hover:bg-yellow-700">PDF da Semana</a>
            <a href="{{ route('restaurante.coordenador.relatorios.pdf', ['periodo' => 'mes']) }}"
               class="bg-green-600 text-white px-4 py-2 rounded text-sm shadow hover:bg-green-700">PDF do Mês</a>
        </div>
    </div>

    {{-- Painel de Métricas Gerais --}}
    <div class="bg-white p-6 rounded shadow space-y-6">
        <h2 class="text-lg font-bold text-gray-700">📊 Totais Gerais</h2>

        @php
            $labels = [
                'total_vendas' => 'Total de Vendas',
                'total_pratos' => 'Total de Pratos Servidos',
                'clientes_normais' => 'Clientes Normais',
                'clientes_temporarios' => 'Clientes Temporários',
                'pagamento_pix' => 'Pagamentos via PIX',
                'pagamento_dinheiro' => 'Pagamentos em Dinheiro',
                'consumo_local' => 'Consumo no Local',
                'consumo_retirada' => 'Consumo para Retirada',
                'estudantes' => 'Estudantes',
                'doacoes' => 'Doações',
            ];
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 text-sm text-gray-800">
            @foreach ($metricas as $chave => $valor)
                <div class="bg-gray-100 p-3 rounded shadow-sm">
                    <strong>{{ $labels[$chave] ?? ucfirst(str_replace('_', ' ', $chave)) }}:</strong><br>{{ $valor }}
                </div>
            @endforeach
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @isset($graficoPagamentos)
            <x-relatorio.grafico titulo="💰 Formas de Pagamento" :src="$graficoPagamentos" />
        @endisset

        @isset($graficoClientes)
            <x-relatorio.grafico titulo="👥 Tipos de Cliente" :src="$graficoClientes" />
        @endisset

        @isset($graficoConsumo)
            <x-relatorio.grafico titulo="🍽️ Tipo de Consumo" :src="$graficoConsumo" />
        @endisset

        @isset($graficoComparativoRestaurantes)
            <x-relatorio.grafico titulo="🏢 Vendas por Restaurante" :src="$graficoComparativoRestaurantes" />
        @endisset

        @isset($graficoComparativoAtendentes)
            <x-relatorio.grafico titulo="👨‍🍳 Pratos por Atendente" :src="$graficoComparativoAtendentes" />
        @endisset
    </div>

    {{-- Detalhamento por Restaurante --}}
    <div class="bg-white p-6 rounded shadow mt-10">
        <h2 class="text-lg font-bold text-gray-700">🏬 Detalhamento por Restaurante</h2>

        @forelse ($dadosPorRestaurante as $grupo)
            <div class="border-t pt-4 mt-4 space-y-2">
                <h3 class="font-semibold text-green-700">
                    {{ $grupo['restaurante'] && isset($grupo['restaurante']->nome) ? $grupo['restaurante']->nome : 'Restaurante não vinculado' }}
                </h3>


                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 text-sm">
                    <div><strong>Vendas:</strong> {{ $grupo['total_vendas'] }}</div>
                    <div><strong>Pratos:</strong> {{ $grupo['total_pratos'] }}</div>
                    <div><strong>PIX:</strong> {{ $grupo['pix'] }}</div>
                    <div><strong>Dinheiro:</strong> {{ $grupo['dinheiro'] }}</div>
                    <div><strong>Normais:</strong> {{ $grupo['normais'] }}</div>
                    <div><strong>Temporários:</strong> {{ $grupo['temporarios'] }}</div>
                </div>

                @if(count($grupo['atendentes']))
                    <table class="mt-4 w-full text-sm border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 text-left">Atendente</th>
                                <th class="p-2 text-right">Vendas</th>
                                <th class="p-2 text-right">Pratos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($grupo['atendentes'] as $atendente)
                                <tr class="border-t">
                                    <td class="p-2">{{ $atendente['nome'] ?? 'Sem nome' }}</td>
                                    <td class="p-2 text-right">{{ $atendente['total_vendas'] }}</td>
                                    <td class="p-2 text-right">{{ $atendente['total_pratos'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @empty
            <p class="text-gray-500">Nenhum restaurante encontrado com dados no período.</p>
        @endforelse
    </div>

</div>
@endsection
