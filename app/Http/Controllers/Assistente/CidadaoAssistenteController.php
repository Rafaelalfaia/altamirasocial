<?php

namespace App\Http\Controllers\Assistente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Cidadao;
use App\Models\Bairro;
use App\Models\User;
use Illuminate\Support\Carbon;

class CidadaoAssistenteController extends Controller
{
    /* ============================================================
     * Helpers (iguais ao PerfilController para padronizar tudo)
     * ============================================================*/

    // Aceita 'Y-m-d', 'd/m/Y' ou qualquer formato parseável → 'Y-m-d' | null
    private function parseDateFlexible(?string $value): ?string
    {
        if ($value === null) return null;
        $v = trim($value);
        if ($v === '') return null;

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) return $v;
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $v)) {
            [$d,$m,$y] = explode('/', $v);
            return sprintf('%04d-%02d-%02d', (int)$y, (int)$m, (int)$d);
        }
        try { return Carbon::parse($v)->format('Y-m-d'); }
        catch (\Throwable $e) { return null; }
    }

    private function normalizeExistingDate($value): ?string
    {
        if (!$value) return null;
        if ($value instanceof \Carbon\Carbon || $value instanceof \Illuminate\Support\Carbon) {
            return $value->format('Y-m-d');
        }
        try { return Carbon::parse((string)$value)->format('Y-m-d'); }
        catch (\Throwable $e) { return null; }
    }

    // Mantém valor antigo quando o campo não veio no request
    private function keep(array $validated, string $key, $current)
    {
        if (array_key_exists($key, $validated)) {
            $val = $validated[$key];
            return ($val === '' ? null : $val);
        }
        return $current;
    }

    // Igual ao keep(), mas para datas
    private function keepDate(array $validated, string $key, $current): ?string
    {
        if (array_key_exists($key, $validated)) {
            return $this->parseDateFlexible($validated[$key]);
        }
        return $this->normalizeExistingDate($current);
    }

    // Preserva boolean quando não veio; se vier, usa boolean()
    private function keepBool(Request $request, string $key, $current): int
    {
        return $request->has($key) ? (int)$request->boolean($key) : (int)($current ? 1 : 0);
    }

    // Converte array/JSON/CSV → array único/limpo
    private function sanitizeTiposDeficiencia($value): array
    {
        $toArray = function (array $arr): array {
            $arr = array_map(fn($v) => trim((string)$v), $arr);
            $arr = array_filter($arr, fn($v) => $v !== '');
            return array_values(array_unique($arr));
        };
        if (is_array($value)) return $toArray($value);
        if (is_string($value)) {
            $s = trim($value);
            if ($s === '') return [];
            $json = json_decode($s, true);
            if (is_array($json)) return $toArray($json);
            return $toArray(explode(',', $s)); // CSV
        }
        return [];
    }

    // Normalizador para moeda "1.234,56" / "1234.56" / numeric
    private function parseMoneyInput($value): ?float
    {
        if ($value === null || $value === '') return null;
        if (is_numeric($value)) return (float)$value;
        $s = preg_replace('/[^\d\.,]/', '', (string)$value);
        if (strpos($s, ',') !== false && strpos($s, '.') !== false) {
            // assume milhar '.' e decimal ','
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        } elseif (strpos($s, ',') !== false) {
            // assume decimal ','
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        }
        return is_numeric($s) ? (float)$s : null;
    }

    // Validação de CPF (mesmo algoritmo)
    private static function validarCpf($cpf): bool
    {
        $cpf = preg_replace('/\D+/', '', (string) $cpf);
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) return false;

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) $soma += (int)$cpf[$i] * (($t + 1) - $i);
            $digito = ($soma * 10) % 11;
            $digito = ($digito === 10) ? 0 : $digito;
            if ((int)$cpf[$t] !== $digito) return false;
        }
        return true;
    }

    /* ============================================================
     * Fluxo de criação básica (assistente cria um novo cidadão)
     * ============================================================*/
    public function criar()
    {
        return view('assistente.cidadao.criar');
    }

    public function salvar(Request $request)
    {
        $request->merge([
            'cpf' => preg_replace('/\D+/', '', (string)$request->input('cpf')),
        ]);

        $request->validate([
            'nome'     => ['required','string','max:255'],
            'cpf'      => ['required','string','size:11', function ($att, $val, $fail) {
                if (!self::validarCpf($val)) $fail('CPF inválido.');
            }, Rule::unique('users','cpf')],
            'email'    => ['nullable','email','max:191', Rule::unique('users','email')],
            'password' => ['required','confirmed','min:8'],
        ]);

        $user = new User();
        $user->name = $request->nome;
        $user->cpf  = $request->cpf; // só dígitos
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('Cidadao');
        }

        $cidadao = new Cidadao();
        $cidadao->user_id = $user->id;
        $cidadao->nome    = $request->nome;
        $cidadao->cpf     = $request->cpf;
        $cidadao->status  = $cidadao->status ?? 'pendente';
        $cidadao->save();

        return redirect()->route('assistente.cidadao.dados.editar', $cidadao->id)
            ->with('success', 'Cidadão criado com sucesso. Preencha o cadastro.');
    }

    /* ============================================================
     * DADOS PESSOAIS
     * ============================================================*/
    public function editarDados($id)
    {
        $cidadao = Cidadao::with('bairro.cidade.estado')->findOrFail($id);
        return view('assistente.cidadao.dados', compact('cidadao'));
    }

    public function salvarDados(Request $request, $id)
    {
        $cidadao = Cidadao::with('user')->findOrFail($id);

        $cpfAtualDigitos = preg_replace('/\D+/', '', (string) $cidadao->cpf);
        $cpfNovoDigitado = (string) $request->input('cpf');
        $cpfNovoDigitos  = preg_replace('/\D+/', '', $cpfNovoDigitado);
        $cpfAlterou      = $cpfNovoDigitos && $cpfNovoDigitos !== $cpfAtualDigitos;

        $rules = [
            'nome'                  => ['required','string','max:255'],
            'data_nascimento'       => ['nullable'],
            'telefone'              => ['nullable','string','max:20'],
            'whatsapp'              => ['nullable','string','max:20'],
            'email'                 => ['nullable','email','max:191'],
            'cor_raca'              => ['nullable','string','max:191'],
            'nis'                   => ['nullable','string','max:20'],
            'rg'                    => ['nullable','string','max:30'],
            'orgao_emissor'         => ['nullable','string','max:30'],
            'data_emissao_rg'       => ['nullable'],
            'titulo_eleitor'        => ['nullable','string','max:20'],
            'zona'                  => ['nullable','string','max:10'],
            'secao'                 => ['nullable','string','max:10'],
            'codigo_cadunico'       => ['nullable','string','max:50'],
            'foto'                  => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],

            // extras alinhados ao PerfilController
            'apelido'               => ['nullable','string','max:80'],
            'naturalidade'          => ['nullable','string','max:120'],
            'unidade_cadastradora'  => ['nullable','string','max:191'],
            'responsavel_familiar'  => ['nullable','string','max:191'],
            'situacao_civil'        => ['nullable','string','max:50'],
            'sexo'                  => ['nullable','string','max:50'],
        ];

        if ($cpfAlterou) {
            $rules['cpf'] = [
                'required','string','size:11',
                function ($attribute, $value, $fail) {
                    if (!self::validarCpf($value)) $fail('O CPF informado é inválido.');
                },
                Rule::unique('users','cpf')->ignore(optional($cidadao->user)->id),
            ];
            // normaliza no request para manter consistência downstream
            $request->merge(['cpf' => $cpfNovoDigitos]);
        } else {
            $rules['cpf'] = ['nullable','string'];
        }

        $validated = $request->validate($rules);

        // Foto única (substitui arquivo antigo)
        if ($request->hasFile('foto')) {
            if ($cidadao->foto && Storage::disk('public')->exists('fotos/'.$cidadao->foto)) {
                Storage::disk('public')->delete('fotos/'.$cidadao->foto);
            }
            $path = $request->file('foto')->store('fotos', 'public');
            $cidadao->foto = basename($path);
        }

        // Datas
        $dataNascimento = $this->keepDate($validated, 'data_nascimento', $cidadao->data_nascimento);
        $dataEmissaoRg  = $this->keepDate($validated, 'data_emissao_rg',  $cidadao->data_emissao_rg);

        // Persistência (mantendo valores quando campo não veio)
        $cidadao->forceFill([
            'nome'                  => $validated['nome'],
            'cpf'                   => array_key_exists('cpf', $validated) ? $validated['cpf'] : $cidadao->cpf,
            'data_nascimento'       => $dataNascimento,
            'sexo'                  => $this->keep($validated,'sexo',$cidadao->sexo),

            'telefone'              => $this->keep($validated,'telefone',$cidadao->telefone),
            'whatsapp'              => $this->keep($validated,'whatsapp',$cidadao->whatsapp),
            'email'                 => $this->keep($validated,'email',$cidadao->email),
            'cor_raca'              => $this->keep($validated,'cor_raca',$cidadao->cor_raca),

            'nis'                   => $this->keep($validated,'nis',$cidadao->nis),
            'rg'                    => $this->keep($validated,'rg',$cidadao->rg),
            'orgao_emissor'         => $this->keep($validated,'orgao_emissor',$cidadao->orgao_emissor),
            'data_emissao_rg'       => $dataEmissaoRg,

            'titulo_eleitor'        => $this->keep($validated,'titulo_eleitor',$cidadao->titulo_eleitor),
            'zona'                  => $this->keep($validated,'zona',$cidadao->zona),
            'secao'                 => $this->keep($validated,'secao',$cidadao->secao),
            'codigo_cadunico'       => $this->keep($validated,'codigo_cadunico',$cidadao->codigo_cadunico),

            'apelido'               => $this->keep($validated,'apelido',$cidadao->apelido),
            'naturalidade'          => $this->keep($validated,'naturalidade',$cidadao->naturalidade),
            'unidade_cadastradora'  => $this->keep($validated,'unidade_cadastradora',$cidadao->unidade_cadastradora),
            'responsavel_familiar'  => $this->keep($validated,'responsavel_familiar',$cidadao->responsavel_familiar),
            'situacao_civil'        => $this->keep($validated,'situacao_civil',$cidadao->situacao_civil),
        ])->save();

        // Sincroniza CPF no User se alterado
        if ($cpfAlterou && $cidadao->user) {
            $cidadao->user->cpf = $validated['cpf'];
            if ($validated['email'] ?? null) $cidadao->user->email = $validated['email'];
            if ($validated['nome'] ?? null)  $cidadao->user->name  = $validated['nome'];
            $cidadao->user->save();
        }

        return redirect()->route('assistente.cidadao.moradia.editar', $cidadao->id)
            ->with('success', 'Dados pessoais salvos com sucesso.');
    }

    // Alias legado (evita erro de método antigo)
    public function atualizarDados(Request $request, $id) { return $this->salvarDados($request, $id); }

    /* ============================================================
     * MORADIA
     * ============================================================*/
    public function editarMoradia($id)
    {
        $cidadao = Cidadao::with('bairro.cidade.estado')->findOrFail($id);
        $bairros = Bairro::with('cidade')->orderBy('nome')->get();
        return view('assistente.cidadao.moradia', compact('cidadao','bairros'));
    }

    public function salvarMoradia(Request $request, $id)
    {
        $cidadao = Cidadao::findOrFail($id);

        $validated = $request->validate([
            'bairro_id'             => ['required','exists:bairros,id'],
            'cep'                   => ['nullable','string','max:20'],
            'rua'                   => ['nullable','string','max:191'],
            'numero'                => ['nullable','string','max:30'],
            'complemento'           => ['nullable','string','max:191'],

            'tipo_moradia'          => ['nullable','string','max:191'],
            'pontos_referencia'     => ['nullable','string'],
            'tempo_reside'          => ['nullable','string','max:191'],
            'qtde_comodos'          => ['nullable','integer','min:0'],
            'tipo_construcao'       => ['nullable','string','max:191'],
            'tipo_via'              => ['nullable','string','max:191'],
            'abastecimento_agua'    => ['nullable','string','max:191'],
            'energia_tipo'          => ['nullable','string','max:191'],

            'tem_agua_encanada'     => ['nullable'],
            'tem_esgoto'            => ['nullable'],
            'tem_coleta_lixo'       => ['nullable'],
            'tem_energia'           => ['nullable'],

            'possui_animais'        => ['nullable'],
            'numero_animais'        => ['nullable','integer','min:0'],

            'pessoas_na_residencia' => ['nullable','integer','min:1','max:50'],
        ]);

        $cidadao->forceFill([
            'bairro_id'             => $validated['bairro_id'],
            'cep'                   => $this->keep($validated,'cep',$cidadao->cep),
            'rua'                   => $this->keep($validated,'rua',$cidadao->rua),
            'numero'                => $this->keep($validated,'numero',$cidadao->numero),
            'complemento'           => $this->keep($validated,'complemento',$cidadao->complemento),

            'tipo_moradia'          => $this->keep($validated,'tipo_moradia',$cidadao->tipo_moradia),
            'pontos_referencia'     => $this->keep($validated,'pontos_referencia',$cidadao->pontos_referencia),
            'tempo_reside'          => $this->keep($validated,'tempo_reside',$cidadao->tempo_reside),
            'qtde_comodos'          => $this->keep($validated,'qtde_comodos',$cidadao->qtde_comodos),
            'tipo_construcao'       => $this->keep($validated,'tipo_construcao',$cidadao->tipo_construcao),
            'tipo_via'              => $this->keep($validated,'tipo_via',$cidadao->tipo_via),
            'abastecimento_agua'    => $this->keep($validated,'abastecimento_agua',$cidadao->abastecimento_agua),
            'energia_tipo'          => $this->keep($validated,'energia_tipo',$cidadao->energia_tipo),

            'tem_agua_encanada'     => $this->keepBool($request,'tem_agua_encanada',$cidadao->tem_agua_encanada),
            'tem_esgoto'            => $this->keepBool($request,'tem_esgoto',$cidadao->tem_esgoto),
            'tem_coleta_lixo'       => $this->keepBool($request,'tem_coleta_lixo',$cidadao->tem_coleta_lixo),
            'tem_energia'           => $this->keepBool($request,'tem_energia',$cidadao->tem_energia),

            'possui_animais'        => $this->keepBool($request,'possui_animais',$cidadao->possui_animais),
            'numero_animais'        => $this->keep($validated,'numero_animais',$cidadao->numero_animais),

            'pessoas_na_residencia' => $this->keep($validated,'pessoas_na_residencia',$cidadao->pessoas_na_residencia),
        ])->save();

        return redirect()->route('assistente.cidadao.trabalho.editar', $cidadao->id)
            ->with('success','Dados de moradia salvos com sucesso.');
    }

    public function atualizarMoradia(Request $request, $id) { return $this->salvarMoradia($request, $id); }

    /* ============================================================
     * TRABALHO & RENDA
     * ============================================================*/
    public function editarTrabalho($id)
    {
        $cidadao = Cidadao::findOrFail($id);
        return view('assistente.cidadao.trabalho', compact('cidadao'));
    }

    public function salvarTrabalho(Request $request, $id)
    {
        $cidadao = Cidadao::findOrFail($id);

        // Normaliza moeda antes de validar, aceitando “1.234,56”
        $request->merge([
            'renda'                => $this->parseMoneyInput($request->input('renda')),
            'renda_total_familiar' => $this->parseMoneyInput($request->input('renda_total_familiar')),
        ]);

        $validated = $request->validate([
            'situacao_profissional'  => ['nullable','string','max:191'],
            'ocupacao'               => ['nullable','string','max:191'],
            'renda'                  => ['nullable','numeric','min:0'],
            'renda_total_familiar'   => ['nullable','numeric','min:0'],
            'pessoas_na_residencia'  => ['nullable','integer','min:1','max:50'],
            'grau_parentesco'        => ['nullable','string','max:191'],
            'escolaridade'           => ['nullable','string','max:191'],
        ]);

        $cidadao->forceFill([
            'situacao_profissional' => $this->keep($validated,'situacao_profissional',$cidadao->situacao_profissional),
            'ocupacao'              => $this->keep($validated,'ocupacao',$cidadao->ocupacao),
            'renda'                 => $this->keep($validated,'renda',$cidadao->renda),
            'renda_total_familiar'  => $this->keep($validated,'renda_total_familiar',$cidadao->renda_total_familiar),
            'pessoas_na_residencia' => $this->keep($validated,'pessoas_na_residencia',$cidadao->pessoas_na_residencia),
            'grau_parentesco'       => $this->keep($validated,'grau_parentesco',$cidadao->grau_parentesco),
            'escolaridade'          => $this->keep($validated,'escolaridade',$cidadao->escolaridade),
        ])->save();

        return redirect()->route('assistente.cidadao.acessibilidade.editar', $cidadao->id)
            ->with('success','Dados de trabalho e renda salvos com sucesso.');
    }

    public function atualizarTrabalho(Request $request, $id) { return $this->salvarTrabalho($request, $id); }

    /* ============================================================
     * ACESSIBILIDADE / OBSERVAÇÕES
     * ============================================================*/
    public function editarAcessibilidade($id)
    {
        $cidadao = Cidadao::findOrFail($id);
        return view('assistente.cidadao.acessibilidade', compact('cidadao'));
    }

    public function salvarAcessibilidade(Request $request, $id)
    {
        $cidadao = Cidadao::findOrFail($id);

        $validated = $request->validate([
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

            // declaração/CID
            'data_declaracao'       => ['nullable'],
            'nome_declarente'       => ['nullable','string','max:191'],
            'nome_entrevistador'    => ['nullable','string','max:191'],

            'cid'                   => ['nullable','string','max:40'],
            'observacoes'           => ['nullable','string'],
            'observacoes_entrevistador' => ['nullable','string'],
        ]);

        $tiposProvided = $request->has('tipos_deficiencia');
        $tipos = $this->sanitizeTiposDeficiencia($tiposProvided ? $request->input('tipos_deficiencia') : $cidadao->tipos_deficiencia);

        $possuiBase = $request->has('possui_deficiencia')
            ? (bool)$request->boolean('possui_deficiencia')
            : (bool)$cidadao->possui_deficiencia;

        // true se marcou checkbox OU se há ao menos um tipo
        $possui = ($possuiBase || count($tipos) > 0) ? 1 : 0;
        $pcd    = $possui;

        $haGravida = $this->keepBool($request, 'ha_gravida', $cidadao->ha_gravida);
        $haIdoso   = $this->keepBool($request, 'ha_idoso',   $cidadao->ha_idoso);

        $dataDeclaracao = $this->keepDate($validated, 'data_declaracao', $cidadao->data_declaracao);
        $tipoDefCsv = !empty($tipos) ? implode(',', $tipos) : null;

        $cidadao->forceFill([
            'pcd'                        => $pcd,
            'possui_deficiencia'         => $possui,

            'tipos_deficiencia'          => $tiposProvided ? $tipos : ($cidadao->tipos_deficiencia ?? []),
            'tipo_deficiencia'           => $tiposProvided ? $tipoDefCsv : ($cidadao->tipo_deficiencia ?? null),

            'ha_gravida'                 => $haGravida,
            'nome_gravida'               => $this->keep($validated,'nome_gravida',$cidadao->nome_gravida),

            'ha_idoso'                   => $haIdoso,
            'nome_idoso'                 => $this->keep($validated,'nome_idoso',$cidadao->nome_idoso),

            'cid'                        => $this->keep($validated,'cid',$cidadao->cid),
            'observacoes'                => $this->keep($validated,'observacoes',$cidadao->observacoes),
            'observacoes_entrevistador'  => $this->keep($validated,'observacoes_entrevistador',$cidadao->observacoes_entrevistador),

            'data_declaracao'            => $dataDeclaracao,
            'nome_declarente'            => $this->keep($validated,'nome_declarente',$cidadao->nome_declarente),
            'nome_entrevistador'         => $this->keep($validated,'nome_entrevistador',$cidadao->nome_entrevistador),
        ])->save();

        return redirect()->route('assistente.usuarios.index')
            ->with('success','Cadastro finalizado com sucesso!');
    }

    public function atualizarAcessibilidade(Request $request, $id) { return $this->salvarAcessibilidade($request, $id); }

    /* ============================================================
     * Cartão público / senha / exclusão
     * ============================================================*/
    public function cartao($id)
    {
        $cidadao = Cidadao::with('bairro.cidade.estado')->findOrFail($id);
        // reuse a view pública que você já tem
        return view('cidadao.cartao-publico', compact('cidadao'));
    }

    public function senha($userId)
    {
        $usuario = User::with('cidadao')->findOrFail($userId);
        return view('assistente.cidadao.senha', compact('usuario'));
    }

    public function atualizarSenha(Request $request, $userId)
    {
        $request->validate([
            'password' => ['required','string','min:8','confirmed'],
        ]);

        $usuario = User::with('cidadao')->findOrFail($userId);
        $usuario->password = Hash::make($request->password);
        $usuario->save();

        return redirect()->route('assistente.cidadao.dados.editar', $usuario->cidadao->id)
            ->with('success', 'Senha atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $cidadao = Cidadao::with('user')->findOrFail($id);

        // remove foto do storage
        if ($cidadao->foto && Storage::disk('public')->exists('fotos/'.$cidadao->foto)) {
            Storage::disk('public')->delete('fotos/'.$cidadao->foto);
        }

        // apaga usuário vinculado (se for a regra do seu sistema)
        $user = $cidadao->user;
        $cidadao->delete();
        if ($user) $user->delete();

        return redirect()->route('assistente.usuarios.index')->with('success','Conta excluída.');
    }
}
