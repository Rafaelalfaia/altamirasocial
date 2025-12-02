<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Programa;
use App\Models\ProgramaInscricao;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



class ProgramaController extends Controller
{
    /** Regiões válidas para validação */
    private array $REGIOES_VALIDAS = ['Altamira', 'Castelo dos Sonhos e Cachoeira da Serra'];


    public function index()
    {
        $programas = Programa::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('coordenador.programas.index', compact('programas'));
    }

    public function create()
    {
        return view('coordenador.programas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome'           => 'required|string|max:255',
            'descricao'      => 'nullable|string',
            'vagas'          => 'required|integer|min:1',
            'valor_corte'    => 'nullable|numeric|min:0',
            'publico_alvo'   => 'nullable|string|max:255',
            'status'         => 'required|in:ativado,desativado',
            'aceita_menores' => 'nullable|boolean',
            'regioes'        => 'nullable|array',
            'regioes.*'      => 'in:' . implode(',', $this->REGIOES_VALIDAS),
            'foto_perfil'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_capa'      => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $dados = $request->only(['nome','descricao','vagas','publico_alvo','status']);
        $dados['valor_corte']    = (float) $request->input('valor_corte', 0.00);
        $dados['regioes']        = $request->input('regioes', []);
        $dados['aceita_menores'] = $request->boolean('aceita_menores');

        if ($request->hasFile('foto_perfil')) {
            $dados['foto_perfil'] = $request->file('foto_perfil')->store('programas', 'public');
        }
        if ($request->hasFile('foto_capa')) {
            $dados['foto_capa'] = $request->file('foto_capa')->store('programas', 'public');
        }

        auth()->user()->programas()->create($dados);

        return redirect()->route('coordenador.programas.index')
            ->with('success', 'Programa criado com sucesso.');
    }

    public function edit(Programa $programa)
    {
        $this->authorize('update', $programa);
        return view('coordenador.programas.edit', compact('programa'));
    }

    public function update(Request $request, Programa $programa)
    {
        $this->authorize('update', $programa);

        $request->validate([
            'nome'           => 'required|string|max:255',
            'descricao'      => 'nullable|string',
            'vagas'          => 'required|integer|min:1',
            'valor_corte'    => 'nullable|numeric|min:0',
            'publico_alvo'   => 'nullable|string|max:255',
            'status'         => 'required|in:ativado,desativado',
            'aceita_menores' => 'nullable|boolean',
            'regioes'        => 'required|array|min:1',
            'regioes.*'      => 'in:' . implode(',', $this->REGIOES_VALIDAS),
            'foto_perfil'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_capa'      => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $dados = $request->only(['nome','descricao','vagas','publico_alvo','status']);
        $dados['valor_corte']    = (float) $request->input('valor_corte', 0.00);
        $dados['regioes']        = $request->input('regioes');
        $dados['aceita_menores'] = $request->boolean('aceita_menores');

        if ($request->hasFile('foto_perfil')) {
            if ($programa->foto_perfil) Storage::disk('public')->delete($programa->foto_perfil);
            $dados['foto_perfil'] = $request->file('foto_perfil')->store('programas', 'public');
        }
        if ($request->hasFile('foto_capa')) {
            if ($programa->foto_capa) Storage::disk('public')->delete($programa->foto_capa);
            $dados['foto_capa'] = $request->file('foto_capa')->store('programas', 'public');
        }

        $programa->update($dados);

        return redirect()->route('coordenador.programas.index')
            ->with('success', 'Programa atualizado com sucesso.');
    }

    public function destroy(Programa $programa)
    {
        $this->authorize('delete', $programa);

        $arquivos = array_filter([$programa->foto_perfil, $programa->foto_capa]);
        if (!empty($arquivos)) {
            Storage::disk('public')->delete($arquivos);
        }

        $programa->delete();

        return redirect()->route('coordenador.programas.index')
            ->with('success', 'Programa excluído com sucesso.');
    }

    /** ------- LISTA / PDF DE INSCRITOS ------- */

    private function queryInscricoesBase(Programa $programa)
    {
        $inscTable = (new ProgramaInscricao)->getTable();

        return ProgramaInscricao::from($inscTable . ' as pi')
            ->where('pi.programa_id', $programa->id)
            ->leftJoin('cidadaos', 'cidadaos.id', '=', 'pi.cidadao_id')
            // Dependentes globais via dependente_id
            ->leftJoin('dependentes as dep', 'dep.id', '=', 'pi.dependente_id')
            // <<< NOVO: bairro do CIDADÃO (responsável/hospedeiro do domicílio)
            ->leftJoin('bairros as b', 'b.id', '=', 'cidadaos.bairro_id')
            ->select([
                'pi.*',
                // Campos auxiliares para view/filtro
                'cidadaos.bairro_id as cid_bairro_id',
                'b.nome as bairro_nome',
            ])
            ->with(['cidadao','dependente']); // Eloquent carrega relações
    }




    public function inscritos(Programa $programa, Request $request)
{
    $this->authorize('view', $programa);

    $status    = in_array($request->query('status'), ['aprovado','pendente','reprovado']) ? $request->query('status') : null;
    $q         = (string) $request->query('q', '');
    $regiao    = (string) $request->query('regiao', '');
    $ordem     = (string) $request->query('ordem', 'az');
    $perPage   = (int) $request->query('per_page', 20);
    $dir       = $ordem === 'za' ? 'DESC' : 'ASC';

    // <<< NOVO: bairro_id (único) vindo da UI
    $bairroId  = $request->integer('bairro_id');

    // Regiões (como já estava)
    $regioes = ProgramaInscricao::where('programa_id', $programa->id)
        ->whereNotNull('regiao')
        ->distinct()
        ->orderBy('regiao')
        ->pluck('regiao');

    // <<< NOVO: Bairros distintos presentes nas inscrições deste programa (via cidadão)
    $bairros = ProgramaInscricao::from('programa_inscricoes as pi')
        ->where('pi.programa_id', $programa->id)
        ->leftJoin('cidadaos', 'cidadaos.id', '=', 'pi.cidadao_id')
        ->leftJoin('bairros as b', 'b.id', '=', 'cidadaos.bairro_id')
        ->whereNotNull('cidadaos.bairro_id')
        ->select('cidadaos.bairro_id as id', 'b.nome')
        ->distinct()
        ->orderBy('b.nome')
        ->get();

    // BASE para métricas (aplica busca, região e bairro, mas NÃO aplica status)
    $base = $this->queryInscricoesBase($programa);

    if ($q !== '') {
        $like   = '%'.$q.'%';
        $digits = preg_replace('/\D+/', '', $q);

        $base->where(function ($w) use ($like, $digits) {
            $w->where('dep.nome', 'like', $like)
              ->orWhere('cidadaos.nome', 'like', $like);

            if ($digits && strlen($digits) >= 3) {
                $w->orWhereRaw('REPLACE(REPLACE(REPLACE(cidadaos.cpf, ".", ""), "-", ""), " ", "") LIKE ?', ["%{$digits}%"]);
            }
        });
    }

    if ($regiao !== '') {
        $base->where('pi.regiao', $regiao);
    }

    // <<< NOVO: filtro de bairro
    if ($bairroId) {
        $base->where('cidadaos.bairro_id', $bairroId);
    }

    // Métricas (no escopo busca/região/bairro)
    $total   = (clone $base)->count();
    $grouped = (clone $base)->select('pi.status', DB::raw('COUNT(*) as total'))
                            ->groupBy('pi.status')->pluck('total','pi.status');

    $metrics = [
        'total'     => $total,
        'aprovado'  => $grouped['aprovado']  ?? 0,
        'pendente'  => $grouped['pendente']  ?? 0,
        'reprovado' => $grouped['reprovado'] ?? 0,
    ];

    // LISTA (aplica o status selecionado — se houver)
    $query = (clone $base);
    if ($status) {
        $query->where('pi.status', $status);
    }

    $query->orderByRaw('LOWER(COALESCE(dep.nome, cidadaos.nome)) '.$dir);

    $inscricoes = $query->paginate($perPage)->withQueryString();

    return view('coordenador.programas.inscritos', [
        'programa'          => $programa,
        'inscricoes'        => $inscricoes,
        'statusSelecionado' => $status,
        'aceitaDependentes' => $programa->aceita_menores,
        'regioes'           => $regioes,
        'bairros'           => $bairros,             // <<< NOVO
        'bairroSelecionado' => $bairroId ?: null,    // <<< NOVO
        'metrics'           => $metrics,
    ]);
}





    public function destroyInscricao(Programa $programa, ProgramaInscricao $inscricao)
    {
        $this->autorizaInscricao($programa, $inscricao);

        // Não deletar o dependente global (tabela dependentes); apenas a inscrição
        $inscricao->delete();

        return back()->with('success', 'Inscrição excluída com sucesso.');
    }



    public function baixarPdf(Programa $programa, Request $request)
    {
        $this->authorize('view', $programa);

        $status   = in_array($request->query('status'), ['aprovado','pendente','reprovado']) ? $request->query('status') : null;
        $q        = (string) $request->query('q', '');
        $regiao   = (string) $request->query('regiao', '');
        $ordem    = (string) $request->query('ordem', 'az');
        $dir      = $ordem === 'za' ? 'DESC' : 'ASC';
        $bairroId = $request->integer('bairro_id'); // <<< NOVO

        // BASE para métricas (aplica busca/região/bairro, NÃO status)
        $base = $this->queryInscricoesBase($programa);

        if ($q !== '') {
            $like   = '%'.$q.'%';
            $digits = preg_replace('/\D+/', '', $q);

            $base->where(function ($w) use ($like, $digits) {
                $w->where('dep.nome', 'like', $like)
                ->orWhere('cidadaos.nome', 'like', $like);

                if ($digits && strlen($digits) >= 3) {
                    $w->orWhereRaw('REPLACE(REPLACE(REPLACE(cidadaos.cpf, ".", ""), "-", ""), " ", "") LIKE ?', ["%{$digits}%"]);
                }
            });
        }

        if ($regiao !== '') {
            $base->where('pi.regiao', $regiao);
        }

        if ($bairroId) { // <<< NOVO
            $base->where('cidadaos.bairro_id', $bairroId);
        }

        $total   = (clone $base)->count();
        $grouped = (clone $base)->select('pi.status', DB::raw('COUNT(*) as total'))
                                ->groupBy('pi.status')->pluck('total','pi.status');

        $metrics = [
            'escopo'     => 'filtro',
            'total'      => $total,
            'aprovado'   => $grouped['aprovado']  ?? 0,
            'pendente'   => $grouped['pendente']  ?? 0,
            'reprovado'  => $grouped['reprovado'] ?? 0,
        ];

        // LISTA (se tiver status selecionado, filtra lista; métricas continuam gerais)
        $list = (clone $base);
        if ($status) {
            $list->where('pi.status', $status);
        }
        $inscricoes = $list->orderByRaw('LOWER(COALESCE(dep.nome, cidadaos.nome)) '.$dir)->get();

        $filtros = [
            'status'    => $status,
            'q'         => $q ?: null,
            'regiao'    => $regiao ?: null,
            'bairro_id' => $bairroId ?: null, // <<< NOVO
            'ordem'     => $ordem,
        ];

        $pdf = Pdf::loadView('coordenador.programas.inscritos-pdf', [
            'programa'   => $programa,
            'inscricoes' => $inscricoes,
            'metrics'    => $metrics,
            'filtros'    => $filtros,
        ]);

        return $pdf->download('inscritos_'.($status ?? 'todos').'_'.Str::slug($programa->nome).'.pdf');
    }



    /** ------- Ações individuais ------- */

    public function aprovar(Programa $programa, ProgramaInscricao $inscricao)
    {
        $this->autorizaInscricao($programa, $inscricao);
        $inscricao->update(['status' => 'aprovado']);
        return back()->with('success', 'Inscrição aprovada com sucesso.');
    }

    public function reprovar(Programa $programa, ProgramaInscricao $inscricao)
    {
        $this->autorizaInscricao($programa, $inscricao);
        $inscricao->update(['status' => 'reprovado']);
        return back()->with('success', 'Inscrição reprovada com sucesso.');
    }

    public function atualizarInscricao(Request $request, Programa $programa, ProgramaInscricao $inscricao)
    {
        $this->autorizaInscricao($programa, $inscricao);

        $request->validate([
            'status' => 'required|in:aprovado,pendente,reprovado',
        ]);

        $inscricao->update(['status' => $request->status]);

        return back()->with('success', 'Status da inscrição atualizado com sucesso.');
    }

    /** ------- Ação em LOTE ------- */
    public function bulkStatus(Request $request, Programa $programa)
    {
        $this->authorize('update', $programa);

        $dados = $request->validate([
            'ids'        => 'required|array|min:1',
            'ids.*'      => 'integer',
            'status'     => 'required|in:aprovado,pendente,reprovado',
        ]);

        $afetadas = ProgramaInscricao::where('programa_id', $programa->id)
            ->whereIn('id', $dados['ids'])
            ->update([
                'status'     => $dados['status'],
                'updated_at' => now(),
            ]);

        return back()->with('success', "Status atualizado para \"{$dados['status']}\" em {$afetadas} inscrição(ões).");
    }

    public function editInscricao(Programa $programa, ProgramaInscricao $inscricao)
    {
        $this->autorizaInscricao($programa, $inscricao);
        return view('coordenador.programas.inscricoes.edit', [ // caminho correto
            'programa'   => $programa,
            'inscricao'  => $inscricao->load('cidadao','dependente'),
        ]);
    }



    public function updateInscricao(Request $request, Programa $programa, ProgramaInscricao $inscricao)
    {
        $this->autorizaInscricao($programa, $inscricao);

        $rules = [
            'status'   => 'required|in:aprovado,pendente,reprovado',
            'regiao'   => 'nullable|string|max:255',
            // campos editáveis do dependente (se existir)
            'dependente.nome'            => 'nullable|string|max:255',
            'dependente.cpf'             => 'nullable|digits:11',
            'dependente.grau_parentesco' => 'nullable|string|max:50',
        ];

        $data = $request->validate($rules);

        // Atualiza a inscrição
        $inscricao->update([
            'status' => $data['status'],
            'regiao' => $data['regiao'] ?? $inscricao->regiao,
        ]);

        // Se existir dependente associado, atualiza campos permitidos
        if ($programa->aceita_menores && $inscricao->dependente) {
            $payloadDep = array_filter($data['dependente'] ?? []);
            if (!empty($payloadDep)) {
                $inscricao->dependente->update($payloadDep);
            }
        }

        return redirect()
            ->route('coordenador.programas.inscricoes.show', [$programa->id, $inscricao->id])
            ->with('success', 'Inscrição atualizada com sucesso.');
    }

    public function showInscricao(Programa $programa, ProgramaInscricao $inscricao)
    {
        $this->autorizaInscricao($programa, $inscricao);

        // Carrega as relações usadas no endereço
        $inscricao->load(['cidadao.bairro.cidade.estado', 'dependente']);

        return view('coordenador.programas.inscricoes.show', [
            'programa'  => $programa,
            'inscricao' => $inscricao,
        ]);
    }

    private function autorizaInscricao(Programa $programa, ProgramaInscricao $inscricao)
    {
        $this->authorize('update', $programa);

        if ($inscricao->programa_id !== $programa->id) {
            abort(403, 'Inscrição não pertence a este programa.');
        }
    }
}
