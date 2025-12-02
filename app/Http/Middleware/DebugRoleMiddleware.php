<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class DebugRoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Usuário não autenticado');
        }

        return response()->json([
            'user_id' => $user->id,
            'user_email' => $user->email,
            'guard' => Auth::getDefaultDriver(),
            'role_solicitada' => $role,
            'roles_do_usuario' => $user->getRoleNames(),
            'tem_role' => $user->hasRole($role),
        ]);
    }
}
