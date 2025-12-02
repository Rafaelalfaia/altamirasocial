<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Models\User;
use App\Models\Evolucao;
use App\Models\Emergencia;
use App\Models\Solicitacao;
use App\Models\DenunciaPrograma;
use App\Models\IndicacaoPrograma;

use App\Models\Cidadao;
use App\Models\ProgramaInscricao;
use App\Models\Acompanhamento;
use App\Models\Programa;

use App\Models\RecebimentoEncaminhamento;
use App\Models\OrgaoPublico;




class RelatorioController extends Controller
{
    public function index()
    {
        return view('coordenador.relatorios.index', [
            // Etapa 1: Assistentes
            'assistentesCriados'         => $this->getAssistentesCriadosPorData(),
            'visitasPorAssistente'       => $this->getVisitasPorAssistente(),
            'plantaoOcorrencias'         => $this->getOcorrenciasEmPlantao(),
            'solicitacoesAssistentes'    => $this->getSolicitacoesAssistentes(),
            'rankingVisitas'             => $this->getRankingVisitas(),
            'denunciasPorAssistente'     => $this->getDenunciasPorAssistente(),
            'indicacoesPorAssistente'    => $this->getIndicacoesPorAssistente(),

            //Etapa 2: Cidadão

            'totalCidadaos'               => $this->getTotalCidadaos(),
            'percentualCidadaosPrograma' => $this->getPercentualCidadaosComPrograma(),
            'generoCidadaos'              => $this->getCidadaosPorGenero(),
            'faixaEtariaCidadaos'        => $this->getCidadaosPorFaixaEtaria(),
            'cidadaosPorBairro'          => $this->getCidadaosPorBairro(),
            'statusAcompanhamentos'      => $this->getStatusAcompanhamentos(),

            // Etapa 3 – Programas Sociais
            'totalProgramas'            => $this->getTotalProgramas(),
            'totalInscritosProgramas'   => $this->getTotalInscritosEmProgramas(),
            'mediaRendaPrograma'        => $this->getMediaRendaPorPrograma(),
            'mediaRendaPerCapitaPorPrograma' => $this->getMediaRendaPerCapitaPorPrograma(),
            'evolucoesPorPrograma'      => $this->getEvolucoesPorPrograma(),
            'distribuicaoPorRegiao'     => $this->getDistribuicaoPorRegiao(),
            'distribuicaoPorBairro' => $this->getDistribuicaoPorBairro(),
            'statusInscricoes' => $this->getStatusInscricoes(),
            'aprovacoesPorPrograma' => $this->getAprovacoesPorPrograma(),


            // Etapa 4 - Geral
            'comparativoRecebimentosEncaminhamentos' => $this->getComparativoRecebimentosEncaminhamentos(),

            'horasPlantaoPorDia' => $this->getHorasPlantaoPorDia(),
            'emergenciasDurantePlantao' => $this->getEmergenciasDurantePlantao(),

            'recebimentosPorOrgao' => $this->getRecebimentosPorOrgao(),
            'encaminhamentosPorOrgao' => $this->getEncaminhamentosPorOrgao(),

           


        ]);
    }

    // 1. Assistentes Criados por mês
    public function getAssistentesCriadosPorData()
    {
        return User::role('Assistente')
            ->where('coordenador_id', Auth::id())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as mes, COUNT(*) as total")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
    }

    // 2. Total de visitas (evoluções) por assistente
    public function getVisitasPorAssistente()
    {
        return Evolucao::with('user')
            ->whereHas('user', fn($q) =>
                $q->where('coordenador_id', Auth::id())
                  ->role('Assistente')
            )
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->get();
    }

    // 3. Ocorrências em plantão (por dia)
    public function getOcorrenciasEmPlantao()
    {
        return Emergencia::whereNotNull('user_id')
            ->whereHas('user', fn($q) =>
                $q->where('coordenador_id', Auth::id())
                  ->role('Assistente')
            )
            ->selectRaw("DATE(created_at) as data, COUNT(*) as total")
            ->groupBy('data')
            ->orderBy('data')
            ->get();
    }

    // 4. Solicitações enviadas para assistentes
    public function getSolicitacoesAssistentes()
    {
        return Solicitacao::where('user_id', Auth::id())
            ->where('destinatario_tipo', 'Assistente')
            ->selectRaw("DATE(created_at) as data, COUNT(*) as total")
            ->groupBy('data')
            ->orderBy('data')
            ->get();
    }

    // 5. Ranking de assistentes que mais fizeram visitas
    public function getRankingVisitas()
    {
        return User::role('Assistente')
            ->where('coordenador_id', Auth::id())
            ->withCount('evolucoes')
            ->orderByDesc('evolucoes_count')
            ->take(5)
            ->get();
    }

    // 6. Denúncias feitas por assistente
    public function getDenunciasPorAssistente()
    {
        return DenunciaPrograma::with('assistente')
            ->whereHas('assistente', fn($q) =>
                $q->where('coordenador_id', Auth::id())
                  ->role('Assistente')
            )
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->get();
    }

