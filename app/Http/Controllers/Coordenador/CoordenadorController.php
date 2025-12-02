<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use App\Models\Cidadao;
use App\Models\User;
use App\Models\Programa;
use App\Models\Emergencia;
use App\Models\DenunciaPrograma;
use App\Models\IndicacaoPrograma;
use App\Models\ModoPlantao;
use App\Models\ProgramaInscricao;
use App\Models\Solicitacao;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CoordenadorController extends Controller
{
    public function index()
    {
        $coordenadorId = Auth::id();
        $anoAtual = now()->year;
        $agora = now();

        // ============================ CONTADORES ============================
        $totalCidadaos      = Cidadao::count();
        $assistentesCriados = User::role('Assistente')->where('coordenador_id', $coordenadorId)->count();
        $meusProgramas      = Programa::where('user_id', $coordenadorId)->count();

        $inscricoesPendentes = ProgramaInscricao::where('status', 'pendente')
            ->whereHas('programa', fn($q) => $q->where('user_id', $coordenadorId))
            ->count();

        $inscricoesAprovadas = ProgramaInscricao::where('status', 'aprovado')
            ->whereHas('programa', fn($q) => $q->where('user_id', $coordenadorId))
            ->count();

        $vagasPreenchidas = Programa::where('user_id', $coordenadorId)
            ->withCount('inscricoes')
            ->get()
            ->sum('inscricoes_count');

        // ============================ EMERGÊNCIAS RECENTES ============================
        $emergenciasRecentes = Emergencia::with('cidadao')
            ->where('created_at', '>=', $agora->copy()->subHours(48))
            ->latest()
            ->get();

        // ============================ GRÁFICOS MENSAIS ============================
        $dadosMensais = [
            'cidadaos' => $this->preencherCom12Meses(
                Cidadao::whereYear('created_at', $anoAtual)
                    ->selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
                    ->groupBy('mes')->pluck('total', 'mes')->all()
            ),
            'assistentes' => $this->preencherCom12Meses(
                User::role('Assistente')->where('coordenador_id', $coordenadorId)
                    ->whereYear('created_at', $anoAtual)
                    ->selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
                    ->groupBy('mes')->pluck('total', 'mes')->all()
            ),
            'programas' => $this->preencherCom12Meses(
                Programa::where('user_id', $coordenadorId)
                    ->whereYear('created_at', $anoAtual)
                    ->selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
                    ->groupBy('mes')->pluck('total', 'mes')->all()
            ),
        ];

        // ============================ INDICAÇÕES E DENÚNCIAS ============================
        $dadosIndicacoesDenuncias = [
            'indicacoes' => $this->preencherCom12Meses(
                IndicacaoPrograma::whereYear('created_at', $anoAtual)
                    ->whereHas('programa', fn($q) => $q->where('user_id', $coordenadorId))
                    ->selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
                    ->groupBy('mes')->pluck('total', 'mes')->all()
            ),
            'denuncias' => $this->preencherCom12Meses(
                DenunciaPrograma::whereYear('created_at', $anoAtual)
                    ->whereHas('programa', fn($q) => $q->where('user_id', $coordenadorId))
                    ->selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
                    ->groupBy('mes')->pluck('total', 'mes')->all()
            ),
        ];

        // ============================ SOLICITAÇÕES POR DESTINATÁRIO ============================
        $dadosSolicitacoes = [
            'cidadao' => $this->preencherCom12Meses(
                Solicitacao::where('user_id', $coordenadorId)
                    ->where('destinatario_tipo', 'cidadao')
                    ->whereYear('created_at', $anoAtual)
                    ->selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
                    ->groupBy('mes')->pluck('total', 'mes')->all()
            ),
            'assistente' => $this->preencherCom12Meses(
                Solicitacao::where('user_id', $coordenadorId)
                    ->where('destinatario_tipo', 'assistente')
                    ->whereYear('created_at', $anoAtual)
                    ->selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
                    ->groupBy('mes')->pluck('total', 'mes')->all()
            ),
        ];

        // ============================ GRÁFICO DE OCORRÊNCIAS ============================
        $dadosEmergencias = $this->preencherCom12Meses(
            Emergencia::whereYear('created_at', $anoAtual)
                ->selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
                ->groupBy('mes')->pluck('total', 'mes')->all()
        );

        // ============================ PLANTONISTAS ============================
        $plantoesRecentes = ModoPlantao::with('user')
            ->where('ativo', true)
            ->where('updated_at', '>=', $agora->copy()->subHours(48))
            ->orderByDesc('updated_at')
            ->get();

        $historicoPlantoes = ModoPlantao::with('user')
            ->where('updated_at', '>=', $agora->copy()->subMonths(6))
            ->orderByDesc('updated_at')
            ->get();

        // Limpa plantões antigos
        ModoPlantao::where('updated_at', '<', $agora->copy()->subMonths(6))->delete();

        // ============================ RANKING DE ASSISTENTES ============================
        $periodo = request()->get('periodo', 30);
        $inicio = $agora->copy()->subDays((int)$periodo);

        $top5Assistentes = User::role('Assistente')
            ->where('coordenador_id', $coordenadorId)
            ->withCount(['evolucoes as total_evolucoes' => fn($q) => $q->where('created_at', '>=', $inicio)])
            ->orderByDesc('total_evolucoes')
            ->take(5)
            ->get();

        // ============================ RETORNO PARA A VIEW ============================
        return view('coordenador.dashboard', compact(
            'totalCidadaos',
            'assistentesCriados',
            'meusProgramas',
            'inscricoesPendentes',
            'inscricoesAprovadas',
            'vagasPreenchidas',
            'emergenciasRecentes',
            'dadosMensais',
            'dadosSolicitacoes',
            'dadosIndicacoesDenuncias',
            'dadosEmergencias',
            'plantoesRecentes',
            'top5Assistentes',
            'periodo',
            'historicoPlantoes'
        ));
    }

    public function historicoPlantoes()
    {
        $historicoPlantoes = ModoPlantao::with('user')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('coordenador.plantoes.historico', compact('historicoPlantoes'));
    }

    private function preencherCom12Meses(array $dados): array
    {
        return array_values(array_replace(array_fill(1, 12, 0), $dados));
    }
}
