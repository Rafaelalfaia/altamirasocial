<?php

namespace App\Http\Controllers\Cidadao;

use App\Http\Controllers\Controller;
use App\Models\Cidadao;

class PerfilPublicoController extends Controller
{
    public function mostrarFicha($id)
    {
        $cidadao = Cidadao::with('bairro.cidade.estado')->findOrFail($id);



        return view('cidadao.perfil.ficha-publica', compact('cidadao'));
    }
}

