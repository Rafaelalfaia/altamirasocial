<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckImpersonationOrRole
{
    public function handle($request, Closure $next, $role)
    {
        // Se estiver impersonando, deixa passar
        if (session()->has('impersonate_admin_id')) {
            return $next($request);
        }

        // Se tiver a role normalmente, também passa
        if (Auth::check() && Auth::user()->hasRole($role)) {
            return $next($request);
        }

        // Caso contrário, bloqueia
        abort(403, 'Você não tem as permissões necessárias.');
    }
}
