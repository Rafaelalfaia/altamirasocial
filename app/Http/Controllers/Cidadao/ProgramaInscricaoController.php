<?php

namespace App\Http\Controllers\Cidadao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Programa;
use App\Models\ProgramaInscricao;
use App\Models\Dependente;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;



class ProgramaInscricaoController extends Controller
{
    public function inscrever(Request $request, Programa $programa)
    {
        // (A) Sanity-check de log p/ garantir que caiu AQUI
        \Log::info('INSCRICAO: ProgramaInscricaoController@inscrever',
            ['programa_id' => $programa->id, 'user_id' => optional(auth()->user())->id]);

        $cidadao = auth()->user()->cidadao ?? null;
        abort_if(!$cidadao, 403, 'Perfil de cidadão não encontrado.');

        // (B) Normaliza regiões do programa (string/JSON/array) e torna obrigatória
        $raw = $programa->regioes ?? [];
        if (is_string($raw)) {
            $j = json_decode($raw, true);
            $regs = (json_last_error() === JSON_ERROR_NONE && is_array($j))
                ? $j
                : preg_split('/,|;|\|/', $raw);
        } elseif (is_array($raw)) {
            $regs = $raw;
        } else {
            $regs = [];
        }
        $regioesValidas = collect($regs)->filter()
            ->map(fn($r) => trim((string)$r))
            ->unique()->values()->all();

        // (C) VALIDAÇÃO — região OBRIGATÓRIA; NADA de dependente_id (singular)
        $dados = $request->validate([
            'regiao'            => ['required','string', Rule::in($regioesValidas)],
            'inscrever_titular' => 'nullable|boolean',
            'dependentes'       => 'nullable|array',
            'dependentes.*'     => 'integer|exists:dependentes,id',
        ], [
            'regiao.required'   => 'Selecione sua região.',
            'regiao.in'         => 'Região inválida para este programa.',
        ]);

        $querTitular = (bool) ($dados['inscrever_titular'] ?? false);

        // (D) Só considera dependentes se o programa aceitar menores
        $depIds = $programa->aceita_menores
            ? collect($dados['dependentes'] ?? [])->unique()->values()
            : collect();

        // (E) Garante que são filhos do cidadão logado
        if ($depIds->isNotEmpty()) {
            $validos = Dependente::where('cidadao_id', $cidadao->id)
                ->whereIn('id', $depIds)->pluck('id');
            $depIds = $depIds->intersect($validos)->values();
        }

        // (F) Obriga titular OU ≥1 dependente
        if (!$querTitular && $depIds->isEmpty()) {
            return back()->withErrors([
                'base' => 'Selecione pelo menos um dependente ou marque "Inscrever meu nome (titular)".'
            ])->withInput();
        }

        $regiao = $dados['regiao'];
        $criados = 0; $repetidos = 0;

        DB::transaction(function () use ($programa, $cidadao, $querTitular, $depIds, $regiao, &$criados, &$repetidos) {

            // (G) Titular (dependente_id = null)
            if ($querTitular) {
                $ja = ProgramaInscricao::where('programa_id', $programa->id)
                    ->where('cidadao_id', $cidadao->id)
                    ->whereNull('dependente_id')
                    ->exists();

                if ($ja) { $repetidos++; }
                else {
                    ProgramaInscricao::create([
                        'programa_id'   => $programa->id,
                        'cidadao_id'    => $cidadao->id,
                        'dependente_id' => null,
                        'status'        => 'pendente',
                        'regiao'        => $regiao,
                    ]);
                    $criados++;
                }
            }

            // (H) Dependentes (múltiplos)
            foreach ($depIds as $depId) {
                $ja = ProgramaInscricao::where('programa_id', $programa->id)
                    ->where('cidadao_id', $cidadao->id)
                    ->where('dependente_id', $depId)
                    ->exists();

                if ($ja) { $repetidos++; continue; }

                ProgramaInscricao::create([
                    'programa_id'   => $programa->id,
                    'cidadao_id'    => $cidadao->id,
                    'dependente_id' => $depId,
                    'status'        => 'pendente',
                    'regiao'        => $regiao,
                ]);
                $criados++;
            }
        });

        $msg = "{$criados} inscrição(ões) criada(s)";
        if ($repetidos > 0) $msg .= " · {$repetidos} já existia(m)";

        return back()->with('success', $msg);
    }


    public function store(Request $request, Programa $programa)
    {
        $cidadao = auth()->user()->cidadao ?? null;
        abort_if(!$cidadao, 403, 'Perfil de cidadão não encontrado.');

        $dados = $request->validate([
            'inscrever_titular' => 'nullable|boolean',
            'dependentes'       => 'nullable|array',
            'dependentes.*'     => 'integer|exists:dependentes,id',
        ]);

        // Se programa não aceita menores, ignoramos qualquer dependente
        $idsSelecionados = $programa->aceita_menores
            ? collect($dados['dependentes'] ?? [])
            : collect();

        // Garante que os dependentes são do cidadão logado
        if ($idsSelecionados->isNotEmpty()) {
            $idsSelecionados = $idsSelecionados->unique()->values();
            $idsValidos = Dependente::where('cidadao_id', $cidadao->id)
                ->whereIn('id', $idsSelecionados)
                ->pluck('id');
            $idsSelecionados = $idsSelecionados->intersect($idsValidos);
        }

        $criados = 0;
        $repetidos = 0;

        DB::transaction(function () use ($programa, $cidadao, $dados, $idsSelecionados, &$criados, &$repetidos) {

            // (opcional) Inscrição do TITULAR (sem dependente)
            if (!empty($dados['inscrever_titular'])) {
                $jaExiste = ProgramaInscricao::where('programa_id', $programa->id)
                    ->where('cidadao_id', $cidadao->id)
                    ->whereNull('dependente_id')
                    ->exists();

                if ($jaExiste) {
                    $repetidos++;
                } else {
                    ProgramaInscricao::create([
                        'programa_id'   => $programa->id,
                        'cidadao_id'    => $cidadao->id,
                        'dependente_id' => null,
                        'status'        => 'pendente',
                        // 'regiao'      => $request->input('regiao') ?? null, // use se precisar
                    ]);
                    $criados++;
                }
            }

            // Inscrições para CADA dependente selecionado
            foreach ($idsSelecionados as $depId) {
                $jaExiste = ProgramaInscricao::where('programa_id', $programa->id)
                    ->where('dependente_id', $depId)
                    ->exists();

                if ($jaExiste) { $repetidos++; continue; }

                ProgramaInscricao::create([
                    'programa_id'   => $programa->id,
                    'cidadao_id'    => $cidadao->id,
                    'dependente_id' => $depId,
                    'status'        => 'pendente',
                ]);
                $criados++;
            }
        });

        $msg = "{$criados} inscrição(ões) criada(s)";
        if ($repetidos > 0) $msg .= " · {$repetidos} já existia(m)";

        return back()->with('success', $msg);
    }

}
