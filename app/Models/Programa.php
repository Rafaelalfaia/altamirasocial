<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'vagas',
        'valor_corte',
        'publico_alvo',
        'status',
        'foto_perfil',
        'foto_capa',
        'regioes',
        'aceita_menores',
        'user_id',
        'recomendado',
        'recomendado_por',
        'recomendado_em',
        'recomendacao_ordem',
    ];

    protected $casts = [
        'regioes' => 'array',
        'aceita_menores' => 'boolean',
        'vagas' => 'integer',
        'valor_corte' => 'float',
        'recomendado' => 'boolean',
        'recomendado_em' => 'datetime',
        'recomendacao_ordem' => 'integer',
    ];

    /**
     * Criador do programa (usuário responsável)
     */
    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Inscrições feitas no programa
     */
    public function inscricoes()
    {
        return $this->hasMany(ProgramaInscricao::class);
    }

    public function recomendadoPorUser()
    {
        return $this->belongsTo(User::class, 'recomendado_por');
    }

    public function scopeOrdenadoParaCidadao($q) {
        return $q->orderByDesc('recomendado')
                ->orderByRaw('COALESCE(recomendacao_ordem, 999999) ASC')
                ->orderByDesc('created_at');
    }

    public function scopeFiltroAdmin($q, ?string $busca = null, ?string $status = null, ?string $recomendado = null)
    {
        if ($busca) {
            $q->where(function($qq) use ($busca) {
                $qq->where('nome','like',"%{$busca}%")
                   ->orWhere('descricao','like',"%{$busca}%");
            });
        }
        if ($status) {
            $q->where('status', $status);
        }
        if ($recomendado === 'sim') {
            $q->where('recomendado', true);
        } elseif ($recomendado === 'nao') {
            $q->where('recomendado', false);
        }
        return $q;
    }

    public function scopeAtivos($q)
    {
        return $q->where('status', 'ativado');
    }
}
