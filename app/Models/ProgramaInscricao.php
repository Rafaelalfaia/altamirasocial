<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgramaInscricao extends Model
{
    use HasFactory;

    // 👇 Corrige o nome da tabela usada
    protected $table = 'programa_inscricoes';

    protected $fillable = [
        'programa_id',
        'cidadao_id',
        'dependente_id',
        'status',
        'regiao_origem',
        'regiao',
    ];

    public function cidadao()
    {
        return $this->belongsTo(Cidadao::class);
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    public function dependente()
    {
        return $this->belongsTo(Dependente::class);
    }






}
