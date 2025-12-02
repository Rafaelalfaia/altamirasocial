<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrgaoPublico extends Model
{
    use HasFactory;

    protected $table = 'orgaos_publicos'; // 👈 define o nome correto da tabela

    protected $fillable = ['coordenador_id', 'nome'];

    public function coordenador()
    {
        return $this->belongsTo(User::class, 'coordenador_id');
    }
}
