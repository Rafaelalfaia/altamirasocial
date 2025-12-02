<?php

namespace App\Http\Controllers\Restaurante\Coordenador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendaRestaurante;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


class RelatorioRestauranteController extends Controller
{
    public function index(Request $request)
{
    // Título e período
    $periodo = $request->get('periodo', 'dia');
    $titulo = 'Relatório Geral do Dia';
    $dataInicial = Carbon::today();
    $dataFinal = Carbon::now();

    if ($periodo === 'semana') {
        $titulo = 'Relatório Geral da Semana';
        $dataInicial = Carbon::now()->startOfWeek();
    } elseif ($periodo === 'mes') {
        $titulo = 'Relatório Geral do Mês';
        $dataInicial = Carbon::now()->startOfMonth();
    } elseif ($request->filled('data_inicial') && $request->filled('data_final')) {
        $titulo = 'Relatório Personalizado';
        $dataInicial = Carbon::parse($request->input('data_inicial'));
        $dataFinal = Carbon::parse($request->input('data_final'))->endOfDay();
    }

    if ($periodo === 'semana') {
        $titulo = 'Relatório Geral da Semana';
        $dataInicial = Carbon::now()->subDays(7)->startOfDay();
    } elseif ($periodo === 'mes') {
        $titulo = 'Relatório Geral do Mês';
        $dataInicial = Carbon::now()->startOfMonth();
    } elseif ($periodo === '30dias') {
        $titulo = 'Relatório dos Últimos 30 Dias';
        $dataInicial = Carbon::now()->subDays(30)->startOfDay();
    } elseif ($periodo === '6meses') {
        $titulo = 'Relatório dos Últimos 6 Meses';
        $dataInicial = Carbon::now()->subMonths(6)->startOfDay();
    }


    // Buscar vendas no período
    $vendas = VendaRestaurante::with(['cidadao', 'cidadaoTemporario', 'user.roles', 'restaurante'])
        ->whereBetween('data_venda', [$dataInicial, $dataFinal])
        ->get();

    // Métricas gerais
    $metricas = $this->getMetricasGerais($vendas);

    // Dados por restaurante (agrupamento com subdetalhes)
    $dadosPorRestaurante = $this->getDadosPorRestaurante($vendas);

    // Dados por atendente com nome e restaurante via SQL (dinâmico e preciso)
    $dadosPorAtendente = $this->getDadosPorAtendente($dataInicial, $dataFinal);

    // Labels e valores dos gráficos
    $labelsAtendentes = collect($dadosPorAtendente)->pluck('atendente')->toArray();
    $valoresPratos = collect($dadosPorAtendente)->pluck('total_pratos')->toArray();

    $labelsRestaurantes = collect($dadosPorRestaurante)->pluck('restaurante.nome')->map(fn($n) => $n ?? 'Desconhecido')->toArray();
    $valoresRestaurantes = collect($dadosPorRestaurante)->pluck('total_vendas')->toArray();

    // Gráficos
    $graficoPagamentos = $this->gerarGraficoUrl('Formas de Pagamento', ['PIX', 'Dinheiro'], [
        $metricas['pagamento_pix'], $metricas['pagamento_dinheiro']
    ]);

    $graficoClientes = $this->gerarGraficoUrl('Tipo de Cliente', ['Normais', 'Temporários'], [
        $metricas['clientes_normais'], $metricas['clientes_temporarios']
    ]);

    $graficoConsumo = $this->gerarGraficoUrl('Tipo de Consumo', ['Local', 'Retirada'], [
        $metricas['consumo_local'], $metricas['consumo_retirada']
    ]);

    $graficoComparativoRestaurantes = $this->gerarGraficoUrl(
        'Vendas por Restaurante',
        $labelsRestaurantes,
        $valoresRestaurantes
    );

    $graficoComparativoAtendentes = $this->gerarGraficoUrl(
        'Pratos Servidos por Atendente',
        $labelsAtendentes,
        $valoresPratos
    );

    return view('restaurante.coordenador.relatorios.index', [
        'periodo' => $periodo,
        'titulo' => $titulo,
        'dataInicial' => $dataInicial->format('d/m/Y'),
        'dataFinal' => $dataFinal->format('d/m/Y'),
        'metricas' => $metricas,
        'dadosPorRestaurante' => $dadosPorRestaurante,
        'dadosPorAtendente' => $dadosPorAtendente,
        'graficoPagamentos' => $graficoPagamentos,
        'graficoClientes' => $graficoClientes,
        'graficoConsumo' => $graficoConsumo,
        'graficoComparativoRestaurantes' => $graficoComparativoRestaurantes,
        'graficoComparativoAtendentes' => $graficoComparativoAtendentes,
    ]);
}


