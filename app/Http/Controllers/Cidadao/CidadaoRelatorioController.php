<?php

namespace App\Http\Controllers\Cidadao;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Cidadao;
use App\Models\VendaRestaurante;
use Carbon\Carbon;

class CidadaoRelatorioController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cidadao = $user->cidadao;

        // Programas
        $programas = $cidadao->inscricoes()->with('programa')->get();

        // Evoluções
        $agora = Carbon::now();
        $evolucoes = $cidadao->acompanhamentos()->with('evolucoes')->get()->flatMap->evolucoes;

        $evolucoesUltimoMes = $evolucoes->where('created_at', '>=', $agora->copy()->subMonth())->count();
        $evolucoes3Meses = $evolucoes->where('created_at', '>=', $agora->copy()->subMonths(3))->count();
        $evolucoes6Meses = $evolucoes->where('created_at', '>=', $agora->copy()->subMonths(6))->count();
        $evolucoesAno = $evolucoes->where('created_at', '>=', $agora->copy()->subYear())->count();

        // Porcentagem de dados preenchidos
        $camposObrigatorios = collect([
            'nome', 'cpf', 'data_nascimento', 'sexo', 'telefone', 'rua', 'numero',
            'tipo_moradia', 'tem_agua_encanada', 'tem_esgoto', 'tem_energia',
            'tem_coleta_lixo', 'renda_total_familiar', 'pessoas_na_residencia',
            'ocupacao', 'escolaridade', 'cor_raca', 'pcd'
        ]);

        $totalCampos = $camposObrigatorios->count();
        $preenchidos = $camposObrigatorios->filter(fn($campo) => !empty($cidadao->$campo))->count();
        $porcentagemPreenchida = round(($preenchidos / $totalCampos) * 100);

        // Refeições no restaurante
        $vendasRestaurante = VendaRestaurante::where('cidadao_id', $cidadao->id)->count();

        return view('cidadao.relatorios.index', compact(
            'programas',
            'evolucoesUltimoMes',
            'evolucoes3Meses',
            'evolucoes6Meses',
            'evolucoesAno',
            'porcentagemPreenchida',
            'vendasRestaurante'
        ));
    }
}
