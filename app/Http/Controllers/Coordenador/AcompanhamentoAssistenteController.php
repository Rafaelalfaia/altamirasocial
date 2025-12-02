<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Acompanhamento;
use App\Models\Evolucao;

class AcompanhamentoAssistenteController extends Controller
{
    public function index($id)
    {
        $assistente = User::findOrFail($id);

        $acompanhamentos = Acompanhamento::with('cidadao')
            ->where('user_id', $assistente->id)
            ->latest()
            ->paginate(10);

        return view('coordenador.assistentes.acompanhamentos.index', compact('assistente', 'acompanhamentos'));
    }

    public function show($assistenteId, $acompanhamentoId)
    {
        $acompanhamento = Acompanhamento::with(['cidadao', 'composicaoFamiliar', 'assistente'])
            ->where('id', $acompanhamentoId)
            ->where('user_id', $assistenteId)
            ->firstOrFail();

        return view('coordenador.assistentes.acompanhamentos.show', compact('acompanhamento'));
    }

    public function evolucoesIndex($assistenteId, $acompanhamentoId)
    {
        $acompanhamento = Acompanhamento::with('cidadao')->findOrFail($acompanhamentoId);
        $evolucoes = $acompanhamento->evolucoes()->latest()->get();

        return view('coordenador.assistentes.acompanhamentos.evolucoes.index', compact('acompanhamento', 'evolucoes'));
    }

}
