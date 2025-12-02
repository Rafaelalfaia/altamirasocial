<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Cidadao;
use App\Models\CidadaoTemporario;
use Carbon\Carbon;
use App\Models\Programa;
use App\Models\ProgramaInscricao;
use App\Models\DenunciaPrograma;
use App\Models\IndicacaoPrograma;
use App\Models\User;
use App\Models\Evolucao;
use App\Models\RespostaSolicitacao;
use App\Models\Solicitacao;
use App\Models\VendaRestaurante;
use App\Models\Emergencia;
use App\Models\RecebimentoEncaminhamento;
use App\Models\OrgaoPublico;



class RelatorioAdminController extends Controller
{
    /**
     * Gráfico de crescimento mensal dos cidadãos cadastrados
     */
    public function index()
    {
        $dataLimite = now()->subHours(48);
        $assistentesPlantao = User::role('Assistente')
            ->where('modo_plantao', true)
            ->where('updated_at', '>=', $dataLimite)
            ->count();

        return view('admin.relatorios.index', compact('assistentesPlantao'));
    }


     public function graficoCrescimentoCidadaos()
{
    $inicio = now()->subMonths(5)->startOfMonth(); // últimos 6 meses incluindo o atual
    $fim = now()->endOfMonth();

    $dadosBrutos = Cidadao::whereBetween('created_at', [$inicio, $fim])
        ->selectRaw("DATE_FORMAT(created_at, '%m/%Y') as mes, COUNT(*) as total")
        ->groupBy('mes')
        ->orderByRaw("STR_TO_DATE(mes, '%m/%Y')")
        ->pluck('total', 'mes')
        ->toArray();

    $meses = collect();
    for ($i = 5; $i >= 0; $i--) {
        $mesLabel = now()->subMonths($i)->format('m/Y');
        $meses->put($mesLabel, $dadosBrutos[$mesLabel] ?? 0);
    }

    return [
        'labels' => $meses->keys(),
        'data' => $meses->values(),
    ];
}


    /**
     * Gráfico de distribuição por gênero
     */
    public function graficoGeneroCidadaos()
    {
        $dados = Cidadao::select('genero', DB::raw('count(*) as total'))
            ->groupBy('genero')
            ->get();

        return [
            'labels' => $dados->pluck('genero'),
            'data' => $dados->pluck('total'),
        ];
    }

    /**
     * Gráfico de faixa etária (definida manualmente)
     */
    public function graficoFaixaEtariaCidadaos()
    {
        $faixas = [
            '0-12'   => [0, 12],
            '13-17'  => [13, 17],
            '18-29'  => [18, 29],
            '30-39'  => [30, 39],
            '40-59'  => [40, 59],
            '60+'    => [60, 150],
        ];

        $hoje = Carbon::now();
        $resultados = [];

        foreach ($faixas as $faixa => [$min, $max]) {
            $total = Cidadao::whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE())'), [$min, $max])->count();
            $resultados[$faixa] = $total;
        }

