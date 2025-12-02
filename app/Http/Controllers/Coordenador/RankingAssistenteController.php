<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Evolucao;

class RankingAssistenteController extends Controller
{
    public function index(Request $request)
{
    $coordenadorId = auth()->id();
    $periodo = $request->get('periodo', '1_mes');

    $inicio = match ($periodo) {
        '3_meses' => now()->subDays(90),
        '6_meses' => now()->subDays(180),
        '1_ano'   => now()->subDays(365),
        default   => now()->subDays(30),
    };

    $ranking = User::role('Assistente')
        ->where('coordenador_id', $coordenadorId)
        ->withCount([
            'evolucoes as total_evolucoes' => function ($query) use ($inicio) {
                $query->where('created_at', '>=', $inicio);
            }
        ])
        ->orderByDesc('total_evolucoes')
        ->take(100) // ou 5, se for o top 5 direto
        ->get();

    return view('coordenador.assistentes.ranking.index', compact('ranking', 'periodo'));
}

}
