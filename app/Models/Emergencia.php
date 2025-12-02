<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emergencia extends Model
{
    protected $fillable = [
        'cidadao_id',
        'user_id',
        'motivo',
        'descricao',
        'sala',
        'status',
    ];

    /**
     * Cidadão que solicitou a emergência.
     */
    public function cidadao()
    {
        return $this->belongsTo(Cidadao::class);
    }

    /**
     * Assistente que atendeu a emergência.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function assistente()
    {
        return $this->belongsTo(User::class, 'assistente_id');
    }

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }




}
