<?php

namespace App\Http\Controllers\Assistente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Evolucao;
use App\Models\Acompanhamento;
use App\Models\Cidadao;
use App\Models\Emergencia;
use App\Models\IndicacaoPrograma;
use App\Models\DenunciaPrograma;
use App\Models\ProgramaInscricao;

class RelatorioAssistenteController extends Controller
{
    public function index(Request $request)
    {
        $assistenteId = Auth::id();

        // Período dinâmico
        $periodo = $request->input('periodo', '1m');
        $dataInicio = match ($periodo) {
            '3m' => Carbon::now()->subMonths(3),
            '6m' => Carbon::now()->subMonths(6),
            '1a' => Carbon::now()->subYear(),
            default => Carbon::now()->subMonth(),
        };

        // Evoluções do assistente
        $evolucoes = Evolucao::with('acompanhamento.cidadao')
            ->where('user_id', $assistenteId)
            ->where('created_at', '>=', $dataInicio)
            ->get();

        // IDs dos cidadãos acompanhados
        $cidadaosIds = $evolucoes->pluck('acompanhamento.cidadao.id')->filter()->unique();
        $cidadaos = Cidadao::with(['bairro.cidade', 'inscricoes.programa'])
            ->whereIn('id', $cidadaosIds)
            ->get();

        // 1. Total de evoluções
        $totalEvolucoes = $evolucoes->count();

        // 2. Cidadãos mais visitados (Top 5)
        $maisVisitados = $evolucoes->groupBy('acompanhamento.cidadao.id')
            ->map(fn($items) => $items->count())
            ->sortDesc()
            ->take(5);

        // 3. Gênero
        $generos = $cidadaos->groupBy('sexo')->map->count();

        // 4. Faixa Etária
        $faixaEtaria = $cidadaos->mapToGroups(function ($c) {
            $idade = Carbon::parse($c->data_nascimento)->age ?? 0;
            return [match (true) {
                $idade < 12 => '0-11',
                $idade < 18 => '12-17',
                $idade < 30 => '18-29',
                $idade < 45 => '30-44',
                $idade < 60 => '45-59',
                default => '60+',
            } => $c];
        })->map->count();

        // 5. Renda per capita (média)
        $rendaPerCapita = $cidadaos->map(function ($c) {
            return $c->pessoas_na_residencia > 0
                ? $c->renda_total_familiar / $c->pessoas_na_residencia
                : 0;
        })->filter()->avg();

        // 6. Bairros dos cidadãos acompanhados
        $bairros = $cidadaos->groupBy(fn($c) => optional($c->bairro)->nome ?? 'Não informado')->map->count();

        // 7. Participação em programas sociais
        $programas = $cidadaos->flatMap(fn($c) => $c->inscricoes->pluck('programa.nome'))->countBy();

        // 8. Ocorrências em evoluções
        $ocorrenciasEvolucao = [
            'casos_emergenciais' => $evolucoes->where('caso_emergencial', true)->count(),
            'tentativas_homicidio' => $evolucoes->where('tentativa_homicidio', true)->count(),
        ];

        // 9. Ocorrências de emergência
        $emergencias = Emergencia::whereIn('cidadao_id', $cidadaosIds)
            ->where('user_id', $assistenteId)
            ->where('created_at', '>=', $dataInicio)
            ->count();

        // 10. Indicações e denúncias
        $indicacoes = IndicacaoPrograma::where('user_id', $assistenteId)
            ->where('created_at', '>=', $dataInicio)
            ->count();

        $denuncias = DenunciaPrograma::where('user_id', $assistenteId)
            ->where('created_at', '>=', $dataInicio)
            ->count();

        // 11 a 14. Gráficos de PCDs
        $pcds = $cidadaos->where('pcd', true);
        $totalPCDs = $pcds->count();
        $pcdsEmProgramas = $pcds->filter(fn ($c) => $c->inscricoes->isNotEmpty())->count();
        $programasPCDs = $pcds->flatMap(fn ($c) => $c->inscricoes->pluck('programa.nome'))->countBy();
        $bairrosPCDs = $pcds->groupBy(fn($c) => optional($c->bairro)->nome ?? 'Não informado')->map->count();

        return view('assistente.relatorios.index', compact(
            'periodo',
            'totalEvolucoes',
            'maisVisitados',
            'generos',
            'faixaEtaria',
            'rendaPerCapita',
            'bairros',
            'programas',
            'ocorrenciasEvolucao',
            'emergencias',
            'indicacoes',
            'denuncias',
            'totalPCDs',
            'pcdsEmProgramas',
            'programasPCDs',
            'bairrosPCDs'
        ));
    }
}