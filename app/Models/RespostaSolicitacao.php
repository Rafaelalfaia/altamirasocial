<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespostaSolicitacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'solicitacao_id',
        'user_id',
        'resposta',
        'concluida',
    ];

    public function solicitacao()
    {
        return $this->belongsTo(Solicitacao::class);
    }

    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
