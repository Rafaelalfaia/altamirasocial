<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OrgaoPublico;

class OrgaoPublicoController extends Controller
{
    public function index()
    {
        $orgaos = OrgaoPublico::where('coordenador_id', Auth::id())->get();
        return view('coordenador.orgaos.index', compact('orgaos'));
    }

    public function create()
    {
        return view('coordenador.orgaos.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $orgao = OrgaoPublico::create([
            'coordenador_id' => Auth::id(),
            'nome' => $request->nome
        ]);

        // Caso seja uma requisição AJAX, retorna JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'id' => $orgao->id,
                'nome' => $orgao->nome,
            ]);
        }

        return redirect()->route('coordenador.orgaos.index')->with('success', 'Órgão criado com sucesso.');
    }

    public function edit($id)
    {
        $orgao = OrgaoPublico::where('coordenador_id', Auth::id())->findOrFail($id);
        return view('coordenador.orgaos.edit', compact('orgao'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $orgao = OrgaoPublico::where('coordenador_id', Auth::id())->findOrFail($id);
        $orgao->update([
            'nome' => $request->nome
        ]);

        return redirect()->route('coordenador.orgaos.index')->with('success', 'Órgão atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $orgao = OrgaoPublico::where('coordenador_id', Auth::id())->findOrFail($id);
        $orgao->delete();

        return redirect()->back()->with('success', 'Órgão removido com sucesso.');
    }
}

