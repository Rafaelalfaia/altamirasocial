<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramaDependente extends Model
{
    protected $fillable = [
        'programa_inscricao_id',
        'nome',
        'cpf',
        'grau_parentesco',
    ];

    public function inscricao()
    {
        return $this->belongsTo(ProgramaInscricao::class);
    }
}

