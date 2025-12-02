<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\IndicacaoPrograma;
use App\Models\DenunciaPrograma;
use App\Models\ProgramaInscricao;

class ProgramaAnaliseController extends Controller
{
    public function index()
    {
        $usuarioId = Auth::id();

        $indicacoesPendentes = IndicacaoPrograma::where('status', 'pendente')
            ->whereHas('programa', fn($q) => $q->where('user_id', $usuarioId))
            ->with(['programa', 'cidadao', 'assistente'])
            ->get();

        $denunciasPendentes = DenunciaPrograma::where('status', 'pendente')
            ->whereHas('programa', fn($q) => $q->where('user_id', $usuarioId))
            ->with(['programa', 'cidadao', 'assistente'])
            ->get();

        return view('coordenador.analises.index', compact('indicacoesPendentes', 'denunciasPendentes'));
    }

    public function historico()
    {
        $usuarioId = Auth::id();

        $indicacoes = IndicacaoPrograma::whereNotNull('status')
            ->whereHas('programa', fn($q) => $q->where('user_id', $usuarioId))
            ->with(['programa', 'cidadao', 'assistente'])
            ->get();

        $denuncias = DenunciaPrograma::whereNotNull('status')
            ->whereHas('programa', fn($q) => $q->where('user_id', $usuarioId))
            ->with(['programa', 'cidadao', 'assistente'])
            ->get();

        return view('coordenador.analises.historico', compact('indicacoes', 'denuncias'));
    }

    public function aceitar($tipo, $id)
    {
        $item = $this->getItem($tipo, $id);

        if (!$item->programa || $item->programa->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para aprovar este item.');
        }

        $item->status = 'aprovada';
        $item->avaliado_em = now();
        $item->resposta_coordenador = null;
        $item->save();

        // Atualiza o status da inscrição
        $inscricao = ProgramaInscricao::where('cidadao_id', $item->cidadao_id)
            ->where('programa_id', $item->programa_id)
            ->first();

        if ($inscricao) {
            if ($tipo === 'indicacao') {
                $inscricao->status = 'aprovado';
            } elseif ($tipo === 'denuncia' && $inscricao->status === 'aprovado') {
                $inscricao->status = 'reprovado';
            }
            $inscricao->save();
        }

        return back()->with('success', ucfirst($tipo) . ' aprovada com sucesso!');
    }

    public function recusar(Request $request, $tipo, $id)
    {
        $request->validate([
            'motivo_rejeicao' => 'required|string|max:1000',
        ]);

        $item = $this->getItem($tipo, $id);

        if (!$item->programa || $item->programa->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para reprovar este item.');
        }

        $item->status = 'reprovada';
        $item->avaliado_em = now();
        $item->resposta_coordenador = $request->motivo_rejeicao;
        $item->save();

        return back()->with('success', ucfirst($tipo) . ' reprovada com sucesso!');
    }

    private function getItem($tipo, $id)
    {
        return match ($tipo) {
            'indicacao' => IndicacaoPrograma::with('programa')->findOrFail($id),
            'denuncia' => DenunciaPrograma::with('programa')->findOrFail($id),
            default => abort(404),
        };
    }
}
