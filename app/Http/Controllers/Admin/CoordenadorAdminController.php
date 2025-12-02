<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CoordenadorAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('Coordenador');

        if ($request->filled('busca')) {
            $busca = $request->input('busca');
            $query->where(function ($q) use ($busca) {
                $q->where('name', 'like', "%$busca%")
                  ->orWhere('email', 'like', "%$busca%")
                  ->orWhere('cpf', 'like', "%$busca%")
                  ->orWhere('telefone', 'like', "%$busca%");
            });
        }

        $coordenadores = $query->paginate(10);

        return view('admin.coordenadores.index', compact('coordenadores'));
    }

    public function entrar($id)
    {
        $coordenador = User::findOrFail($id);

        // Evita impersonar outro admin
        if ($coordenador->hasRole('Admin')) {
            abort(403, 'Você não pode impersonar outro Admin.');
        }

        // Salva o ID do admin e do coordenador impersonado
        session()->put('impersonate_admin_id', auth()->id());
        session()->put('impersonate_coordenador_id', $coordenador->id);

        // Login como o coordenador
        auth()->loginUsingId($coordenador->id);

        return redirect()->route('coordenador.dashboard');
    }

    public function sair()
    {
        if (session()->has('impersonate_admin_id')) {
            $adminId = session()->pull('impersonate_admin_id');
            session()->forget('impersonate_coordenador_id');
            auth()->loginUsingId($adminId);

            return redirect()->route('admin.coordenadores')->with('status', 'Retornado ao perfil de administrador.');
        }

        return redirect()->route('dashboard');
    }


}
