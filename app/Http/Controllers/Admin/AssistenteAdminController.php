<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class AssistenteAdminController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->input('busca');

        $assistentes = User::role('Assistente')
            ->when($busca, function ($query, $busca) {
                $query->where(function ($q) use ($busca) {
                    $q->where('name', 'like', "%{$busca}%")
                      ->orWhere('email', 'like', "%{$busca}%");
                });
            })
            ->with('roles', 'coordenadores')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.assistentes.index', compact('assistentes', 'busca'));
    }

    public function edit(User $assistente)
    {
        $assistente->load('coordenadores');

        $todosCoordenadores = User::role('Coordenador')->get();
        $rolesDisponiveis = Role::pluck('name', 'name');

        return view('admin.assistentes.edit', compact('assistente', 'todosCoordenadores', 'rolesDisponiveis'));
    }

    public function update(Request $request, User $assistente)
    {
        $request->validate([
            'roles' => 'array',
            'coordenadores' => 'array',
        ]);

        $assistente->syncRoles($request->roles ?? []);
        $assistente->coordenadores()->sync($request->coordenadores ?? []);

        return redirect()->route('admin.assistentes')->with('status', 'Assistente atualizado com sucesso!');
    }

    public function entrar($id)
    {
        $assistente = User::findOrFail($id);

        // Segurança: garante que o usuário seja assistente
        if (!$assistente->hasRole('Assistente')) {
            abort(403, 'Apenas assistentes podem ser acessados por aqui.');
        }

        // Salva o admin atual que está impersonando
        session()->put('impersonate_admin_id', auth()->id());
        session()->put('impersonate_assistente_id', $assistente->id);

        // Faz login como o assistente
        auth()->loginUsingId($assistente->id);

        return redirect()->route('dashboard');
    }


    public function sair()
    {
        if (session()->has('impersonate_admin_id')) {
            $adminId = session()->pull('impersonate_admin_id');
            session()->forget('impersonate_assistente_id');
            Auth::loginUsingId($adminId);

            return redirect()->route('admin.assistentes')->with('status', 'Você voltou ao perfil de administrador.');
        }

        return redirect()->route('dashboard');
    }
}
