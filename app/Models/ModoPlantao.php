<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModoPlantao extends Model
{
    protected $table = 'modo_plantao';

    protected $fillable = ['user_id', 'ativo'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

}
