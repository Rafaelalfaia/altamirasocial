<?php

namespace App\Http\Controllers\Restaurante\Atendente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VendaRestaurante;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\VendasAtendenteExport;
use Maatwebsite\Excel\Facades\Excel;
class RelatorioRestauranteController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Períodos base
        $hoje = Carbon::today();
        $inicioSemana = Carbon::now()->startOfWeek();
        $inicioMes = Carbon::now()->startOfMonth();

        // Coleta as vendas do atendente por período
        $vendasDia = VendaRestaurante::where('user_id', $user->id)
            ->whereDate('data_venda', $hoje)
            ->with(['cidadao', 'cidadaoTemporario'])
            ->get();

        $vendasSemana = VendaRestaurante::where('user_id', $user->id)
            ->whereBetween('data_venda', [$inicioSemana, now()])
            ->with(['cidadao', 'cidadaoTemporario'])
            ->get();

        $vendasMes = VendaRestaurante::where('user_id', $user->id)
            ->whereBetween('data_venda', [$inicioMes, now()])
            ->with(['cidadao', 'cidadaoTemporario'])
            ->get();

        // Função anônima para calcular métricas
        $calcularMetricas = function ($vendas) {
            return [
                'total_vendas' => $vendas->count(),
                'total_pratos' => $vendas->sum('numero_pratos'),
                'clientes_normais' => $vendas->where('tipo_cliente', 'cidadao')->count(),
                'clientes_temporarios' => $vendas->where('tipo_cliente', 'temporario')->count(),
                'pagamento_pix' => $vendas->where('forma_pagamento', 'pix')->count(),
                'pagamento_dinheiro' => $vendas->where('forma_pagamento', 'dinheiro')->count(),
                'consumo_local' => $vendas->where('tipo_consumo', 'local')->count(),
                'consumo_retirada' => $vendas->where('tipo_consumo', 'retirada')->count(),
                'doacoes' => $vendas->where('doacao', true)->count(),
                'estudantes' => $vendas->where('estudante', true)->count(),
            ];
        };

        // Métricas por período
        $metricasDia = $calcularMetricas($vendasDia);
        $metricasSemana = $calcularMetricas($vendasSemana);
        $metricasMes = $calcularMetricas($vendasMes);

        // Retorna view com todos os dados necessários
        return view('restaurante.atendente.relatorios.index', [
            'vendasDia' => $vendasDia,
            'vendasSemana' => $vendasSemana,
            'vendasMes' => $vendasMes,
            'metricasDia' => $metricasDia,
            'metricasSemana' => $metricasSemana,
            'metricasMes' => $metricasMes,
        ]);
    }

    public function gerarPdf(Request $request)
    {
        $user = Auth::user();
        $periodo = $request->get('periodo', 'dia');

        switch ($periodo) {
            case 'semana':
                $titulo = 'Relatório de Vendas da Semana';
                $inicio = Carbon::now()->startOfWeek();
                break;
            case 'mes':
                $titulo = 'Relatório de Vendas do Mês';
                $inicio = Carbon::now()->startOfMonth();
                break;
            default:
                $titulo = 'Relatório de Vendas do Dia';
                $inicio = Carbon::today();
                break;
        }

        // Busca das vendas do atendente no período
        $vendas = VendaRestaurante::where('user_id', $user->id)
            ->whereBetween('data_venda', [$inicio, now()])
            ->with(['cidadao', 'cidadaoTemporario'])
            ->orderBy('data_venda', 'desc')
            ->get();

        $restaurante = $user->restaurantes()->first();

        // Cálculo das métricas para exibir no PDF
        $metricas = [
            'totalVendas' => $vendas->count(),
            'totalPratos' => $vendas->sum('numero_pratos'),
            'normais' => $vendas->where('tipo_cliente', 'cidadao')->count(),
            'temporarios' => $vendas->where('tipo_cliente', 'temporario')->count(),
            'pix' => $vendas->where('forma_pagamento', 'pix')->count(),
            'dinheiro' => $vendas->where('forma_pagamento', 'dinheiro')->count(),
            'local' => $vendas->where('tipo_consumo', 'local')->count(),
            'retirada' => $vendas->where('tipo_consumo', 'retirada')->count(),
            'estudantes' => $vendas->where('estudante', true)->count(),
            'doacoes' => $vendas->where('doacao', true)->count(),
        ];

        // Gerar gráficos
        $graficoPagamentos = $this->gerarGraficoUrl('Formas de Pagamento', ['PIX', 'Dinheiro'], [$metricas['pix'], $metricas['dinheiro']]);
        $graficoClientes = $this->gerarGraficoUrl('Tipo de Cliente', ['Normais', 'Temporários'], [$metricas['normais'], $metricas['temporarios']]);

        // Data para exibir no relatório e nome do arquivo
        $dataAtual = Carbon::now()->format('d/m/Y H:i');
        $nomeArquivo = 'relatorio_' . $periodo . '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        // Gera e retorna o PDF
        $pdf = Pdf::loadView('restaurante.atendente.relatorios.pdf_with_chart', [
            'vendas' => $vendas,
            'titulo' => $titulo,
            'usuario' => $user->name,
            'data_geracao' => $dataAtual,
            'restaurante' => $restaurante?->nome ?? 'N/D',
            'graficoPagamentos' => $graficoPagamentos,
            'graficoClientes' => $graficoClientes,
        ] + $metricas);

        return $pdf->download($nomeArquivo);
    }


    private function gerarGraficoUrl(string $titulo, array $labels, array $valores)
    {
        // Garante que tenha cores suficientes
        $cores = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

        // Corrige mismatch entre labels e valores
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
                    'title' => [
                        'display' => true,
                        'text' => $titulo,
                        'font' => ['size' => 14]
                    ]
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true
                    ]
                ]
            ]
        ];

        $json = json_encode($config);
        return 'https://quickchart.io/chart?c=' . urlencode($json);
    }

    public function exportarExcel(Request $request)
    {
        $user = Auth::user();
        $periodo = $request->get('periodo', 'dia');

        switch ($periodo) {
            case 'semana':
                $inicio = Carbon::now()->startOfWeek();
                break;
            case 'mes':
                $inicio = Carbon::now()->startOfMonth();
                break;
            default:
                $inicio = Carbon::today();
                break;
        }

        $vendas = VendaRestaurante::where('user_id', $user->id)
            ->whereBetween('data_venda', [$inicio, now()])
            ->with(['cidadao', 'cidadaoTemporario'])
            ->get();

        $export = new VendasAtendenteExport($vendas);
        return Excel::download($export, "vendas_{$periodo}_atendente.xlsx");
    }


}
