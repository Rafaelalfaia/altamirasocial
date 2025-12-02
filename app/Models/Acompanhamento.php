<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acompanhamento extends Model
{
    use HasFactory;

    protected $fillable = [
        // chaves estrangeiras
        'cidadao_id',
        'user_id',
        'assistente_id',
        'data',

        // identificação
        'nome_unidade',
        'nome_responsavel',
        'apelido',
        'sexo',
        'data_nascimento',
        'naturalidade',
        'cpf',
        'nis',
        'rg',
        'orgao_emissor',
        'data_emissao',
        'titulo_eleitor',
        'zona',
        'secao',
        'codigo_cadunico',
        'endereco',
        'numero',
        'bairro',
        'ponto_referencia',
        'estado_civil',
        'whatsapp',

        // socioeconômico
        'cor',
        'equipamentos_comunitarios',
        'situacao_moradia',
        'tempo_residencia',
        'quantidade_comodos',
        'tipo_construcao',
        'energia',
        'agua',
        'esgoto',
        'lixo',
        'tipo_rua',
        'possui_gravida',
        'nome_gravida',
        'possui_idoso',
        'nome_idoso',
        'situacao_profissional',
        'possui_deficiencia',
        'tipos_deficiencia',
        'observacoes'
    ];

    protected $casts = [
        'equipamentos_comunitarios' => 'array',
        'tipos_deficiencia' => 'array',
        'possui_gravida' => 'boolean',
        'possui_idoso' => 'boolean',
        'possui_deficiencia' => 'boolean',
        'data_nascimento' => 'date',
        'data_emissao' => 'date',
        'data' => 'date',
    ];

    public function cidadao()
    {
        return $this->belongsTo(Cidadao::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }


    public function assistente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function composicaoFamiliar()
    {
        return $this->hasMany(ComposicaoFamiliar::class);
    }

    public function evolucoes()
    {
        return $this->hasMany(\App\Models\Evolucao::class);
    }


}
