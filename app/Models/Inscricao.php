<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscricao extends Model
{
    protected $table = 'inscricoes';

    protected $fillable = [
        'cidadao_id',
        'programa_id',
        'status',
    ];

    public function cidadao()
    {
        return $this->belongsTo(Cidadao::class);
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }
}
