<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Exibe a tela de login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Processa o login.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Redirecionamento com base na role
        if ($user->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('Coordenador')) {
            return redirect()->route('coordenador.dashboard');
        }

        if ($user->hasRole('Tecnico')) {
            return redirect()->route('tecnico.dashboard');
        }

        if ($user->hasRole('Assistente')) {
            return redirect()->route('assistente.dashboard');
        }

        if ($user->hasRole('Cidadao')) {
            return redirect()->route('cidadao.dashboard');
        }

        if ($user->hasRole('Coordenador Restaurante')) {
            return redirect()->route('restaurante.coordenador.dashboard');
        }

        if ($user->hasRole('Atendente Restaurante')) {
            return redirect()->route('restaurante.atendente.dashboard');
        }

        if ($user->hasRole('Restaurante')) {
            return redirect()->route('restaurante.dashboard');
        }

        Auth::logout();
        abort(403, 'Acesso não autorizado. Nenhum papel atribuído.');
    }


    /**
     * Faz logout do usuário autenticado.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
