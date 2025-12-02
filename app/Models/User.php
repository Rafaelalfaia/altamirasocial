<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;



    /**
     * Atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'name',
        'email',
        'cpf',
        'telefone',
        'password',
        'dica_senha',
        'foto',
        'coordenador_id'

    ];

    /**
     * Atributos ocultos na serialização.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atributos que devem ser convertidos para tipos nativos.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel fará o hash automaticamente
    ];

    /**
     * Relacionamento com o modelo Cidadao.
     */
    public function cidadao()
    {
        return $this->hasOne(\App\Models\Cidadao::class);
    }

    public function programas()
    {
        return $this->hasMany(Programa::class);
    }


    public function coordenador()
    {
        return $this->belongsTo(User::class, 'coordenador_id');
    }

    public function assistentesCriados()
    {
        return $this->hasMany(User::class, 'coordenador_id');
    }

    public function evolucoes()
    {
        return $this->hasMany(\App\Models\Evolucao::class, 'user_id');
    }

        public function solicitacoes()
    {
        return $this->hasMany(\App\Models\Solicitacao::class, 'destinatario_id');
    }

    public function coordenadores()
    {
        return $this->belongsToMany(User::class, 'coordenador_assistente', 'assistente_id', 'coordenador_id');
    }

        public function getFotoUrlAttribute()
    {
        $caminho = storage_path('app/public/fotos/' . $this->foto);

        if ($this->foto && file_exists($caminho)) {
            return asset('storage/fotos/' . $this->foto);
        }

        return asset('imagens/avatar-padrao.png'); // imagem padrão em public/imagens/
    }

    public function emergencias()
    {
        return $this->hasMany(Emergencia::class, 'assistente_id');
    }

    public function assistente()
    {
        return $this->belongsTo(User::class, 'user_id'); // ou 'responsavel_id'
    }



    public function restaurantes()
{
    return $this->belongsToMany(Restaurante::class);
}









}