    private function getMetricasGerais($vendas)
    {
        return [
            'total_vendas' => $vendas->count(),
            'total_pratos' => $vendas->sum('numero_pratos'),
            'clientes_normais' => $vendas->where('tipo_cliente', 'cidadao')->count(),
            'clientes_temporarios' => $vendas->where('tipo_cliente', 'temporario')->count(),
            'pagamento_pix' => $vendas->where('forma_pagamento', 'pix')->count(),
            'pagamento_dinheiro' => $vendas->where('forma_pagamento', 'dinheiro')->count(),
            'consumo_local' => $vendas->where('tipo_consumo', 'local')->count(),
            'consumo_retirada' => $vendas->where('tipo_consumo', 'retirada')->count(),
            'estudantes' => $vendas->where('estudante', true)->count(),
            'doacoes' => $vendas->where('doacao', true)->count(),
        ];

    }

    private function getDadosPorAtendente($dataInicial, $dataFinal)
{
    return \DB::table('venda_restaurantes as vr')
        ->join('users as u', 'u.id', '=', 'vr.user_id')
        ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
        ->join('roles as r', 'r.id', '=', 'mhr.role_id')
        ->leftJoin('restaurante_user as ru', 'ru.user_id', '=', 'u.id')
        ->leftJoin('restaurantes as res', 'res.id', '=', 'ru.restaurante_id')
        ->where('r.name', 'Atendente Restaurante')
        ->whereBetween('vr.data_venda', [$dataInicial, $dataFinal])
        ->select(
            'u.name as atendente',
            'res.nome as restaurante',
            \DB::raw('COUNT(vr.id) as total_vendas'),
            \DB::raw('SUM(vr.numero_pratos) as total_pratos')
        )
        ->groupBy('u.id', 'u.name', 'res.nome')
        ->get()
        ->map(function ($item) {
            return [
                'atendente' => $item->atendente,
                'restaurante' => $item->restaurante ?? 'Desconhecido',
                'total_vendas' => $item->total_vendas,
                'total_pratos' => $item->total_pratos,
            ];
        });
}



    private function getDadosPorRestaurante($vendas)
    {
        return $vendas->groupBy('restaurante_id')
            ->map(function ($grupo, $restauranteId) {
                $restaurante = $grupo->first()->restaurante;

                return [
                    'restaurante' => $restaurante,
                    'total_vendas' => $grupo->count(),
                    'total_pratos' => $grupo->sum('numero_pratos'),
                    'pix' => $grupo->where('forma_pagamento', 'pix')->count(),
                    'dinheiro' => $grupo->where('forma_pagamento', 'dinheiro')->count(),
                    'normais' => $grupo->where('tipo_cliente', 'cidadao')->count(),
                    'temporarios' => $grupo->where('tipo_cliente', 'temporario')->count(),
                    'atendentes' => $grupo->groupBy('user_id')->filter(function ($vendasAtendente) {
                        $usuario = $vendasAtendente->first()->user;
                        return $usuario && $usuario->hasRole('Atendente Restaurante');
                    })->map(function ($vendasAtendente) {
                        $usuario = $vendasAtendente->first()->user;
                        return [
                            'nome' => $usuario?->name ?? 'Desconhecido',
                            'total_vendas' => $vendasAtendente->count(),
                            'total_pratos' => $vendasAtendente->sum('numero_pratos'),
                        ];
                    })->values()->all(),

                ];
            })->values()->all();
    }

