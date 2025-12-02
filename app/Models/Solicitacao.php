<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solicitacao extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'solicitacoes';
    protected $fillable = [
        'titulo',
        'mensagem',
        'resposta',
        'user_id',
        'destinatario_id',
        'destinatario_tipo',
        'status',
    ];


    public function respostas()
    {
        return $this->hasMany(RespostaSolicitacao::class);
    }

    public function coordenador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }



}
