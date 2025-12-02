<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LotePagamento;
use App\Models\Programa;
use App\Exports\LotePagamentoExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class LotePagamentoController extends Controller
{
    public function index()
    {
        $lotes = LotePagamento::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('coordenador.lotes.index', compact('lotes'));
    }

    public function create()
    {
        $programas = Programa::where('status', 'ativado')
            ->where('user_id', auth()->id())
            ->get();

        return view('coordenador.lotes.create', compact('programas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'programa_id' => 'required|exists:programas,id',
            'valor_pagamento' => 'required|numeric|min:0',
            'formato_cpf' => 'required|in:com_pontos,sem_pontos',
            'periodo_pagamento' => 'required|string|max:255',
            'regiao' => 'required|string|max:255',
        ]);

        LotePagamento::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'status' => 'Pendente',
            'data_envio' => now(),
            'programa_id' => $request->programa_id,
            'valor_pagamento' => $request->valor_pagamento,
            'formato_cpf' => $request->formato_cpf,
            'periodo_pagamento' => $request->periodo_pagamento,
            'regiao' => $request->regiao,
            'previsao_pagamento' => $request->input('previsao_pagamento'),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('coordenador.lotes.index')->with('success', 'Lote criado com sucesso.');
    }

    public function edit(LotePagamento $lote)
    {
        $this->authorizeAcesso($lote);

        $programas = Programa::where('status', 'ativado')
            ->where('user_id', auth()->id())
            ->get();

        return view('coordenador.lotes.edit', compact('lote', 'programas'));
    }

    public function update(Request $request, LotePagamento $lote)
    {
        $this->authorizeAcesso($lote);

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'programa_id' => 'required|exists:programas,id',
            'valor_pagamento' => 'required|numeric|min:0',
            'formato_cpf' => 'required|in:com_pontos,sem_pontos',
            'periodo_pagamento' => 'required|string|max:255',
            'regiao' => 'required|string|max:255',
        ]);

        $lote->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'programa_id' => $request->programa_id,
            'valor_pagamento' => $request->valor_pagamento,
            'formato_cpf' => $request->formato_cpf,
            'periodo_pagamento' => $request->periodo_pagamento,
            'regiao' => $request->regiao,
        ]);

        return redirect()->route('coordenador.lotes.index')->with('success', 'Lote atualizado com sucesso.');
    }

    public function exportar($id)
    {
        try {
            $lote = LotePagamento::with(['inscricoes.cidadao', 'programa'])->findOrFail($id);
            $this->authorizeAcesso($lote);

            return Excel::download(new LotePagamentoExport($lote), 'lote_pagamento_' . $lote->id . '.xlsx');
        } catch (\Throwable $e) {
            Log::error('Erro ao exportar lote: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Não foi possível exportar o lote.');
        }
    }

    public function baixar(LotePagamento $lote)
    {
        try {
            $this->authorizeAcesso($lote);

            $lote = LotePagamento::with(['inscricoes.cidadao', 'programa'])->findOrFail($lote->id);
            return Excel::download(new LotePagamentoExport($lote), 'lote_pagamento_' . $lote->id . '.xlsx');
        } catch (\Throwable $e) {
            Log::error('Erro ao baixar lote: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Não foi possível gerar o download do lote.');
        }
    }

    public function destroy(LotePagamento $lote)
    {
        $this->authorizeAcesso($lote);

        $lote->delete();

        return redirect()->route('coordenador.lotes.index')->with('success', 'Lote excluído com sucesso.');
    }

    /**
     * Confirma que o lote pertence ao coordenador logado.
     */
    private function authorizeAcesso(LotePagamento $lote)
    {
        if ($lote->user_id !== auth()->id()) {
            abort(403, 'Acesso negado.');
        }
    }
}
