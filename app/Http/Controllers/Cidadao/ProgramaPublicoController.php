<?php

namespace App\Http\Controllers\Cidadao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Programa;

class ProgramaPublicoController extends Controller
{
    /**
     * Lista pública (para cidadão): programas ativados, com busca.
     */
    public function index(Request $request)
{
    $busca = trim((string) $request->input('busca'));

    $programas = Programa::query()
        ->where('status', 'ativado')
        ->when($busca !== '', function ($q) use ($busca) {
            $q->where(function ($qq) use ($busca) {
                $qq->where('nome', 'like', "%{$busca}%")
                   ->orWhere('descricao', 'like', "%{$busca}%");
            });
        })
        // 👇 ordenação pedida: recomendados no topo
        ->orderByDesc('recomendado')
        ->orderByRaw('COALESCE(recomendacao_ordem, 999999) ASC')
        ->orderByDesc('created_at')
        ->paginate(12)
        ->withQueryString();

    return view('cidadao.programas.index', compact('programas', 'busca'));
}


    /**
     * Detalhe do programa (cidadão).
     * Bloqueia o formulário apenas se faltar um dos 5 campos mínimos.
     */
    public function ver(Programa $programa)
    {
        if ($programa->status !== 'ativado') {
            abort(404);
        }

        $cidadao = auth()->user()?->cidadao;

        $mensagem           = null;
        $jaInscrito         = false;
        $dependentes        = collect();
        $bloquearInscricao  = false;

        if (!$cidadao || !$cidadao->temDadosMinimosParaInscricao()) {
            $faltando = $cidadao
                ? $cidadao->faltandoDadosMinimos()
                : ['Nome Completo','CPF','Bairro','Renda Total Familiar (R$)','Pessoas na Residência'];

            $mensagem = 'Preencha os dados obrigatórios do titular: ' . implode(', ', $faltando) . '.';
            $bloquearInscricao = true;
        } else {
            $jaInscrito = $programa->inscricoes()
                ->where('cidadao_id', $cidadao->id)
                ->exists();

            $dependentes = $cidadao->dependentes;
        }

        return view('cidadao.programas.ver', compact(
            'programa', 'mensagem', 'jaInscrito', 'dependentes', 'bloquearInscricao'
        ));
    }

    /**
     * Inscrição do titular e/ou múltiplos dependentes.
     * Aplica CORTE automático por renda per capita do titular:
     *  - 'reprovado' se renda_per_capita > valor_corte do programa
     *  - 'pendente'  caso contrário
     */
    public function inscrever(Request $request, Programa $programa)
    {
        if ($programa->status !== 'ativado') {
            abort(404);
        }

        $cidadao = $request->user()?->cidadao;

        // Bloqueia somente se faltarem os 5 campos mínimos
        if (!$cidadao || !$cidadao->temDadosMinimosParaInscricao()) {
            $faltando = $cidadao
                ? $cidadao->faltandoDadosMinimos()
                : ['Nome Completo','CPF','Bairro','Renda Total Familiar (R$)','Pessoas na Residência'];

            return back()->with('mensagem', 'Para se inscrever, preencha: ' . implode(', ', $faltando) . '.');
        }

        // Normaliza lista de regiões do programa (json/array/csv/string)
        $regioes = $this->normalizarRegioes($programa->regioes);

        // Validação de entrada do formulário
        $rules = [
            'inscrever_titular' => ['nullable','boolean'],
            'dependentes'       => ['nullable','array'],
            'dependentes.*'     => [
                'integer',
                Rule::exists('dependentes','id')->where(fn($q) => $q->where('cidadao_id', $cidadao->id)),
            ],
        ];
        if (!empty($regioes)) {
            $rules['regiao'] = ['required', Rule::in($regioes)];
        } else {
            $rules['regiao'] = ['nullable','string','max:100'];
        }

        $data = $request->validate($rules);

        $inscreverTitular = (bool) ($data['inscrever_titular'] ?? false);
        $depIds = collect($data['dependentes'] ?? [])->filter()->unique()->values();

        // Se o programa não aceita menores, ignora dependentes
        if (!$programa->aceita_menores) {
            $depIds = collect();
        }

        if (!$inscreverTitular && $depIds->isEmpty()) {
            return back()->withErrors([
                'dependentes' => 'Selecione ao menos uma opção (titular e/ou dependente).'
            ])->withInput();
        }

        // === CORTE por RENDA PER CAPITA (do titular) ===
        $rendaPerCapita = (float) $cidadao->renda_per_capita;
        $temCorte   = !is_null($programa->valor_corte) && $programa->valor_corte !== '';
        $valorCorte = $temCorte ? (float) $programa->valor_corte : null;

        $statusInicial = ($temCorte && $rendaPerCapita > $valorCorte) ? 'reprovado' : 'pendente';

        // Criação idempotente (evita duplicidade)
        $criados = 0;
        $jaExistiam = 0;

        DB::transaction(function () use ($programa, $cidadao, $inscreverTitular, $depIds, $data, $statusInicial, &$criados, &$jaExistiam) {
            // Titular
            if ($inscreverTitular) {
                $exists = $programa->inscricoes()
                    ->where('cidadao_id', $cidadao->id)
                    ->whereNull('dependente_id')
                    ->exists();

                if ($exists) {
                    $jaExistiam++;
                } else {
                    $programa->inscricoes()->create([
                        'cidadao_id'    => $cidadao->id,
                        'dependente_id' => null,
                        'regiao'        => $data['regiao'] ?? null,
                        'status'        => $statusInicial,
                    ]);
                    $criados++;
                }
            }

            // Dependentes
            foreach ($depIds as $depId) {
                $exists = $programa->inscricoes()
                    ->where('cidadao_id', $cidadao->id)
                    ->where('dependente_id', $depId)
                    ->exists();

                if ($exists) {
                    $jaExistiam++;
                } else {
                    $programa->inscricoes()->create([
                        'cidadao_id'    => $cidadao->id,
                        'dependente_id' => $depId,
                        'regiao'        => $data['regiao'] ?? null,
                        'status'        => $statusInicial,
                    ]);
                    $criados++;
                }
            }
        });

        // Mensagens
        $msg = [];
        if ($criados > 0)    { $msg[] = "{$criados} inscrição(ões) registrada(s)."; }
        if ($jaExistiam > 0) { $msg[] = "{$jaExistiam} já existia(m) e foram ignorada(s)."; }
        if ($statusInicial === 'reprovado') {
            $msg[] = "Seu pedido foi registrado com status reprovado conforme as regras do programa.";
        }

        return redirect()
            ->route('cidadao.programas.ver', $programa)
            ->with('mensagem', implode(' ', $msg) ?: 'Nada a fazer.');
    }

    /**
     * Normaliza lista de regiões do programa (json/array/csv/string).
     */
    private function normalizarRegioes($raw): array
    {
        if (is_string($raw)) {
            $json = json_decode($raw, true);
            $arr  = (json_last_error() === JSON_ERROR_NONE && is_array($json))
                ? $json
                : preg_split('/,|;|\|/', $raw);
        } elseif (is_array($raw)) {
            $arr = $raw;
        } else {
            $arr = [];
        }

        return array_values(array_filter(array_map(
            fn($v) => trim((string) $v),
            $arr
        )));
    }
}
