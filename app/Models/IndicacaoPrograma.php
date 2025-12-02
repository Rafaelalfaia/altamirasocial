<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicacaoPrograma extends Model
{
    use HasFactory;

    protected $table = 'indicacoes_programas'; // ou 'indicacao_programas', conforme seu migration

    protected $fillable = [
        'programa_id',
        'cidadao_id',
        'user_id',
        'status',                // null, 'aprovada', 'reprovada'
        'avaliado_em',
        'resposta_coordenador',
    ];

    protected $dates = [
        'avaliado_em',
        'created_at',
        'updated_at',
    ];

    

    // Relacionamento com Programa
    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    // Relacionamento com Cidadão
    public function cidadao()
    {
        return $this->belongsTo(Cidadao::class);
    }

    // Relacionamento com o usuário (assistente) que indicou
    public function assistente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Escopo para apenas pendentes
    public function scopePendentes($query)
    {
        return $query->whereNull('status');
    }

    // Escopo para apenas aprovadas
    public function scopeAprovadas($query)
    {
        return $query->where('status', 'aprovada');
    }

    // Escopo para apenas reprovadas
    public function scopeReprovadas($query)
    {
        return $query->where('status', 'reprovada');
    }
}
