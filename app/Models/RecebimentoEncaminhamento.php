<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecebimentoEncaminhamento extends Model
{
    use HasFactory;

    protected $table = 'recebimentos_encaminhamentos'; // 👈 define o nome correto da tabela

    protected $fillable = [
        'coordenador_id',
        'orgao_publico_id',
        'tipo',
        'cidadao_id',
        'nome_cidadao',
        'programa_social_id',
        'descricao',
    ];

    public function coordenador()
    {
        return $this->belongsTo(User::class, 'coordenador_id');
    }

    public function orgao()
    {
        return $this->belongsTo(OrgaoPublico::class, 'orgao_publico_id');
    }

    public function cidadao()
    {
        return $this->belongsTo(Cidadao::class, 'cidadao_id');
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'programa_social_id');
    }
}