        return [
            'labels' => array_keys($resultados),
            'data' => array_values($resultados),
        ];
    }

    /**
     * Gráfico de PCD (Pessoas com deficiência)
     */
    public function graficoPcdCidadaos()
    {
        $dados = Cidadao::where('pcd', true)
            ->select('tipo_deficiencia', DB::raw('count(*) as total'))
            ->groupBy('tipo_deficiencia')
            ->get();

        return [
            'labels' => $dados->pluck('tipo_deficiencia')->map(fn($t) => $t ?: 'Não Informado'),
            'data' => $dados->pluck('total'),
        ];
    }


    /**
     * Gráfico de cidadãos por região
     * (baseado no relacionamento com bairros -> cidades -> estados)
     */
    public function graficoCidadaosPorRegiao()
    {
        $dados = Cidadao::with('bairro')
            ->select('regiao', DB::raw('count(*) as total'))
            ->groupBy('regiao')
            ->get();

        return [
            'labels' => $dados->pluck('regiao'),
            'data' => $dados->pluck('total'),
        ];
    }

    /**
     * Gráfico de preenchimento dos dados cadastrais (porcentagem)
     */
    public function graficoPreenchimentoCadastro()
    {
        $total = Cidadao::count();

        $completos = Cidadao::whereNotNull('nome')
            ->whereNotNull('data_nascimento')
            ->whereNotNull('telefone')
            ->whereNotNull('bairro_id')
            ->whereNotNull('renda_total_familiar')
            ->whereNotNull('possui_deficiencia')
            ->count();

        return [
            'labels' => ['Preenchido', 'Incompleto'],
            'data' => [$completos, $total - $completos],
        ];
    }


    /**
     * Gráfico de cidadãos temporários por mês
     */
    public function graficoCidadaosTemporarios()
    {
        $dados = CidadaoTemporario::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return [
            'labels' => $dados->pluck('mes'),
            'data' => $dados->pluck('total'),
        ];
    }

    public function graficoInscricoesPorPrograma()
    {
        $dados = ProgramaInscricao::with('programa')
            ->select('programa_id', DB::raw('COUNT(*) as total'))
            ->groupBy('programa_id')
            ->get();

        return [
            'labels' => $dados->map(fn($item) => optional($item->programa)->nome ?? 'Desconhecido'),
            'data' => $dados->pluck('total'),
        ];
    }

    public function graficoStatusInscricoes()
    {
        $dados = ProgramaInscricao::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        return [
            'labels' => $dados->pluck('status'),
            'data' => $dados->pluck('total'),
        ];
    }

    public function graficoDenunciasPorPrograma()
    {
        $dados = DenunciaPrograma::with('programa')
            ->select('programa_id', DB::raw('COUNT(*) as total'))
            ->groupBy('programa_id')
            ->get();

        return [
            'labels' => $dados->map(fn($item) => optional($item->programa)->nome ?? 'Desconhecido'),
            'data' => $dados->pluck('total'),
        ];
    }

    public function graficoIndicacoesPorPrograma()
    {
        $dados = IndicacaoPrograma::with('programa')
            ->select('programa_id', DB::raw('COUNT(*) as total'))
            ->groupBy('programa_id')
            ->get();

        return [
            'labels' => $dados->map(fn($item) => optional($item->programa)->nome ?? 'Desconhecido'),
            'data' => $dados->pluck('total'),
        ];
    }

    public function graficoRegioesInscricoes()
    {
        $dados = ProgramaInscricao::select('regiao', DB::raw('COUNT(*) as total'))
            ->groupBy('regiao')
            ->get();

        return [
            'labels' => $dados->pluck('regiao'),
            'data' => $dados->pluck('total'),
        ];
    }

    public function graficoMediaRendaBeneficiarios()
    {
        $dados = Programa::with(['inscricoes.cidadao'])
            ->get()
            ->map(function ($programa) {
                $rendimentos = $programa->inscricoes
                    ->filter(fn($inscricao) =>
                        $inscricao->cidadao &&
                        $inscricao->cidadao->renda_total_familiar !== null &&
                        $inscricao->cidadao->pessoas_na_residencia > 0
                    )
                    ->map(function ($inscricao) {
                        $cidadao = $inscricao->cidadao;
                        return $cidadao->renda_total_familiar / max($cidadao->pessoas_na_residencia, 1);
                    });

                return [
                    'programa' => $programa->nome,
                    'media_per_capita' => $rendimentos->count() ? round($rendimentos->avg(), 2) : 0,
                ];
            });

        return [
            'labels' => $dados->pluck('programa'),
            'data' => $dados->pluck('media_per_capita'),
        ];
    }


    public function graficoEvolucoesPorAssistente()
    {
        $dados = User::role('Assistente')
            ->withCount('evolucoes')
            ->orderByDesc('evolucoes_count')
            ->limit(5)
            ->get();

        return [
            'labels' => $dados->pluck('name'),
            'data' => $dados->pluck('evolucoes_count'),
        ];
    }

    public function graficoAssistenteMaisAtivo()
    {
        $dados = User::role('Assistente')
            ->withCount('evolucoes')
            ->orderByDesc('evolucoes_count')
            ->limit(10)
            ->get();

        return [
            'labels' => $dados->pluck('name'),
            'data' => $dados->pluck('evolucoes_count'),
        ];
    }
    public function graficoPlantaoAtivo()
    {
        $dataLimite = now()->subHours(48);

        $total = User::role('Assistente')
            ->where('modo_plantao', true)
            ->where('updated_at', '>=', $dataLimite)
            ->count();

        $inativos = User::role('Assistente')->count() - $total;

        return [
            'labels' => ['Em plantão', 'Fora do plantão'],
            'data' => [$total, $inativos],
        ];
    }
    public function graficoRespostasSolicitacoesAssistente()
    {
        $dados = RespostaSolicitacao::with('user')
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'labels' => $dados->map(fn($r) => optional($r->user)->name ?? 'Desconhecido'),
            'data' => $dados->pluck('total'),
        ];
    }
    public function graficoRankingAssistentesProdutividade()
    {
        $assistentes = User::role('Assistente')->get();

        $ranking = $assistentes->map(function ($user) {
            $pontuacao = ($user->evolucoes()->count() * 2)
                        + RespostaSolicitacao::where('user_id', $user->id)->count()
                        + ($user->modo_plantao ? 1 : 0);

            return [
                'nome' => $user->name,
                'pontuacao' => $pontuacao,
            ];
        })->sortByDesc('pontuacao')->take(5)->values();

        return [
            'labels' => $ranking->pluck('nome'),
            'data' => $ranking->pluck('pontuacao'),
        ];
    }

    public function graficoVendasPorDia()
    {
        $dados = VendaRestaurante::selectRaw('DATE(created_at) as dia, COUNT(*) as total')
            ->whereDate('created_at', '>=', now()->subDays(15))
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        return [
            'labels' => $dados->pluck('dia'),
            'data' => $dados->pluck('total'),
        ];
    }
    public function graficoTipoConsumo()
    {
        $dados = VendaRestaurante::select('tipo_consumo', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo_consumo')
            ->get();

        return [
            'labels' => $dados->pluck('tipo_consumo'),
            'data' => $dados->pluck('total'),
        ];
    }
    public function graficoFormasPagamento()
    {
        $dados = VendaRestaurante::select('forma_pagamento', DB::raw('COUNT(*) as total'))
            ->groupBy('forma_pagamento')
            ->get();

        return [
            'labels' => $dados->pluck('forma_pagamento'),
            'data' => $dados->pluck('total'),
        ];
    }
    public function graficoEmergenciasPorPeriodo()
    {
        $dados = Emergencia::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mes, COUNT(*) as total')
            ->whereDate('created_at', '>=', now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return [
            'labels' => $dados->pluck('mes'),
            'data' => $dados->pluck('total'),
        ];
    }
    public function graficoEncaminhamentosPorOrgao()
    {
        $dados = RecebimentoEncaminhamento::with('orgao')
            ->select('orgao_publico_id', DB::raw('COUNT(*) as total'))
            ->groupBy('orgao_publico_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'labels' => $dados->map(fn($r) => optional($r->orgao)->nome ?? 'Desconhecido'),
            'data' => $dados->pluck('total'),
        ];
    }

}
