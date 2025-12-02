<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cidadao extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos via create() ou update()
    protected $fillable = [
        'user_id',
        'bairro_id',
        'nome',
        'cpf',
        'data_nascimento',
        'sexo',
        'telefone',
        'email',
        'cep',
        'rua',
        'numero',
        'complemento',
        'tipo_moradia',
        'tem_esgoto',
        'tem_agua_encanada',
        'tem_coleta_lixo',
        'tem_energia',
        'renda_total_familiar',
        'pessoas_na_residencia',
        'ocupacao',
        'grau_parentesco',
        'escolaridade',
        'pcd',
        'tipo_deficiencia',
        'observacoes',
        'possui_animais',
        'numero_animais',
        'foto',
        'cor_raca',
        'nis',
        'rg',
        'orgao_emissor',
        'data_emissao_rg',
        'titulo_eleitor',
        'zona',
        'secao',
        'codigo_cadunico',
        'possui_deficiencia',
        'cid',

    ];

    // Relacionamento: cada cidadão pertence a um usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bairro()
    {
        return $this->belongsTo(\App\Models\Bairro::class, 'bairro_id');
    }


    public function inscricoes()
    {
        return $this->hasMany(\App\Models\ProgramaInscricao::class);
    }

    public function acompanhamentos()
    {
        return $this->hasMany(\App\Models\Acompanhamento::class);
    }

    public function ultimoAcompanhamento()
    {
        return $this->hasOne(\App\Models\Acompanhamento::class)->latestOfMany();
    }


    public function dependentes()
    {
        return $this->hasMany(Dependente::class);
    }

    public function getRendaPerCapitaAttribute(): float
    {
        $pessoas = (int) ($this->pessoas_na_residencia ?? 0);
        $pessoas = max(1, $pessoas);
        $renda   = (float) ($this->renda_total_familiar ?? 0);

        return round($renda / $pessoas, 2);
    }


    public function temDadosMinimosParaInscricao(): bool
    {
        return filled($this->nome)
            && $this->cpfValido()
            && $this->temBairro()
            && !is_null($this->renda_total_familiar)
            && !is_null($this->pessoas_na_residencia)
            && (int) $this->pessoas_na_residencia > 0;
    }


    public function faltandoDadosMinimos(): array
    {
        $faltando = [];
        if (!filled($this->nome))                           $faltando[] = 'Nome Completo';
        if (!$this->cpfValido())                            $faltando[] = 'CPF';
        if (!$this->temBairro())                            $faltando[] = 'Bairro';
        if (is_null($this->renda_total_familiar))           $faltando[] = 'Renda Total Familiar (R$)';
        if (is_null($this->pessoas_na_residencia) || (int) $this->pessoas_na_residencia < 1)
                                                            $faltando[] = 'Pessoas na Residência';
        return $faltando;
    }

    private function cpfValido(): bool
    {
        $cpf = preg_replace('/\D/', '', (string) $this->cpf);
        return filled($cpf) && strlen($cpf) === 11; // (opcional: validar dígitos)
    }

    private function temBairro(): bool
    {
        return !is_null($this->bairro_id) || filled($this->attributes['bairro'] ?? null);
    }
   public function getCepFormatadoAttribute(): ?string
    {
        if (!$this->cep) return null;
        $d = preg_replace('/\D+/', '', $this->cep);
        return strlen($d) === 8 ? substr($d,0,5).'-'.substr($d,5) : $this->cep;
    }

    public function getEnderecoLinhaAttribute(): ?string
{
    // 1) Rua, número e complemento
    $end1 = trim(collect([$this->rua ?: null, $this->numero ?: null])->filter()->implode(', '));
    if ($this->complemento) {
        $end1 = $end1 ? ($end1.' - '.$this->complemento) : $this->complemento;
    }

    // 2) Bairros: prioriza RELAÇÃO; cai para coluna textual 'bairro' se existir
    $bRel       = $this->getRelationValue('bairro'); // evita colidir com atributo 'bairro'
    $bairroNome = optional($bRel)->nome ?? ($this->attributes['bairro'] ?? null);

    // 3) Cidade/UF via relação (se houver)
    $cidade = optional($bRel?->cidade)->nome;
    $uf     = optional($bRel?->cidade?->estado)->sigla
           ?: optional($bRel?->cidade?->estado)->uf;

    // 4) CEP
    $cep = $this->cep_formatado;

    $partes = collect([
        $end1 ?: null,
        $bairroNome,
        trim(collect([$cidade, $uf])->filter()->implode(' - ')) ?: null,
        $cep ? 'CEP '.$cep : null,
    ])->filter()->values()->all();

    return $partes ? implode(', ', $partes) : null;
}

}
