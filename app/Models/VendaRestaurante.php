<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendaRestaurante extends Model
{
    use HasFactory;

    protected $table = 'venda_restaurantes';

    protected $fillable = [
        'restaurante_id',
        'tipo_cliente',
        'cidadao_id',
        'cidadao_temporario_id',
        'numero_pratos',
        'tipo_consumo',
        'forma_pagamento',
        'data_venda',
        'finalizado',
        'estudante',
        'doacao',
    ];

    // Relacionamento com cidadão regular
    public function cidadao()
    {
        return $this->belongsTo(Cidadao::class);
    }

    public function cidadaoTemporario()
    {
        return $this->belongsTo(CidadaoTemporario::class);
    }

   public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'restaurante_id');
    }
    public function restaurante()
    {
        return $this->belongsTo(\App\Models\Restaurante::class);
    }


}
