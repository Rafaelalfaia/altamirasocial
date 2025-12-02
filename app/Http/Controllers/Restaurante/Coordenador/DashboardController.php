<?php

namespace App\Http\Controllers\Restaurante\Coordenador;

use App\Http\Controllers\Controller;
use App\Models\Restaurante;
use App\Models\User;
use App\Models\VendaRestaurante;
use App\Models\Cidadao;
use App\Models\CidadaoTemporario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        // Restaurantes do coordenador
        $restaurantes = $usuario->restaurantes; // relacionamento belongsToMany
        $restauranteIds = $restaurantes->pluck('id');

        // Atendentes vinculados a esses restaurantes
        $atendentes = User::whereHas('restaurantes', function ($query) use ($restauranteIds) {
            $query->whereIn('restaurante_id', $restauranteIds);
        })->role('Atendente Restaurante')->get();

        // Vendas totais dos restaurantes
        $vendas = VendaRestaurante::whereIn('restaurante_id', $restauranteIds)->get();

        // Vendas no mês atual
        $inicioMes = Carbon::now()->startOfMonth();
        $vendasDoMes = $vendas->where('created_at', '>=', $inicioMes);

        // Total arrecadado
        $totalReais = $vendas->sum('valor');

        // Total de pratos servidos
        $totalPratos = $vendas->count();

        // Cidadãos normais
        $cidadaos = VendaRestaurante::whereIn('restaurante_id', $restauranteIds)
            ->whereNotNull('cidadao_id')
            ->distinct('cidadao_id')
            ->count('cidadao_id');

        // Temporários atendidos
       $temporarios = VendaRestaurante::whereIn('restaurante_id', $restauranteIds)
        ->whereNotNull('cidadao_temporario_id')
        ->distinct('cidadao_temporario_id')
        ->count('cidadao_temporario_id');


        return view('restaurante.coordenador.dashboard', [
            'restaurantes' => $restaurantes,
            'atendentes' => $atendentes,
            'totalVendas' => $vendas->count(),
            'totalPratos' => $totalPratos,
            'totalReais' => $totalReais,
            'vendasDoMes' => $vendasDoMes->count(),
            'cidadaos' => $cidadaos,
            'temporarios' => $temporarios,
        ]);
    }
}
