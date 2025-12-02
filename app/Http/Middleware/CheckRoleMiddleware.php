<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Usuário não autenticado.');
        }

        if (!$user->hasRole($role)) {
            abort(403, 'Usuário não possui a role: ' . $role);
        }

        return $next($request);
    }
}
