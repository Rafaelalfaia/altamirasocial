<?php

namespace App\Http\Controllers\Cidadao;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Cidadao;
use App\Models\Programa;

class CidadaoController extends Controller
{
    public function dashboard()
    {
        $usuario = Auth::user();

        // cria/busca sem forçar cpf
        $cidadao = Cidadao::firstOrCreate(
            ['user_id' => $usuario->id],
            [
                'nome'   => $usuario->name,
                'status' => 'pendente',
            ]
        );

        $programas = Programa::orderBy('created_at', 'desc')->take(5)->get();

        return view('cidadao.dashboard', compact('usuario', 'cidadao', 'programas'));
    }


    public function editar()
    {
        $cidadao = Auth::user()->cidadao;
        return view('cidadao.editar', compact('cidadao'));
    }


    public function update(Request $request)
    {
        $usuario = Auth::user();
        $cidadao = Cidadao::where('user_id', $usuario->id)->firstOrFail();

        // Validação básica (ajuste conforme necessário)
        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14',
        ]);

        $cidadao->update($request->only([
            'nome',
            'cpf',
            'bairro',
            'renda',
            'status'
        ]));

        return redirect()->route('cidadao.dashboard')->with('success', 'Dados atualizados com sucesso!');
    }
}
