<?php

namespace App\Http\Controllers\Cidadao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Cidadao;
use App\Models\Bairro;
use Illuminate\Support\Carbon;

class PerfilController extends Controller
{
    /* =========================
     * HELPERS GERAIS
     * =======================*/

    /** Busca o Cidadão do usuário logado sem criar automaticamente. */
    private function getCidadao(bool $withRelacionamentos = false): ?Cidadao
    {
        $q = Cidadao::query()->where('user_id', Auth::id());
        if ($withRelacionamentos) {
            $q->with('bairro.cidade.estado');
        }
        return $q->first();
    }

    /** Data flexível: aceita Y-m-d, d/m/Y ou qualquer formato parseável; retorna 'Y-m-d' ou null. */
    private function parseDateFlexible(?string $value): ?string
    {
        if ($value === null) return null;
        $v = trim($value);
        if ($v === '') return null;

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) {
            return $v; // já está Y-m-d
        }
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $v)) {
            [$d,$m,$y] = explode('/', $v);
            return sprintf('%04d-%02d-%02d', (int)$y, (int)$m, (int)$d);
        }
        try {
            return Carbon::parse($v)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Normaliza um valor já existente de data para 'Y-m-d' (Carbon|string|null). */
    private function normalizeExistingDate($value): ?string
    {
        if (!$value) return null;
        if ($value instanceof \Carbon\Carbon || $value instanceof \Illuminate\Support\Carbon) {
            return $value->format('Y-m-d');
        }
        try {
            return Carbon::parse((string)$value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Mantém o valor atual se o campo não veio no request; se vier vazio, salva null. */
    private function keep(array $validated, string $key, $current)
    {
        if (array_key_exists($key, $validated)) {
            $val = $validated[$key];
            return ($val === '' ? null : $val);
        }
        return $current;
    }

    /** Igual ao keep(), mas para datas (usa parseDateFlexible/normalizeExistingDate). */
    private function keepDate(array $validated, string $key, $current): ?string
    {
        if (array_key_exists($key, $validated)) {
            return $this->parseDateFlexible($validated[$key]);
        }
        return $this->normalizeExistingDate($current);
    }

    /**
     * Mantém boolean atual quando o input não veio no request.
     * Se veio, usa $request->boolean($key). Retorna 0/1.
     */
    private function keepBool(Request $request, string $key, $current): int
    {
        return $request->has($key) ? (int)$request->boolean($key) : (int)($current ? 1 : 0);
    }

    /* =========================
     * DADOS PESSOAIS
     * =======================*/
    public function dadosPessoais()
    {
        $cidadao = $this->getCidadao();
        return view('cidadao.perfil.dados', compact('cidadao'));
    }

    public function cartaoPublico($id)
    {
        $cidadao = Cidadao::with('bairro.cidade.estado')->findOrFail($id);
        return view('cidadao.cartao-publico', compact('cidadao'));
    }

    public function salvarDadosPessoais(Request $request)
    {
        $cidadao = $this->getCidadao();

        $cpfAtualDigitos = preg_replace('/\D+/', '', (string) optional($cidadao)->cpf);
        $cpfNovoDigitos  = preg_replace('/\D+/', '', (string) $request->input('cpf'));
        $cpfAlterou      = $cpfNovoDigitos && $cpfNovoDigitos !== $cpfAtualDigitos;

        $rules = [
            'nome'            => ['required','string','max:255'],
            // datas serão parseadas manualmente e preservadas se não vierem
            'data_nascimento' => ['nullable'],
            'telefone'        => ['nullable','string','max:20'],
            'email'           => ['nullable','email','max:191'],
            'cor_raca'        => ['nullable','string','max:191'],
            'nis'             => ['nullable','string','max:20'],
            'rg'              => ['nullable','string','max:30'],
            'orgao_emissor'   => ['nullable','string','max:30'],
            'data_emissao_rg' => ['nullable'],
            'titulo_eleitor'  => ['nullable','string','max:20'],
            'zona'            => ['nullable','string','max:10'],
            'secao'           => ['nullable','string','max:10'],
            'codigo_cadunico' => ['nullable','string','max:50'],
            'foto'            => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
            // novos (2025)
            'apelido'               => ['nullable','string','max:80'],
            'naturalidade'          => ['nullable','string','max:120'],
            'unidade_cadastradora'  => ['nullable','string','max:191'],
            'responsavel_familiar'  => ['nullable','string','max:191'],
            'situacao_civil'        => ['nullable','string','max:30'],
            'whatsapp'              => ['nullable','string','max:20'],
        ];

        $rules['sexo'] = [ $cidadao ? 'nullable' : 'required', Rule::in(['Masculino','Feminino','Outro']) ];

        if (!$cidadao || $cpfAlterou) {
            $rules['cpf'] = [
                'required','string',
                function ($attribute, $value, $fail) {
                    if (!self::validarCpf($value)) $fail('O CPF informado é inválido.');
                },
            ];
        } else {
            $rules['cpf'] = ['nullable','string'];
        }

        $validated = $request->validate($rules);

        if (!$cidadao) {
            $cidadao = new Cidadao();
            $cidadao->user_id = Auth::id();
        }

        // Upload de foto (substitui a antiga se houver)
        if ($request->hasFile('foto')) {
            if ($cidadao->foto && Storage::disk('public')->exists('fotos/'.$cidadao->foto)) {
                Storage::disk('public')->delete('fotos/'.$cidadao->foto);
            }
            $path = $request->file('foto')->store('fotos','public');
            $cidadao->foto = basename($path);
        }

        // Datas preservadas quando não vierem no request:
        $dataNascimento = $this->keepDate($validated, 'data_nascimento', $cidadao->data_nascimento);
        $dataEmissaoRg  = $this->keepDate($validated, 'data_emissao_rg',  $cidadao->data_emissao_rg);

        // Persistência sem apagar campos antigos quando o input não veio
        $cidadao->forceFill([
            'nome'                 => $validated['nome'],
            'cpf'                  => array_key_exists('cpf', $validated) ? $validated['cpf'] : $cidadao->cpf,
            'data_nascimento'      => $dataNascimento,
            'sexo'                 => array_key_exists('sexo', $validated) ? ($validated['sexo'] ?? null) : $cidadao->sexo,
            'telefone'             => $this->keep($validated, 'telefone', $cidadao->telefone),
            'email'                => $this->keep($validated, 'email', $cidadao->email),
            'cor_raca'             => $this->keep($validated, 'cor_raca', $cidadao->cor_raca),
            'nis'                  => $this->keep($validated, 'nis', $cidadao->nis),
            'rg'                   => $this->keep($validated, 'rg', $cidadao->rg),
            'orgao_emissor'        => $this->keep($validated, 'orgao_emissor', $cidadao->orgao_emissor),
            'data_emissao_rg'      => $dataEmissaoRg,
            'titulo_eleitor'       => $this->keep($validated, 'titulo_eleitor', $cidadao->titulo_eleitor),
            'zona'                 => $this->keep($validated, 'zona', $cidadao->zona),
            'secao'                => $this->keep($validated, 'secao', $cidadao->secao),
            'codigo_cadunico'      => $this->keep($validated, 'codigo_cadunico', $cidadao->codigo_cadunico),
            // novos (2025)
            'apelido'              => $this->keep($validated, 'apelido', $cidadao->apelido),
            'naturalidade'         => $this->keep($validated, 'naturalidade', $cidadao->naturalidade),
            'unidade_cadastradora' => $this->keep($validated, 'unidade_cadastradora', $cidadao->unidade_cadastradora),
            'responsavel_familiar' => $this->keep($validated, 'responsavel_familiar', $cidadao->responsavel_familiar),
            'situacao_civil'       => $this->keep($validated, 'situacao_civil', $cidadao->situacao_civil),
            'whatsapp'             => $this->keep($validated, 'whatsapp', $cidadao->whatsapp),
        ])->save();

        return redirect()->route('cidadao.perfil.moradia')
            ->with('success','Dados pessoais salvos com sucesso!');
    }

    /* =========================
     * MORADIA
     * =======================*/
    public function moradia()
    {
        $cidadao = $this->getCidadao(true);
        if (!$cidadao) {
            return redirect()->route('cidadao.perfil.dados')
                ->with('warning', 'Preencha seus dados pessoais antes de prosseguir.');
        }

        $bairros = Bairro::with('cidade.estado')->orderBy('nome')->get();
        return view('cidadao.perfil.moradia', compact('cidadao', 'bairros'));
    }

    public function salvarMoradia(Request $request)
    {
        $cidadao = $this->getCidadao();
        if (!$cidadao) {
            return redirect()->route('cidadao.perfil.dados')
                ->with('warning','Preencha seus dados pessoais antes de prosseguir.');
        }

        $validated = $request->validate([
            'bairro_id'           => ['required','exists:bairros,id'],
            'cep'                 => ['nullable','string','max:20'],
            'rua'                 => ['nullable','string','max:191'],
            'numero'              => ['nullable','string','max:50'],
            'complemento'         => ['nullable','string','max:191'],

            // Situação de moradia — manter compatibilidade textual
            'tipo_moradia'        => ['nullable','string','max:191'],

            // Novos (2025)
            'pontos_referencia'   => ['nullable','string','max:191'],
            'tempo_reside'        => ['nullable','string','max:30'],
            'qtde_comodos'        => ['nullable','integer','min:0','max:50'],
            'tipo_construcao'     => ['nullable','string','max:30'],
            'tipo_via'            => ['nullable','string','max:30'],
            'abastecimento_agua'  => ['nullable','string','max:30'],
            'energia_tipo'        => ['nullable','string','max:30'],

            // utilidades (checkboxes)
            'tem_esgoto'          => ['nullable'],
            'tem_agua_encanada'   => ['nullable'],
            'tem_coleta_lixo'     => ['nullable'],
            'tem_energia'         => ['nullable'],
            'possui_animais'      => ['nullable'],
        ], [
            'bairro_id.required' => 'Selecione um bairro válido da lista.',
            'bairro_id.exists'   => 'Selecione um bairro válido da lista.',
        ]);

        $cidadao->forceFill([
            'cep'                => $this->keep($validated, 'cep', $cidadao->cep),
            'rua'                => $this->keep($validated, 'rua', $cidadao->rua),
            'numero'             => $this->keep($validated, 'numero', $cidadao->numero),
            'complemento'        => $this->keep($validated, 'complemento', $cidadao->complemento),
            'bairro_id'          => $validated['bairro_id'],
            'tipo_moradia'       => array_key_exists('tipo_moradia',$validated) ? ($validated['tipo_moradia'] ?: null) : $cidadao->tipo_moradia,

            'pontos_referencia'  => $this->keep($validated, 'pontos_referencia', $cidadao->pontos_referencia),
            'tempo_reside'       => $this->keep($validated, 'tempo_reside', $cidadao->tempo_reside),
            'qtde_comodos'       => $this->keep($validated, 'qtde_comodos', $cidadao->qtde_comodos),
            'tipo_construcao'    => $this->keep($validated, 'tipo_construcao', $cidadao->tipo_construcao),
            'tipo_via'           => $this->keep($validated, 'tipo_via', $cidadao->tipo_via),
            'abastecimento_agua' => $this->keep($validated, 'abastecimento_agua', $cidadao->abastecimento_agua),
            'energia_tipo'       => $this->keep($validated, 'energia_tipo', $cidadao->energia_tipo),

            // booleans preservando quando o input não veio
            'tem_esgoto'         => $this->keepBool($request, 'tem_esgoto', $cidadao->tem_esgoto),
            'tem_agua_encanada'  => $this->keepBool($request, 'tem_agua_encanada', $cidadao->tem_agua_encanada),
            'tem_coleta_lixo'    => $this->keepBool($request, 'tem_coleta_lixo', $cidadao->tem_coleta_lixo),
            'tem_energia'        => $this->keepBool($request, 'tem_energia', $cidadao->tem_energia),
            'possui_animais'     => $this->keepBool($request, 'possui_animais', $cidadao->possui_animais),
        ])->save();

        return redirect()->route('cidadao.perfil.trabalho')
            ->with('success','Dados de moradia atualizados com sucesso.');
    }

    /* =========================
     * TRABALHO E RENDA
     * =======================*/
    public function trabalho()
    {
        $cidadao = $this->getCidadao();
        if (!$cidadao) {
            return redirect()->route('cidadao.perfil.dados')
                ->with('warning', 'Preencha seus dados pessoais antes de prosseguir.');
        }
        return view('cidadao.perfil.trabalho', compact('cidadao'));
    }

    public function salvarTrabalho(Request $request)
    {
        $cidadao = $this->getCidadao();
        if (!$cidadao) {
            return redirect()->route('cidadao.perfil.dados')
                ->with('warning','Preencha seus dados pessoais antes de prosseguir.');
        }

        $validated = $request->validate([
            'situacao_profissional'  => ['nullable','string','max:40'],
            'ocupacao'               => ['nullable','string','max:191'],
            'renda'                  => ['nullable','numeric','min:0'],
            'renda_total_familiar'   => ['nullable','numeric','min:0'],
            'pessoas_na_residencia'  => ['nullable','integer','min:1','max:50'],
            'grau_parentesco'        => ['nullable','string','max:191'],
            'escolaridade'           => ['nullable','string','max:191'],
        ]);

        $cidadao->forceFill([
            'situacao_profissional' => $this->keep($validated, 'situacao_profissional', $cidadao->situacao_profissional),
            'ocupacao'              => $this->keep($validated, 'ocupacao', $cidadao->ocupacao),
            'renda'                 => $this->keep($validated, 'renda', $cidadao->renda),
            'renda_total_familiar'  => $this->keep($validated, 'renda_total_familiar', $cidadao->renda_total_familiar),
            'pessoas_na_residencia' => $this->keep($validated, 'pessoas_na_residencia', $cidadao->pessoas_na_residencia),
            'grau_parentesco'       => $this->keep($validated, 'grau_parentesco', $cidadao->grau_parentesco),
            'escolaridade'          => $this->keep($validated, 'escolaridade', $cidadao->escolaridade),
        ])->save();

        return redirect()->route('cidadao.perfil.acessibilidade')
            ->with('success','Dados de trabalho e renda salvos com sucesso.');
    }

    /* =========================
     * ACESSIBILIDADE
     * =======================*/
    public function acessibilidade()
    {
        $cidadao = $this->getCidadao();
        if (!$cidadao) {
            return redirect()->route('cidadao.perfil.dados')
                ->with('warning', 'Preencha seus dados pessoais antes de prosseguir.');
        }
        return view('cidadao.perfil.acessibilidade', compact('cidadao'));
    }

    public function salvarAcessibilidade(Request $request)
    {
        $cidadao = $this->getCidadao();
        if (!$cidadao) {
            return redirect()->route('cidadao.perfil.dados')
                ->with('warning','Preencha seus dados pessoais antes de prosseguir.');
        }

        $validated = $request->validate([
            // checkboxes/flags (preservaremos quando não vierem)
            'pcd'                   => ['nullable'],
            'possui_deficiencia'    => ['nullable'],

            // legado (csv) + atual (array)
            'tipo_deficiencia'      => ['nullable','string','max:191'],
            'tipos_deficiencia'     => ['nullable','array'],
            'tipos_deficiencia.*'   => ['string','max:40'],

            'ha_gravida'            => ['nullable'],
            'nome_gravida'          => ['nullable','string','max:191'],
            'ha_idoso'              => ['nullable'],
            'nome_idoso'            => ['nullable','string','max:191'],

            'observacoes'              => ['nullable','string'],
            'observacoes_entrevistador'=> ['nullable','string'],

            // data — preservada se não vier
            'data_declaracao'          => ['nullable'],
            'nome_declarente'          => ['nullable','string','max:191'],
            'nome_entrevistador'       => ['nullable','string','max:191'],
        ]);

        // TIPOS: se o grupo veio no request (mesmo vazio), usamos; senão, preservamos do banco
        $tiposProvided = $request->has('tipos_deficiencia'); // true quando existe o input (até hidden)
        $tipos = $tiposProvided
            ? $this->sanitizeTiposDeficiencia($request->input('tipos_deficiencia'))
            : $this->sanitizeTiposDeficiencia($cidadao->tipos_deficiencia ?? null);

        // Possui deficiência: se o checkbox veio, usa; senão, preserva do banco
        $possuiProvided = $request->has('possui_deficiencia');
        $possuiBase     = $possuiProvided ? $request->boolean('possui_deficiencia') : (bool)$cidadao->possui_deficiencia;

        // Regra final: true se marcou o checkbox OU se há ao menos um tipo
        $possui = ($possuiBase || count($tipos) > 0) ? 1 : 0;
        $pcd    = $possui; // espelhado

        // Booleans condicionais preservando quando input não veio
        $haGravida = $this->keepBool($request, 'ha_gravida', $cidadao->ha_gravida);
        $haIdoso   = $this->keepBool($request, 'ha_idoso',   $cidadao->ha_idoso);

        // Data (preserva quando não vem)
        $dataDeclaracao = $this->keepDate($validated, 'data_declaracao', $cidadao->data_declaracao);

        // CSV legado (sempre derivado do array atual)
        $tipoDefCsv = !empty($tipos) ? implode(',', $tipos) : null;

        $cidadao->forceFill([
            'pcd'                      => $pcd,
            'possui_deficiencia'       => $possui,

            // Se o grupo veio no request, aplica o novo valor (mesmo vazio); senão, mantém
            'tipos_deficiencia'        => $tiposProvided ? $tipos : ($cidadao->tipos_deficiencia ?? []),
            'tipo_deficiencia'         => $tiposProvided ? $tipoDefCsv : ($cidadao->tipo_deficiencia ?? null),

            'ha_gravida'               => $haGravida,
            'nome_gravida'             => $this->keep($validated, 'nome_gravida', $cidadao->nome_gravida),

            'ha_idoso'                 => $haIdoso,
            'nome_idoso'               => $this->keep($validated, 'nome_idoso', $cidadao->nome_idoso),

            'observacoes'              => $this->keep($validated, 'observacoes', $cidadao->observacoes),
            'observacoes_entrevistador'=> $this->keep($validated, 'observacoes_entrevistador', $cidadao->observacoes_entrevistador),

            'data_declaracao'          => $dataDeclaracao,
            'nome_declarente'          => $this->keep($validated, 'nome_declarente', $cidadao->nome_declarente),
            'nome_entrevistador'       => $this->keep($validated, 'nome_entrevistador', $cidadao->nome_entrevistador),
        ])->save();


        return redirect()->route('cidadao.dashboard')
            ->with('success','Cadastro completo!');
    }

    /** Converte valor (array/JSON/CSV/string) em array de strings, limpo e sem duplicatas. */
    private function sanitizeTiposDeficiencia($value): array
    {
        $toArray = function (array $arr): array {
            $arr = array_map(fn($v) => trim((string)$v), $arr);
            $arr = array_filter($arr, fn($v) => $v !== '');
            $arr = array_values(array_unique($arr));
            return $arr;
        };

        // Já é array (ex.: checkboxes)
        if (is_array($value)) {
            return $toArray($value);
        }

        // String: pode ser "" (vazio), JSON ou CSV
        if (is_string($value)) {
            $s = trim($value);
            if ($s === '') {
                return [];
            }
            // tenta JSON primeiro (ex.: '["Física","Visual"]')
            $json = json_decode($s, true);
            if (is_array($json)) {
                return $toArray($json);
            }
            // fallback: trata como CSV (ex.: 'Física,Visual')
            return $toArray(explode(',', $s));
        }

        // Qualquer outra coisa: vazio
        return [];
    }




    /* =========================
     * CPF helper
     * =======================*/
    private static function validarCpf($cpf): bool
    {
        $cpf = preg_replace('/\D+/', '', (string) $cpf);
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) return false;

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += (int)$cpf[$i] * (($t + 1) - $i);
            }
            $digito = ($soma * 10) % 11;
            $digito = ($digito === 10) ? 0 : $digito;
            if ((int)$cpf[$t] !== $digito) return false;
        }
        return true;
    }
}
