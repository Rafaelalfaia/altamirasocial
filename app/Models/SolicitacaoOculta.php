<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitacaoOculta extends Model
{
    use HasFactory;

    protected $table = 'solicitacao_ocultas';

    protected $fillable = [
        'user_id',
        'solicitacao_id',
    ];

    // Relacionamento com usuário (quem ocultou)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento com a solicitação original
    public function solicitacao()
    {
        return $this->belongsTo(Solicitacao::class);
    }
}
