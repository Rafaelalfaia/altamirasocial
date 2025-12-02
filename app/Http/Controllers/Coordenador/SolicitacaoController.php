<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use App\Models\Solicitacao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SolicitacaoController extends Controller
{
    /**
     * Lista todas as solicitações enviadas.
     */
    public function index()
    {
        $solicitacoes = Solicitacao::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return view('coordenador.solicitacoes.index', compact('solicitacoes'));
    }


    /**
     * Exibe formulário de criação de nova solicitação.
     */
    public function create()
    {
        $assistentes = User::role('Assistente')->get();
        $cidadaos = User::role('Cidadao')->get();

        return view('coordenador.solicitacoes.create', compact('assistentes', 'cidadaos'));
    }

    /**
     * Armazena solicitações para múltiplos destinatários.
     */
    public function store(Request $request)
{
    // Mescla os dois arrays de destinatários (pode ser vazio)
    $assistentes = $request->input('destinatarios_assistentes', []);
    $cidadaos = $request->input('destinatarios_cidadaos', []);
    $todosDestinatarios = array_merge($assistentes, $cidadaos);

    // Validação
    $request->validate([
        'titulo' => 'required|string|max:255',
        'mensagem' => 'required|string',
    ]);

    if (empty($todosDestinatarios)) {
        return redirect()->back()
            ->withErrors(['destinatarios' => 'Selecione ao menos um destinatário (assistente ou cidadão).'])
            ->withInput();
    }

    // Cria as solicitações individualmente para cada destinatário
    foreach ($todosDestinatarios as $destinatarioId) {
        // Detecta se é assistente ou cidadão
        $tipo = in_array($destinatarioId, $assistentes) ? 'assistente' : 'cidadao';


        \App\Models\Solicitacao::create([
            'titulo' => $request->titulo,
            'mensagem' => $request->mensagem,
            'user_id' => auth()->id(),
            'destinatario_id' => $destinatarioId,
            'destinatario_tipo' => $tipo,
            'status' => 'pendente',
        ]);
    }

    return redirect()->route('coordenador.solicitacoes.index')
        ->with('success', 'Solicitação(ões) enviada(s) com sucesso!');
}


    /**
     * Exibe detalhes de uma solicitação.
     */
    public function show(Solicitacao $solicitacao)
    {
        return view('coordenador.solicitacoes.show', compact('solicitacao'));
    }

    /**
     * Exibe formulário para edição de uma solicitação.
     */
    public function edit(Solicitacao $solicitacao)
    {
        return view('coordenador.solicitacoes.edit', compact('solicitacao'));
    }

    /**
     * Atualiza os dados da solicitação.
     */
    public function update(Request $request, Solicitacao $solicitacao)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'mensagem' => 'required|string',
        ]);

        $solicitacao->update([
            'titulo' => $request->titulo,
            'mensagem' => $request->mensagem, 
        ]);

        return redirect()->route('coordenador.solicitacoes.index')
            ->with('success', 'Solicitação atualizada com sucesso.');
    }

    /**
     * Marca uma solicitação como concluída.
     */
    public function fechar(Solicitacao $solicitacao)
    {
        $solicitacao->update(['status' => 'concluida']);

        return back()->with('success', 'Solicitação encerrada.');
    }

    public function cancelarEnvio(Solicitacao $solicitacao)
    {
        $solicitacao->status_envio = 'cancelado';
        $solicitacao->save();

        return redirect()->route('coordenador.solicitacoes.index')
            ->with('success', 'Envio da solicitação cancelado com sucesso.');
    }

    /**
     * Exclui uma solicitação (soft delete).
     */
    public function destroy(Solicitacao $solicitacao)
{
    $solicitacao->delete();

    return redirect()->route('coordenador.solicitacoes.index')
        ->with('success', 'Solicitação apagada com sucesso!');
}


}
