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
use Carbon\Carbon;
use App\Models\Cidadao;




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

    public function indicadores(Programa $programa, Request $request)
    {
        $this->authorize('view', $programa);

        $q = trim((string) $request->query('q', ''));
        $cidadaosIdsSelecionados = (array) $request->input('cidadaos', []);

        // Base: inscrições do programa
        $inscricoesBase = ProgramaInscricao::where('programa_id', $programa->id);

        $metricsPrograma = [
            'total_inscricoes' => (clone $inscricoesBase)->count(),
            'aprovadas'        => (clone $inscricoesBase)->where('status', 'aprovado')->count(),
            'pendentes'        => (clone $inscricoesBase)->where('status', 'pendente')->count(),
            'reprovadas'       => (clone $inscricoesBase)->where('status', 'reprovado')->count(),
        ];

        // Carrega todos os cidadãos que têm inscrição nesse programa
        $cidadaosPrograma = Cidadao::with([
                'bairro',
                'acompanhamentos',
                'ultimoAcompanhamento',
                'inscricoes.programa',
            ])
            ->whereHas('inscricoes', function ($q2) use ($programa) {
                $q2->where('programa_id', $programa->id);
            })
            ->get();

        // ---------- PERFIL GERAL DO PROGRAMA ----------

        // Sexo
        $sexoPrograma = [
            'Masculino'             => 0,
            'Feminino'              => 0,
            'Outro/Não informado'   => 0,
        ];

        // Renda familiar por faixa
        $rendaFaixasPrograma = [
            'Até R$ 600'        => 0,
            'R$ 601–1.200'      => 0,
            'R$ 1.201–2.000'    => 0,
            'Acima de R$ 2.000' => 0,
            'Não informado'     => 0,
        ];

        // Idade por faixa
        $idadeFaixasPrograma = [
            '0–12'           => 0,
            '13–17'          => 0,
            '18–24'          => 0,
            '25–39'          => 0,
            '40–59'          => 0,
            '60+'            => 0,
            'Não informado'  => 0,
        ];

        // PCD
        $pcdPrograma = [
            'Com deficiência'  => 0,
            'Sem deficiência'  => 0,
        ];

        // Escolaridade / Bairros / Emprego (situação profissional)
        $escolaridadePrograma = [];
        $bairrosPrograma = [];
        $empregoPrograma = [];

        // Acompanhamentos
        $totalAcompPrograma = 0;
        $acompanhamentoDistribuicao = [
            '0 atend.'   => 0,
            '1–2 atend.' => 0,
            '3–5 atend.' => 0,
            '6+ atend.'  => 0,
        ];

        // Participação em outros programas
        $cidadaosComOutrosProgramas = 0;
        $outrosProgramasContagem = [];

        $hoje = Carbon::now();
        $idadesParaMedia = [];
        $rendasParaMedia = [];
        $rendasPerCapitaParaMedia = [];

        foreach ($cidadaosPrograma as $cid) {
            // Sexo
            $sexo = strtolower(trim((string) $cid->sexo));
            if (in_array($sexo, ['masculino', 'm'])) {
                $sexoPrograma['Masculino']++;
            } elseif (in_array($sexo, ['feminino', 'f'])) {
                $sexoPrograma['Feminino']++;
            } else {
                $sexoPrograma['Outro/Não informado']++;
            }

            // Renda familiar
            $renda = $cid->renda_total_familiar;
            if (is_null($renda)) {
                $rendaFaixasPrograma['Não informado']++;
            } else {
                $renda = (float) $renda;
                $rendasParaMedia[] = $renda;

                if ($renda <= 600) {
                    $rendaFaixasPrograma['Até R$ 600']++;
                } elseif ($renda <= 1200) {
                    $rendaFaixasPrograma['R$ 601–1.200']++;
                } elseif ($renda <= 2000) {
                    $rendaFaixasPrograma['R$ 1.201–2.000']++;
                } else {
                    $rendaFaixasPrograma['Acima de R$ 2.000']++;
                }
            }

            // Renda per capita (se existir campo/atributo)
            if (!is_null($cid->renda_per_capita ?? null)) {
                $rendasPerCapitaParaMedia[] = (float) $cid->renda_per_capita;
            }

            // Idade
            if ($cid->data_nascimento) {
                try {
                    $idade = Carbon::parse($cid->data_nascimento)->age;
                    $idadesParaMedia[] = $idade;

                    if ($idade <= 12) {
                        $idadeFaixasPrograma['0–12']++;
                    } elseif ($idade <= 17) {
                        $idadeFaixasPrograma['13–17']++;
                    } elseif ($idade <= 24) {
                        $idadeFaixasPrograma['18–24']++;
                    } elseif ($idade <= 39) {
                        $idadeFaixasPrograma['25–39']++;
                    } elseif ($idade <= 59) {
                        $idadeFaixasPrograma['40–59']++;
                    } else {
                        $idadeFaixasPrograma['60+']++;
                    }
                } catch (\Exception $e) {
                    $idadeFaixasPrograma['Não informado']++;
                }
            } else {
                $idadeFaixasPrograma['Não informado']++;
            }

            // PCD
            $temDeficiencia = (bool) ($cid->pcd ?? false) || (bool) ($cid->possui_deficiencia ?? false);
            if ($temDeficiencia) {
                $pcdPrograma['Com deficiência']++;
            } else {
                $pcdPrograma['Sem deficiência']++;
            }

            // Escolaridade
            $esc = trim((string) $cid->escolaridade);
            if ($esc === '') {
                $esc = 'Não informada';
            }
            $escolaridadePrograma[$esc] = ($escolaridadePrograma[$esc] ?? 0) + 1;

            // Bairros
            $bairroNome = optional($cid->bairro)->nome ?? ($cid->bairro ?? 'Não informado');
            $bairrosPrograma[$bairroNome] = ($bairrosPrograma[$bairroNome] ?? 0) + 1;

            // Emprego / situação profissional
            $situacao = optional($cid->ultimoAcompanhamento)->situacao_profissional ?: ($cid->ocupacao ?? '');
            $situacao = trim((string) $situacao);
            if ($situacao === '') {
                $situacao = 'Não informado';
            }
            $empregoPrograma[$situacao] = ($empregoPrograma[$situacao] ?? 0) + 1;

            // Acompanhamentos
            $qtdAcomp = $cid->acompanhamentos->count();
            $totalAcompPrograma += $qtdAcomp;

            if ($qtdAcomp === 0) {
                $acompanhamentoDistribuicao['0 atend.']++;
            } elseif ($qtdAcomp <= 2) {
                $acompanhamentoDistribuicao['1–2 atend.']++;
            } elseif ($qtdAcomp <= 5) {
                $acompanhamentoDistribuicao['3–5 atend.']++;
            } else {
                $acompanhamentoDistribuicao['6+ atend.']++;
            }

            // Participação em outros programas
            $outros = $cid->inscricoes->where('programa_id', '!=', $programa->id);

            if ($outros->isNotEmpty()) {
                $cidadaosComOutrosProgramas++;

                foreach ($outros as $insc) {
                    $nomeProg = optional($insc->programa)->nome ?? ('Programa ' . $insc->programa_id);
                    $outrosProgramasContagem[$nomeProg] = ($outrosProgramasContagem[$nomeProg] ?? 0) + 1;
                }
            }
        }

        // Médias gerais do programa
        $metricsProgramaExtra = [
            'idade_media'            => !empty($idadesParaMedia) ? round(array_sum($idadesParaMedia) / count($idadesParaMedia), 1) : null,
            'renda_media_familiar'   => !empty($rendasParaMedia) ? round(array_sum($rendasParaMedia) / count($rendasParaMedia), 2) : null,
            'renda_media_per_capita' => !empty($rendasPerCapitaParaMedia) ? round(array_sum($rendasPerCapitaParaMedia) / count($rendasPerCapitaParaMedia), 2) : null,
            'media_acompanhamentos'  => $cidadaosPrograma->count() > 0 ? round($totalAcompPrograma / $cidadaosPrograma->count(), 1) : null,
            'cidadaos_outros_programas' => $cidadaosComOutrosProgramas,
            'total_cidadaos_programa'   => $cidadaosPrograma->count(),
        ];

        $escolaridadePrograma = collect($escolaridadePrograma)->sortDesc();
        $bairrosPrograma = collect($bairrosPrograma)->sortDesc()->take(10);
        $empregoPrograma = collect($empregoPrograma)->sortDesc()->take(6);
        $outrosProgramasTop = collect($outrosProgramasContagem)->sortDesc()->take(6);

        // ---------- BUSCA DE CIDADÃOS + SELEÇÃO ----------

        $cidadaosBusca = collect();
        if ($q !== '') {
            $cidadaosBusca = Cidadao::query()
                ->whereHas('inscricoes', function ($query) use ($programa) {
                    $query->where('programa_id', $programa->id);
                })
                ->where(function ($query) use ($q) {
                    $query->where('nome', 'like', "%{$q}%")
                        ->orWhere('cpf', 'like', "%{$q}%");
                })
                ->orderBy('nome')
                ->limit(30)
                ->get();
        }

        $cidadaosSelecionados = collect();
        $metricsCidadaos = [
            'total'                     => 0,
            'idade_media'               => null,
            'renda_media_familiar'      => null,
            'renda_media_per_capita'    => null,
            'total_acompanhamentos'     => 0,
            'total_programas_distintos' => 0,
        ];

        $chartProgramasCidadaos = [
            'labels' => [],
            'data'   => [],
        ];

        $sexoSelecionados = [
            'Masculino'             => 0,
            'Feminino'              => 0,
            'Outro/Não informado'   => 0,
        ];
        $idadeFaixasSelecionados = [
            '0–12'           => 0,
            '13–17'          => 0,
            '18–24'          => 0,
            '25–39'          => 0,
            '40–59'          => 0,
            '60+'            => 0,
            'Não informado'  => 0,
        ];
        $pcdSelecionados = [
            'Com deficiência' => 0,
            'Sem deficiência' => 0,
        ];

        if (!empty($cidadaosIdsSelecionados)) {
            $cidadaosSelecionados = Cidadao::with(['acompanhamentos', 'inscricoes.programa'])
                ->whereIn('id', $cidadaosIdsSelecionados)
                ->get();

            if ($cidadaosSelecionados->isNotEmpty()) {
                $metricsCidadaos['total'] = $cidadaosSelecionados->count();

                $idadesSel = [];
                $rendaSel = [];
                $rendaPerCapSel = [];

                foreach ($cidadaosSelecionados as $cid) {
                    // idade
                    if ($cid->data_nascimento) {
                        try {
                            $idade = Carbon::parse($cid->data_nascimento)->age;
                            $idadesSel[] = $idade;

                            if ($idade <= 12) {
                                $idadeFaixasSelecionados['0–12']++;
                            } elseif ($idade <= 17) {
                                $idadeFaixasSelecionados['13–17']++;
                            } elseif ($idade <= 24) {
                                $idadeFaixasSelecionados['18–24']++;
                            } elseif ($idade <= 39) {
                                $idadeFaixasSelecionados['25–39']++;
                            } elseif ($idade <= 59) {
                                $idadeFaixasSelecionados['40–59']++;
                            } else {
                                $idadeFaixasSelecionados['60+']++;
                            }
                        } catch (\Exception $e) {
                            $idadeFaixasSelecionados['Não informado']++;
                        }
                    } else {
                        $idadeFaixasSelecionados['Não informado']++;
                    }

                    // renda
                    if (!is_null($cid->renda_total_familiar)) {
                        $rendaSel[] = (float) $cid->renda_total_familiar;
                    }
                    if (!is_null($cid->renda_per_capita ?? null)) {
                        $rendaPerCapSel[] = (float) $cid->renda_per_capita;
                    }

                    // sexo
                    $sexo = strtolower(trim((string) $cid->sexo));
                    if (in_array($sexo, ['masculino', 'm'])) {
                        $sexoSelecionados['Masculino']++;
                    } elseif (in_array($sexo, ['feminino', 'f'])) {
                        $sexoSelecionados['Feminino']++;
                    } else {
                        $sexoSelecionados['Outro/Não informado']++;
                    }

                    // PCD
                    $temDef = (bool) ($cid->pcd ?? false) || (bool) ($cid->possui_deficiencia ?? false);
                    if ($temDef) {
                        $pcdSelecionados['Com deficiência']++;
                    } else {
                        $pcdSelecionados['Sem deficiência']++;
                    }
                }

                if (!empty($idadesSel)) {
                    $metricsCidadaos['idade_media'] = round(array_sum($idadesSel) / count($idadesSel), 1);
                }
                if (!empty($rendaSel)) {
                    $metricsCidadaos['renda_media_familiar'] = round(array_sum($rendaSel) / count($rendaSel), 2);
                }
                if (!empty($rendaPerCapSel)) {
                    $metricsCidadaos['renda_media_per_capita'] = round(array_sum($rendaPerCapSel) / count($rendaPerCapSel), 2);
                }

                $metricsCidadaos['total_acompanhamentos'] = $cidadaosSelecionados
                    ->sum(fn ($c) => $c->acompanhamentos->count());

                $programasIds = $cidadaosSelecionados
                    ->flatMap(fn ($c) => $c->inscricoes->pluck('programa_id'))
                    ->unique();

                $metricsCidadaos['total_programas_distintos'] = $programasIds->count();

                $programasAgrupados = $cidadaosSelecionados
                    ->flatMap(fn ($c) => $c->inscricoes)
                    ->groupBy('programa_id');

                $chartProgramasCidadaos['labels'] = $programasAgrupados
                    ->map(function ($inscricoes) {
                        $primeira = $inscricoes->first();
                        return optional($primeira->programa)->nome ?? 'Programa ' . $primeira->programa_id;
                    })
                    ->values()
                    ->all();

                $chartProgramasCidadaos['data'] = $programasAgrupados
                    ->map->count()
                    ->values()
                    ->all();
            }
        }

        return view('coordenador.programas.indicadores', compact(
            'programa',
            'metricsPrograma',
            'metricsProgramaExtra',
            'sexoPrograma',
            'rendaFaixasPrograma',
            'idadeFaixasPrograma',
            'pcdPrograma',
            'escolaridadePrograma',
            'bairrosPrograma',
            'empregoPrograma',
            'acompanhamentoDistribuicao',
            'outrosProgramasTop',
            'cidadaosBusca',
            'q',
            'cidadaosSelecionados',
            'cidadaosIdsSelecionados',
            'metricsCidadaos',
            'chartProgramasCidadaos',
            'sexoSelecionados',
            'idadeFaixasSelecionados',
            'pcdSelecionados'
        ));
    }



    private function autorizaInscricao(Programa $programa, ProgramaInscricao $inscricao)
    {
        $this->authorize('update', $programa);

        if ($inscricao->programa_id !== $programa->id) {
            abort(403, 'Inscrição não pertence a este programa.');
        }
    }
}
