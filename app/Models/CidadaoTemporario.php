<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CidadaoTemporario extends Model
{
    use HasFactory;

    protected $table = 'cidadaos_temporarios';

    protected $fillable = [
        'nome',
        'motivo',
        'prazo_validade',
        'inicio_validez',
        'fim_validez',
        'status',
        'user_id',
    ];

    protected $casts = [
        'inicio_validez' => 'datetime',
        'fim_validez' => 'datetime',
    ];


    // RELAÇÃO COM VENDAS
    public function vendas()
    {
        return $this->hasMany(VendaRestaurante::class, 'cidadao_temporario_id');
    }

    // ESCOPO: verificar se está dentro da validade
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo')->where('fim_validez', '>=', now());
    }


    // ACESSOR: Dias restantes até fim da validade
    public function getDiasRestantesAttribute()
    {
        return now()->diffInDays($this->fim_validez, false);
    }

    public function getExpiradoAttribute()
    {
        return $this->fim_validez->lt(now());
    }

    public function getStatusFormatadoAttribute()
    {
        return $this->expirado ? 'Expirado' : 'Ativo';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
