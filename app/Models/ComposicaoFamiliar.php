<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComposicaoFamiliar extends Model
{
    protected $table = 'composicoes_familiares';

    protected $fillable = [
        'acompanhamento_id',
        'nome',
        'data_nascimento',
        'parentesco',
        'escolaridade',
        'beneficio',
        'valor_beneficio',
        'profissao',
        'renda_bruta',
    ];

    public function acompanhamento()
    {
        return $this->belongsTo(Acompanhamento::class);
    }
}
