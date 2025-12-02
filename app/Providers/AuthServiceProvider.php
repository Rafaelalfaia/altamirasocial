<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Models e Policies
use App\Models\Programa;
use App\Policies\ProgramaPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapeamento de modelos para suas respectivas policies.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Programa::class => ProgramaPolicy::class,
        // Adicione outros modelos aqui, se necessário
    ];

    /**
     * Registra serviços de autenticação/autorização.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        
    }
}
