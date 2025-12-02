<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DenunciaPrograma extends Model
{
    use HasFactory;

    protected $table = 'denuncia_programas';

    protected $fillable = [
        'programa_id',
        'cidadao_id',
        'user_id',
        'motivo',
        'denunciado_em',
    ];

    protected $dates = ['denunciado_em'];

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    public function cidadao()
    {
        return $this->belongsTo(Cidadao::class);
    }

    public function assistente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
