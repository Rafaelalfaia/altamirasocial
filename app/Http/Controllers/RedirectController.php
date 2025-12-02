<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class RedirectController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Verifique qual role o usuário realmente possui
        if ($user->hasRole('Cidadao')) {
            return redirect()->route('cidadao.dashboard');
        } elseif ($user->hasRole('Coordenador')) {
            return redirect()->route('coordenador.dashboard');
        } elseif ($user->hasRole('Tecnico')) {
            return redirect()->route('tecnico.dashboard');
        } elseif ($user->hasRole('Assistente')) {
            return redirect()->route('assistente.dashboard');
        } elseif ($user->hasRole('Cidadao')) {
            return redirect()->route('cidadao.dashboard');
        }

        // fallback
        abort(403, 'Acesso não autorizado');
    }
}
