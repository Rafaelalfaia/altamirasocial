<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Cidadao;
use App\Models\Programa;
use App\Models\ProgramaInscricao;
use App\Models\Evolucao;
use App\Models\Emergencia;
use App\Models\DenunciaPrograma;
use App\Models\IndicacaoPrograma;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        // Contagens principais
        $totalUsuarios      = User::count();
        $totalRoles         = Role::count();
        $totalAdmins        = User::role('Admin')->count();
        $totalCoordenadores = User::role('Coordenador')->count();

        $totalAssistentes   = User::role('Assistente')->count();
        $totalCidadaos      = Cidadao::count();
        $totalProgramas     = Programa::count();
        $totalInscricoes    = ProgramaInscricao::count();
        $totalEvolucoes     = Evolucao::count();
        $totalEmergencias   = Emergencia::count();
        $totalDenuncias     = DenunciaPrograma::count();
        $totalIndicacoes    = IndicacaoPrograma::count();

        // Logs simulados (pode futuramente integrar com spatie/laravel-activitylog)
        $logsRecentes = [
            ['usuario' => 'Admin SEMAPS', 'acao' => 'Criou usuário', 'tabela' => 'users', 'data' => Carbon::now()->subMinutes(5)->format('d/m/Y H:i')],
            ['usuario' => 'Técnico João', 'acao' => 'Aprovou inscrição', 'tabela' => 'inscricaos', 'data' => Carbon::now()->subHour()->format('d/m/Y H:i')],
            ['usuario' => 'Assistente Maria', 'acao' => 'Editou cidadão', 'tabela' => 'cidadaos', 'data' => Carbon::now()->subHours(2)->format('d/m/Y H:i')],
            ['usuario' => 'Coordenador Ana', 'acao' => 'Aprovou lote', 'tabela' => 'lote_pagamentos', 'data' => Carbon::now()->subDay()->format('d/m/Y H:i')],
            ['usuario' => 'Admin SEMAPS', 'acao' => 'Criou backup', 'tabela' => 'backups', 'data' => Carbon::now()->subDays(2)->format('d/m/Y H:i')],
        ];

        // Gráfico: usuários por papel
        $roles = Role::all();
        $graficoUsuarios = [
            'labels' => [],
            'valores' => []
        ];

        foreach ($roles as $role) {
            $graficoUsuarios['labels'][] = $role->name;
            $graficoUsuarios['valores'][] = User::role($role->name)->count();
        }


        // Gráfico: cidadãos cadastrados por mês (últimos 6 meses)
        $cidadaosPorMes = Cidadao::selectRaw("DATE_FORMAT(created_at, '%m/%Y') as mes, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('mes')
            ->orderByRaw("STR_TO_DATE(mes, '%m/%Y')")
            ->pluck('total', 'mes');

        return view('admin.dashboard', compact(
            'totalUsuarios',
            'totalRoles',
            'totalAdmins',
            'totalCoordenadores',
            'totalAssistentes',
            'totalCidadaos',
            'totalProgramas',
            'totalInscricoes',
            'totalEvolucoes',
            'totalEmergencias',
            'totalDenuncias',
            'totalIndicacoes',
            'graficoUsuarios',
            'cidadaosPorMes',
            'logsRecentes'
        ));
    }
}
