<?php

namespace App\Http\Controllers\Cidadao;

use App\Http\Controllers\Controller;
use App\Models\Solicitacao;
use Illuminate\Http\Request;

class SolicitacaoCidadaoController extends Controller
{
    /**
     * Lista as solicitações destinadas ao cidadão autenticado.
     */
    public function index()
    {
        $userId = auth()->id();

        $solicitacoes = Solicitacao::where('destinatario_id', $userId)
            ->where('destinatario_tipo', 'Cidadao')
            ->latest()
            ->get();

        return view('cidadao.solicitacoes.index', compact('solicitacoes'));
    }

    /**
     * Permite responder uma solicitação.
     */
    public function responder(Request $request, Solicitacao $solicitacao)
    {
        $request->validate(['resposta' => 'required|string']);

        // Garante que o cidadão só responda à própria solicitação
        if ($solicitacao->destinatario_id !== auth()->id()) {
            return back()->with('error', 'Você não tem permissão para responder essa solicitação.');
        }

        $solicitacao->update(['resposta' => $request->resposta]);

        return back()->with('success', 'Resposta enviada com sucesso.');
    }

    /**
     * Permite marcar uma solicitação como concluída.
     */
    public function concluir(Solicitacao $solicitacao)
    {
        // Garante que o cidadão só conclua a própria solicitação
        if ($solicitacao->destinatario_id !== auth()->id()) {
            return back()->with('error', 'Você não tem permissão para concluir essa solicitação.');
        }

        $solicitacao->update(['status' => 'concluida']);

        return back()->with('success', 'Solicitação marcada como concluída.');
    }
}
