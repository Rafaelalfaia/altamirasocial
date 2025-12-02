<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evolucao extends Model
{
    protected $table = 'evolucoes';

    protected $fillable = [
        'acompanhamento_id',
        'user_id',
        'titulo',
        'resumo',
        'local_atendimento',
        'caso_emergencial',
        'descricao_emergencial',
        'tentativa_homicidio',
    ];

    // Relação com o acompanhamento
    public function acompanhamento(): BelongsTo
    {
        return $this->belongsTo(Acompanhamento::class);
    }



    // Relação com o usuário (assistente que criou)
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
