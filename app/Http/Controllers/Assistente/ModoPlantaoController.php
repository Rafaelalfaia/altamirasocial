<?php

namespace App\Http\Controllers\Assistente;

use App\Http\Controllers\Controller;
use App\Models\ModoPlantao;
use Illuminate\Http\Request;

class ModoPlantaoController extends Controller
{
    public function alternar()
    {
        $modo = ModoPlantao::firstOrCreate(['user_id' => auth()->id()]);

        if (! $modo->ativo) {
            $modo->inicio_plantao = now();
        } else {
            $modo->fim_plantao = now();
        }

        $modo->ativo = ! $modo->ativo;
        $modo->save();

        return back()->with('success', 'Modo Plantão ' . ($modo->ativo ? 'ativado' : 'desativado'));
    }

    public function historico(Request $request)
    {
        $query = ModoPlantao::with('user')->latest();

        if ($request->filled('nome')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->nome . '%');
            });
        }

        if ($request->filled('data')) {
            $query->whereDate('inicio_plantao', $request->data);
        }

        $historicoPlantoes = $query->get();

        return view('coordenador.plantoes.historico', compact('historicoPlantoes'));
    }
}
