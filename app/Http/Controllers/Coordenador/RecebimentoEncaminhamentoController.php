<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RecebimentoEncaminhamento;
use App\Models\OrgaoPublico;
use App\Models\Cidadao;
use App\Models\Programa;

class RecebimentoEncaminhamentoController extends Controller
{
    public function index()
    {
        $registros = RecebimentoEncaminhamento::where('coordenador_id', Auth::id())
                        ->with(['orgao', 'cidadao', 'programa'])
                        ->latest()
                        ->get();

        return view('coordenador.recebimentos.index', compact('registros'));
    }

    public function create()
    {
        $orgaos = OrgaoPublico::where('coordenador_id', Auth::id())->get();
        $cidadaos = Cidadao::all();
        $programas = Programa::all();
        $isEdit = false;

        return view('coordenador.recebimentos.create', compact('orgaos', 'cidadaos', 'programas', 'isEdit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'orgao_publico_id' => 'required|exists:orgaos_publicos,id',
            'tipo' => 'required|in:recebimento,encaminhamento',
            'nome_cidadao' => 'required|string|max:255',
            'cidadao_id' => 'nullable|exists:cidadaos,id',
            'programa_social_id' => 'nullable|exists:programas,id',
            'descricao' => 'nullable|string',
        ]);

        RecebimentoEncaminhamento::create([
            'coordenador_id' => Auth::id(),
            'orgao_publico_id' => $request->orgao_publico_id,
            'tipo' => $request->tipo,
            'cidadao_id' => $request->cidadao_id,
            'nome_cidadao' => $request->nome_cidadao,
            'programa_social_id' => $request->programa_social_id,
            'descricao' => $request->descricao,
        ]);

        return redirect()->route('coordenador.recebimentos.index')->with('success', 'Registro salvo com sucesso.');
    }

    public function show($id)
    {
        $registro = RecebimentoEncaminhamento::where('coordenador_id', Auth::id())
                        ->with(['orgao', 'cidadao', 'programa'])
                        ->findOrFail($id);

        return view('coordenador.recebimentos.show', compact('registro'));
    }

    public function edit($id)
    {
        $registro = RecebimentoEncaminhamento::where('coordenador_id', Auth::id())->findOrFail($id);
        $orgaos = OrgaoPublico::where('coordenador_id', Auth::id())->get();
        $cidadaos = Cidadao::all();
        $programas = Programa::all();
        $isEdit = true;

        return view('coordenador.recebimentos.create', compact('registro', 'orgaos', 'cidadaos', 'programas', 'isEdit'));
    }

    public function update(Request $request, $id)
    {
        $registro = RecebimentoEncaminhamento::where('coordenador_id', Auth::id())->findOrFail($id);

        $request->validate([
            'orgao_publico_id' => 'required|exists:orgaos_publicos,id',
            'tipo' => 'required|in:recebimento,encaminhamento',
            'nome_cidadao' => 'required|string|max:255',
            'cidadao_id' => 'nullable|exists:cidadaos,id',
            'programa_social_id' => 'nullable|exists:programas,id',
            'descricao' => 'nullable|string',
        ]);

        $registro->update([
            'orgao_publico_id' => $request->orgao_publico_id,
            'tipo' => $request->tipo,
            'cidadao_id' => $request->cidadao_id,
            'nome_cidadao' => $request->nome_cidadao,
            'programa_social_id' => $request->programa_social_id,
            'descricao' => $request->descricao,
        ]);

        return redirect()->route('coordenador.recebimentos.index')->with('success', 'Registro atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $registro = RecebimentoEncaminhamento::where('coordenador_id', auth()->id())->findOrFail($id);
        $registro->delete();

        return redirect()->route('coordenador.recebimentos.index')
            ->with('success', 'Registro excluído com sucesso.');
    }

}
