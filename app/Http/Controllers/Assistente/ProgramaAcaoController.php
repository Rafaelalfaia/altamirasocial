<?php

namespace App\Http\Controllers\Assistente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Programa;
use App\Models\ProgramaInscricao;
use App\Models\DenunciaPrograma;
use App\Models\Cidadao;
use Illuminate\Support\Facades\Auth;
use App\Models\IndicacaoPrograma;

class ProgramaAcaoController extends Controller
{
    public function indicar(Programa $programa)
    {
        if ($programa->user_id !== auth()->id() && !auth()->user()->hasRole('Assistente')) {
            abort(403);
        }
    
        $userId = auth()->id();
    
        // Pega os IDs dos cidadãos já indicados por esse assistente nesse programa
        $jaIndicados = IndicacaoPrograma::where('programa_id', $programa->id)
            ->where('user_id', $userId)
            ->pluck('cidadao_id');
    
        // Buscar inscrições do programa com status pendente ou reprovado
        // e que NÃO estão entre os já indicados
        $inscricoes = $programa->inscricoes()
            ->whereIn('status', ['pendente', 'reprovado'])
            ->whereNotIn('cidadao_id', $jaIndicados)
            ->with('cidadao')
            ->get();
    
        return view('assistente.programas.indicar', compact('programa', 'inscricoes'));
    }
    


    public function denunciar($programa_id)
    {
        $programa = Programa::findOrFail($programa_id);
        $userId = auth()->id();

        $inscricoes = ProgramaInscricao::with('cidadao')
            ->where('programa_id', $programa_id)
            ->where('status', 'aprovado')
            ->get()
            ->filter(function ($inscricao) use ($programa_id, $userId) {
                // Filtra apenas quem ainda NÃO foi denunciado por este assistente
                return !DenunciaPrograma::where('programa_id', $programa_id)
                    ->where('cidadao_id', $inscricao->cidadao_id)
                    ->where('user_id', $userId)
                    ->exists();
            });

        return view('assistente.programas.denunciar', compact('programa', 'inscricoes'));
}

    // Formulário para denúncia
    public function criarDenuncia($programa_id, $cidadao_id)
    {
        $programa = Programa::findOrFail($programa_id);
        $cidadao = Cidadao::findOrFail($cidadao_id);

        return view('assistente.programas.denunciar_form', compact('programa', 'cidadao'));
    }

    public function criarIndicacao($programa_id, $cidadao_id)
{
    $programa = Programa::findOrFail($programa_id);
    $cidadao = Cidadao::findOrFail($cidadao_id);

    return view('assistente.programas.indicar_form', compact('programa', 'cidadao'));
}


    // Envia a denúncia
    public function salvarDenuncia(Request $request, $programa_id, $cidadao_id)
    {
        $request->validate([
            'motivo' => 'required|string|max:1000',
        ]);

        DenunciaPrograma::create([
            'programa_id' => $programa_id,
            'cidadao_id' => $cidadao_id,
            'user_id' => Auth::id(),
            'motivo' => $request->motivo,
        ]);

        return redirect()->route('assistente.programas.denunciar', $programa_id)
            ->with('success', 'Denúncia registrada com sucesso!');
    }

    public function salvarIndicacao(Request $request, $programa_id, $cidadao_id)
    {
        $request->validate([
            'justificativa' => 'required|string|max:1000',
        ]);

        // Verifica se já existe uma indicação
        $existe = IndicacaoPrograma::where('programa_id', $programa_id)
            ->where('cidadao_id', $cidadao_id)
            ->exists();

        if ($existe) {
            return redirect()
                ->route('assistente.programas.indicar', $programa_id)
                ->with('success', 'Este cidadão já foi indicado para este programa.');
        }

        // Cria nova indicação
        IndicacaoPrograma::create([
            'programa_id' => $programa_id,
            'cidadao_id' => $cidadao_id,
            'user_id' => Auth::id(),
            'justificativa' => $request->justificativa,
            'status' => 'pendente',
            'indicado_em' => now(),
        ]);

        return redirect()
            ->route('assistente.programas.indicar', $programa_id)
            ->with('success', 'Indicação registrada com sucesso!');
    }





    // Histórico de denúncias feitas pelo assistente
    public function historico()
    {
        $userId = auth()->id();

        $denuncias = DenunciaPrograma::with(['cidadao', 'programa'])
            ->where('user_id', $userId)
            ->get();

        $indicacoes = IndicacaoPrograma::with(['cidadao', 'programa'])
            ->where('user_id', $userId)
            ->get();

        return view('assistente.programas.historico', compact('denuncias', 'indicacoes'));
    }
}
