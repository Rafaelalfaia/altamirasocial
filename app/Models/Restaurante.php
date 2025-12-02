<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurante extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nome',
        'responsavel',
        'telefone',
        'endereco',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendas()
    {
        return $this->hasMany(VendaRestaurante::class);
    }

    public function atendentes()
    {
        return $this->belongsToMany(User::class, 'restaurante_user')
            ->withTimestamps()
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Atendente Restaurante');
            });
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'restaurante_user');
    }


    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'restaurante_user');
    }


}
