<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LotePagamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'status',
        'data_envio',
        'programa_id',
        'valor_pagamento',
        'formato_cpf',
        'periodo_pagamento',
        'previsao_pagamento',
        'regiao',
        'user_id',
    ];

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    public function inscricoes()
    {
        return $this->hasMany(\App\Models\ProgramaInscricao::class, 'programa_id', 'programa_id')
            ->where('status', 'aprovado');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
