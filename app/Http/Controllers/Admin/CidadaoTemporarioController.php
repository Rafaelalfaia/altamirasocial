<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CidadaoTemporario;
use App\Models\User;
use Carbon\Carbon;

class CidadaoTemporarioController extends Controller
{
    // Listar todos os cidadãos temporários
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filtroRestaurante = $request->input('restaurante');

        $cidadaos = CidadaoTemporario::with('user') // carrega usuário criador
            ->when($search, function ($query, $search) {
                $query->where('nome', 'like', "%{$search}%")
                    ->orWhere('cpf', 'like', "%{$search}%");
            })
            ->when($filtroRestaurante, function ($query, $filtroRestaurante) {
                $query->whereHas('user.restaurantes', function ($q) use ($filtroRestaurante) {
                    $q->where('id', $filtroRestaurante);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.cidadaos_temporarios.index', compact('cidadaos', 'search', 'filtroRestaurante'));
    }

    // Visualizar um cidadão temporário
    public function show($id)
    {
        $cidadao = CidadaoTemporario::with(['user.restaurantes'])->findOrFail($id);

        return view('admin.cidadaos_temporarios.show', compact('cidadao'));
    }
}
