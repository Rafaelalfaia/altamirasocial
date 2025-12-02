<?php


namespace App\Http\Controllers\Cidadao;

use App\Http\Controllers\Controller;
use App\Models\Cidadao;

class FichaPublicaController extends Controller
{
    public function mostrar($id)
    {
        $cidadao = Cidadao::with('bairro.cidade.estado')->findOrFail($id);
        return view('cidadao.perfil.ficha-publica', compact('cidadao'));
    }
}