    private function gerarDadosAtendentes($dataInicial, $dataFinal)
    {
        return \DB::table('venda_restaurantes as vr')
            ->join('users as u', 'u.id', '=', 'vr.user_id')
            ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
            ->join('roles as r', 'r.id', '=', 'mhr.role_id')
            ->where('r.name', 'Atendente Restaurante')
            ->whereBetween('vr.data_venda', [$dataInicial, $dataFinal])
            ->select(
                'u.name as nome',
                \DB::raw('COUNT(vr.id) as total_vendas'),
                \DB::raw('SUM(vr.numero_pratos) as total_pratos')
            )
            ->groupBy('u.id', 'u.name')
            ->get()
            ->map(fn($item) => [
                'nome' => $item->nome,
                'total_vendas' => $item->total_vendas,
                'total_pratos' => $item->total_pratos,
            ]);
    }

    private function gerarGraficoUrl(string $titulo, array $labels, array $valores)
    {
        $cores = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1', '#ec4899', '#14b8a6', '#f43f5e', '#8b5cf6', '#0ea5e9'];
        $count = min(count($labels), count($valores));
        $labels = array_slice($labels, 0, $count);
        $valores = array_slice($valores, 0, $count);
        $coresUsadas = array_slice($cores, 0, $count);

        $config = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => $titulo,
                    'data' => $valores,
                    'backgroundColor' => $coresUsadas
                ]]
            ],
            'options' => [
                'plugins' => [
                    'legend' => ['display' => false],
                    'title' => ['display' => true, 'text' => $titulo]
                ],
                'scales' => ['y' => ['beginAtZero' => true]]
            ]
        ];

        return 'https://quickchart.io/chart?c=' . urlencode(json_encode($config));
    }

    public function gerarPdf(Request $request)
{
    $dataInicial = Carbon::parse($request->input('data_inicial', now()->startOfMonth()));
    $dataFinal = Carbon::parse($request->input('data_final', now()))->endOfDay();

    // Busca todas as vendas no período
    $vendas = VendaRestaurante::with(['cidadao', 'cidadaoTemporario', 'user.roles', 'restaurante'])
        ->whereBetween('data_venda', [$dataInicial, $dataFinal])
        ->get();

    // Métricas gerais
    $metricas = $this->getMetricasGerais($vendas);

    // Dados por atendente com nome e restaurante (SQL direto para precisão)
    $dadosPorAtendente = $this->getDadosPorAtendente($dataInicial, $dataFinal);

    // Dados do coordenador logado e restaurante vinculado
    $usuario = auth()->user()->name;
    $restaurante = auth()->user()->restaurantes()->first();

    // Geração do PDF
    $pdf = \PDF::loadView('restaurante.coordenador.relatorios.pdf', [
        'titulo' => 'Relatório de Vendas',
        'restaurante' => $restaurante,
        'usuario' => $usuario,
        'data_geracao' => now()->format('d/m/Y H:i'),
        'data_inicial' => $dataInicial->format('d/m/Y'),
        'data_final' => $dataFinal->format('d/m/Y'),
        'vendas' => $vendas,
        'dadosPorAtendente' => $dadosPorAtendente,
        'totalVendas' => $metricas['total_vendas'],
        'totalPratos' => $metricas['total_pratos'],
        'normais' => $metricas['clientes_normais'],
        'temporarios' => $metricas['clientes_temporarios'],
        'estudantes' => $metricas['estudantes'],
        'doacoes' => $metricas['doacoes'],
        'pix' => $metricas['pagamento_pix'],
        'dinheiro' => $metricas['pagamento_dinheiro'],
        'local' => $metricas['consumo_local'],
        'retirada' => $metricas['consumo_retirada'],
    ]);

    return $pdf->download('relatorio-vendas.pdf');
}


}
