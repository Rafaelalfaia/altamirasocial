<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use App\Models\Cidadao;
use App\Models\Bairro;
use Illuminate\Http\Request;

class CidadaoController extends Controller
{
    public function index()
    {
        $cidadaos = Cidadao::with('user', 'bairro.cidade.estado')
            ->latest()
            ->paginate(10);

        return view('coordenador.cidadaos.index', compact('cidadaos'));
    }

    public function edit($id)
    {
        $cidadao = Cidadao::with('bairro.cidade.estado')->findOrFail($id);
        $bairros = Bairro::with('cidade.estado')->get(); // Para dropdown de bairros

        return view('coordenador.cidadaos.edit', compact('cidadao', 'bairros'));
    }

    public function update(Request $request, $id)
    {
        $cidadao = Cidadao::findOrFail($id);

        $request->validate([
            'bairro_id' => 'required|exists:bairros,id',
        ]);

        $cidadao->bairro_id = $request->bairro_id;
        $cidadao->save();

        return redirect()->route('coordenador.cidadaos.index')
                         ->with('success', 'Bairro atualizado com sucesso.');
    }
}
