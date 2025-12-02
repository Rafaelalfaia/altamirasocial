<?php

namespace App\Http\Controllers\Assistente;

use App\Http\Controllers\Controller;
use App\Models\Solicitacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SolicitacaoAssistenteController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $solicitacoes = Solicitacao::where('destinatario_id', $userId)
            ->latest()
            ->get();

        return view('assistente.solicitacoes.index', compact('solicitacoes'));
    }


    public function responder(Request $request, Solicitacao $solicitacao)
    {
        $request->validate(['resposta' => 'required|string']);
        $solicitacao->update(['resposta' => $request->resposta]);
        return back()->with('success', 'Resposta enviada.');
    }

    public function concluir(Solicitacao $solicitacao)
    {
        $solicitacao->update(['status' => 'concluida']);
        return back()->with('success', 'Solicitação concluída.');
    }

    public function destroy(Solicitacao $solicitacao)
    {
        if ($solicitacao->status === 'concluida') {
            $solicitacao->delete();
            return back()->with('success', 'Solicitação excluída.');
        }
        return back()->with('error', 'Só é possível excluir solicitações concluídas.');
    }
}
