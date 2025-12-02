<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Estado;
use App\Models\Cidade;
use App\Models\Bairro;

class MoradiaController extends Controller
{
    public function index()
    {
        $estados = Estado::with('cidades')->orderBy('nome')->get();
        $cidades = Cidade::with('estado')->orderBy('nome')->get();
        $bairros = Bairro::with('cidade.estado')->orderBy('nome')->get();

        
        return view('coordenador.bairros.index', compact('estados', 'cidades', 'bairros'));
    }




    public function salvarEstado(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:estados,nome',
        ]);

        Estado::create($request->only('nome'));

        return back()->with('success', 'Estado adicionado com sucesso!');
    }

    public function salvarCidade(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'estado_id' => 'required|exists:estados,id',
        ]);

        Cidade::create($request->only(['nome', 'estado_id']));

        return back()->with('success', 'Cidade adicionada com sucesso!');
    }

    public function salvarBairro(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cidade_id' => 'required|exists:cidades,id',
        ]);

        Bairro::create($request->only(['nome', 'cidade_id']));

        return back()->with('success', 'Bairro adicionado com sucesso!');
    }

    public function deletarBairro(Bairro $bairro)
    {
        $bairro->delete();
        return back()->with('success', 'Bairro removido com sucesso!');
    }

    public function deletarEstado(Estado $estado)
    {
        $estado->delete();
        return back()->with('success', 'Estado removido com sucesso!');
    }

    public function deletarCidade(Cidade $cidade)
    {
        $cidade->delete();
        return back()->with('success', 'Cidade removida com sucesso!');
    }


}
