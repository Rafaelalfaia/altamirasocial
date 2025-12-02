<?php

namespace App\Http\Controllers\Restaurante\Atendente;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\VendaRestaurante;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $restaurante = $usuario->restaurantes()->first(); // muitos-para-muitos

        if (!$restaurante) {
            return back()->withErrors(['restaurante' => 'Você não está vinculado a um restaurante.']);
        }

        // Datas
        $hoje = Carbon::today();
        $inicioSemana = Carbon::now()->startOfWeek();
        $inicioMes = Carbon::now()->startOfMonth();

        // Função para calcular as métricas de um conjunto de vendas
        $calcularMetricas = function ($vendas) {
            return [
                'total_vendas' => $vendas->count(),
                'total_pratos' => $vendas->sum('numero_pratos'),
                'clientes_normais' => $vendas->where('tipo_cliente', 'cidadao')->count(),
                'clientes_temporarios' => $vendas->where('tipo_cliente', 'temporario')->count(),
                'pagamento_pix' => $vendas->where('forma_pagamento', 'pix')->count(),
                'pagamento_dinheiro' => $vendas->where('forma_pagamento', 'dinheiro')->count(),
                'pagamento_credito' => $vendas->where('forma_pagamento', 'credito')->count(),
                'pagamento_debito' => $vendas->where('forma_pagamento', 'debito')->count(),
            ];
        };

        // Coleta de vendas
        $vendasHoje = VendaRestaurante::where('user_id', $usuario->id)
            ->whereDate('data_venda', $hoje)
            ->get();

        $vendasSemana = VendaRestaurante::where('user_id', $usuario->id)
            ->whereBetween('data_venda', [$inicioSemana, now()])
            ->get();

        $vendasMes = VendaRestaurante::where('user_id', $usuario->id)
            ->whereBetween('data_venda', [$inicioMes, now()])
            ->get();

        // Métricas
        $metricasHoje = $calcularMetricas($vendasHoje);
        $metricasSemana = $calcularMetricas($vendasSemana);
        $metricasMes = $calcularMetricas($vendasMes);

        return view('restaurante.atendente.dashboard', [
            'usuario' => $usuario,
            'restaurante' => $restaurante,
            'metricasHoje' => $metricasHoje,
            'metricasSemana' => $metricasSemana,
            'metricasMes' => $metricasMes,
        ]);
    }
}
