<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cidadao;
use Illuminate\Support\Facades\Auth;

class CidadaoAdminController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->input('busca');

        $cidadaos = Cidadao::with('user')
            ->when($busca, function ($query, $busca) {
                $query->whereHas('user', function ($q) use ($busca) {
                    $q->where('name', 'like', "%{$busca}%")
                      ->orWhere('email', 'like', "%{$busca}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.cidadaos.index', compact('cidadaos', 'busca'));
    }

    public function entrar($id)
    {
        $usuario = User::findOrFail($id);

        // Verifica se realmente é um cidadão
        if (!$usuario->hasRole('Cidadao')) {
            abort(403, 'Apenas cidadãos podem ser acessados por aqui.');
        }

        // Se quem está logado for admin
        if (auth()->user()->hasRole('Admin')) {
            session()->put('impersonate_admin_id', auth()->id());
        }

        // Se for assistente
        if (auth()->user()->hasRole('Assistente')) {
            session()->put('impersonate_assistente_id', auth()->id());
        }

        session()->put('impersonate_cidadao_id', $usuario->id);

        auth()->loginUsingId($usuario->id);

        return redirect()->route('dashboard');
    }

    public function sair()
    {
        // Voltar para Admin
        if (session()->has('impersonate_admin_id')) {
            $adminId = session()->pull('impersonate_admin_id');
            session()->forget('impersonate_cidadao_id');
            auth()->loginUsingId($adminId);

            return redirect()->route('admin.cidadaos')->with('status', 'Retornado ao perfil de administrador.');
        }

        // Voltar para Assistente
        if (session()->has('impersonate_assistente_id')) {
            $assistenteId = session()->pull('impersonate_assistente_id');
            session()->forget('impersonate_cidadao_id');
            auth()->loginUsingId($assistenteId);

            return redirect()->route('assistente.dashboard')->with('status', 'Retornado ao perfil de assistente.');
        }

        return redirect()->route('dashboard');
    }
}
