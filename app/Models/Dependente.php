<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dependente extends Model
{
    protected $table = 'dependentes';

    protected $fillable = [
        'cidadao_id',
        'nome',
        'data_nascimento',
        'cpf',
        'grau_parentesco',
        'sexo',
        'escolaridade',
    ];

    public function cidadao()
    {
        return $this->belongsTo(Cidadao::class);
    }


    public function inscricoes()
    {
        return $this->hasMany(ProgramaInscricao::class);
    }
}