    // 7. Indicações feitas por assistente
    public function getIndicacoesPorAssistente()
    {
        return IndicacaoPrograma::with('assistente')
            ->whereHas('assistente', fn($q) =>
                $q->where('coordenador_id', Auth::id())
                  ->role('Assistente')
            )
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->get();
    }

    //2. CIDADÃO 

        // 1. Total de Cidadãos
    public function getTotalCidadaos()
    {
        return Cidadao::count();
    }

    // 2. Percentual de Cidadãos em Programas
    public function getPercentualCidadaosComPrograma()
    {
        $total = Cidadao::count();
        $comInscricao = ProgramaInscricao::distinct('cidadao_id')->count('cidadao_id');

        return $total > 0 ? round(($comInscricao / $total) * 100, 2) : 0;
    }

    // 3. Distribuição por Gênero
    public function getCidadaosPorGenero()
    {
        return Cidadao::selectRaw('sexo, COUNT(*) as total')
            ->groupBy('sexo')
            ->get();
    }

    // 4. Faixa Etária
    public function getCidadaosPorFaixaEtaria()
    {
        return Cidadao::selectRaw("
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) < 12 THEN 'Criança (0–11)'
                    WHEN TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) BETWEEN 12 AND 17 THEN 'Adolescente (12–17)'
                    WHEN TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) BETWEEN 18 AND 29 THEN 'Jovem (18–29)'
                    WHEN TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) BETWEEN 30 AND 59 THEN 'Adulto (30–59)'
                    ELSE 'Idoso (60+)'
                END as faixa,
                COUNT(*) as total
            ")
            ->whereNotNull('data_nascimento')
            ->groupBy('faixa')
            ->orderByRaw("FIELD(faixa, 'Criança (0–11)', 'Adolescente (12–17)', 'Jovem (18–29)', 'Adulto (30–59)', 'Idoso (60+)')")
            ->get();
    }

    // 5. Cidadãos por Bairro
    public function getCidadaosPorBairro()
    {
        return Cidadao::with('bairro')
            ->selectRaw('bairro_id, COUNT(*) as total')
            ->groupBy('bairro_id')
            ->get()
            ->filter(fn($c) => $c->bairro); // evita bairros excluídos
    }

    // 6. Status de Acompanhamentos (Com ou Sem)
    public function getStatusAcompanhamentos()
    {
        $com = Acompanhamento::distinct('cidadao_id')->count('cidadao_id');
        $total = Cidadao::count();
        $sem = max($total - $com, 0);

        return collect([
            ['status' => 'Com Acompanhamento', 'total' => $com],
            ['status' => 'Sem Acompanhamento', 'total' => $sem],
        ]);
    }

    public function graficoPessoasComDeficiencia()
    {
        $comDeficiencia = Cidadao::where('pcd', 1)->count();
        $semDeficiencia = Cidadao::where('pcd', 0)->count();

        return response()->json([
            'labels' => ['Com Deficiência', 'Sem Deficiência'],
            'dados' => [$comDeficiencia, $semDeficiencia],
        ]);
    }


    public function graficoParticipacaoProgramas()
    {
        // Cidadãos que têm pelo menos uma inscrição
        $comPrograma = \App\Models\Cidadao::has('inscricoes')->count();

        // Cidadãos que não têm nenhuma inscrição
        $semPrograma = \App\Models\Cidadao::doesntHave('inscricoes')->count();

        return response()->json([
            'labels' => ['Participam de Programas', 'Não Participam'],
            'dados' => [$comPrograma, $semPrograma],
        ]);
    }


    public function graficoPCDsPorPrograma()
    {
        $coordenadorId = auth()->id();

        $programas = \App\Models\Programa::with(['inscricoes.cidadao' => function ($query) {
                $query->where('pcd', 1);
            }])
            ->where('user_id', $coordenadorId) // 👈 Limita aos programas do coordenador autenticado
            ->get();

        $labels = [];
        $dados = [];

        foreach ($programas as $programa) {
            $qtdPcds = $programa->inscricoes->filter(function ($inscricao) {
                return $inscricao->cidadao !== null;
            })->count();

            if ($qtdPcds > 0) {
                $labels[] = $programa->nome;
                $dados[] = $qtdPcds;
            }
        }

        return response()->json([
            'labels' => $labels,
            'dados' => $dados,
        ]);
    }




    // 3. PROGRAMA

    // Total de programas sociais
    protected function getTotalProgramas(): int
    {
        return Programa::count();
    }

    // Total de inscritos em programas
    protected function getTotalInscritosEmProgramas(): int
    {
        return ProgramaInscricao::distinct('cidadao_id')->count('cidadao_id');
    }

    // Média de renda dos inscritos por programa
    protected function getMediaRendaPorPrograma()
    {
        return \App\Models\Programa::where('user_id', Auth::id()) // 👈 filtra programas do coordenador
            ->with(['inscricoes.cidadao'])
            ->get()
            ->map(function ($programa) {
                $rendas = $programa->inscricoes->pluck('cidadao.renda_total_familiar')->filter();
                return [
                    'nome' => $programa->nome,
                    'media' => $rendas->count() ? round($rendas->avg(), 2) : 0,
                ];
            });
    }

    protected function getMediaRendaPerCapitaPorPrograma()
    {
        return \App\Models\Programa::where('user_id', Auth::id())
            ->with(['inscricoes.cidadao'])
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
                    'nome' => $programa->nome,
                    'media_per_capita' => $rendimentos->count() ? round($rendimentos->avg(), 2) : 0,
                ];
            });
    }


    // Total de evoluções por programa
    protected function getEvolucoesPorPrograma()
    {
        return \App\Models\Programa::where('user_id', Auth::id()) // 👈 filtra programas do coordenador
            ->with(['inscricoes.cidadao.acompanhamentos.evolucoes'])
            ->get()
            ->map(function ($programa) {
                $total = 0;
                foreach ($programa->inscricoes as $inscricao) {
                    $cidadao = $inscricao->cidadao;
                    if ($cidadao) {
                        foreach ($cidadao->acompanhamentos as $acomp) {
                            $total += $acomp->evolucoes->count();
                        }
                    }
                }
                return [
                    'nome' => $programa->nome,
                    'total' => $total,
                ];
            });
    }

    // Distribuição de inscritos por região
    protected function getDistribuicaoPorRegiao()
    {
        return ProgramaInscricao::with('programa')
            ->whereNotNull('regiao_origem')
            ->whereHas('programa', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->select('regiao_origem as regiao', DB::raw('count(*) as total'))
            ->groupBy('regiao_origem')
            ->get();
    }


    protected function getDistribuicaoPorBairro()
    {
        return ProgramaInscricao::with('cidadao.bairro', 'programa')
            ->whereHas('cidadao.bairro')
            ->whereHas('programa', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->get()
            ->groupBy(function ($inscricao) {
                return $inscricao->cidadao->bairro->nome ?? 'Não informado';
            })
            ->map(function ($grupo) {
                return [
                    'bairro' => $grupo->first()->cidadao->bairro->nome ?? 'Não informado',
                    'total' => $grupo->count()
                ];
            })
            ->values();
    }


    protected function getStatusInscricoes()
    {
        return ProgramaInscricao::with('programa')
            ->whereHas('programa', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
    }

    
    protected function getAprovacoesPorPrograma()
    {
        return ProgramaInscricao::where('status', 'aprovado')
            ->whereHas('programa', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->selectRaw('programa_id, COUNT(*) as total')
            ->groupBy('programa_id')
            ->with('programa:id,nome')
            ->get()
            ->map(fn ($i) => [
                'programa' => $i->programa->nome ?? 'N/D',
                'total' => $i->total
            ]);
    }
    
    
    //4. Geral


    
    protected function getComparativoRecebimentosEncaminhamentos()
    {
        return \App\Models\RecebimentoEncaminhamento::where('coordenador_id', Auth::id())
            ->selectRaw('tipo, COUNT(*) as total')
            ->groupBy('tipo')
            ->pluck('total', 'tipo');
    }


    protected function getHorasPlantaoPorDia()
    {
        return \App\Models\ModoPlantao::whereNotNull('inicio_plantao')
            ->whereNotNull('fim_plantao')
            ->selectRaw('DATE(inicio_plantao) as data, SUM(TIMESTAMPDIFF(SECOND, inicio_plantao, fim_plantao)/3600) as total_horas')
            ->groupBy(DB::raw('DATE(inicio_plantao)'))
            ->orderBy('data')
            ->get();
    }


    protected function getEmergenciasDurantePlantao()
    {
        return \App\Models\Emergencia::whereNotNull('user_id')
            ->whereDate('created_at', '>=', now()->subDays(30)) // últimos 30 dias
            ->selectRaw('DATE(created_at) as data, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('data')
            ->get();
    }

    protected function getRecebimentosPorOrgao()
    {
        return \App\Models\RecebimentoEncaminhamento::with('orgao')
            ->where('coordenador_id', Auth::id())
            ->where('tipo', 'recebimento') // somente recebidos
            ->selectRaw('orgao_publico_id, COUNT(*) as total')
            ->groupBy('orgao_publico_id')
            ->get()
            ->map(function ($item) {
                return [
                    'orgao' => $item->orgao->nome ?? 'Desconhecido',
                    'total' => $item->total,
                ];
            });
    }


    protected function getEncaminhamentosPorOrgao()
    {
        return \App\Models\RecebimentoEncaminhamento::with('orgao')
            ->where('coordenador_id', Auth::id())
            ->where('tipo', 'encaminhamento') // 🔁 agora filtrando só os encaminhamentos
            ->selectRaw('orgao_publico_id, COUNT(*) as total')
            ->groupBy('orgao_publico_id')
            ->get()
            ->map(function ($item) {
                return [
                    'orgao' => $item->orgao->nome ?? 'Desconhecido',
                    'total' => $item->total,
                ];
            });
    }
    

    

}
