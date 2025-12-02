<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class UsuarioController extends Controller
{

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('busca')) {
            $busca = $request->input('busca');
            $query->where(function ($q) use ($busca) {
                $q->where('name', 'like', "%$busca%")
                ->orWhere('email', 'like', "%$busca%")
                ->orWhere('cpf', 'like', "%$busca%");
            });
        }

        if ($request->filled('filtro_role')) {
            $role = $request->input('filtro_role');
            $query->whereHas('roles', fn ($q) => $q->where('name', $role));
        }

        $usuarios = $query->with('roles')->paginate(10);
        $todosRoles = Role::pluck('name');

        return view('admin.usuarios.index', compact('usuarios', 'todosRoles'));
    }


    public function create()
    {
        $roles = Role::pluck('name', 'name');
        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'cpf' => preg_replace('/\D+/', '', (string) $request->cpf),
        ]);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'cpf'      => 'required|string|size:11|unique:users,cpf',
            'password' => 'required|string|min:6',
            'role'     => 'required|exists:roles,name',
        ]);

        $usuario = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'cpf'      => $request->cpf, // já vem normalizado (11 dígitos)
            'password' => Hash::make($request->password),
        ]);

        $usuario->assignRole($request->role);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuário criado com sucesso.');
    }


    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        $roles   = Role::pluck('name', 'name');

        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        // normaliza antes de validar (se vier no form)
        if ($request->has('cpf')) {
            $request->merge([
                'cpf' => preg_replace('/\D+/', '', (string) $request->cpf),
            ]);
        }

        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $usuario->id,
            'role'     => 'required|exists:roles,name',
            'password' => 'nullable|min:6',
            // só valide CPF se enviado (mantém sem checagem algorítmica)
            'cpf'      => 'nullable|string|size:11|unique:users,cpf,' . $usuario->id,
        ]);

        $usuario->name  = $request->name;
        $usuario->email = $request->email;

        if ($request->filled('cpf')) {
            $usuario->cpf = $request->cpf; // já normalizado
        }

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();
        $usuario->syncRoles([$request->role]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }


    public function destroy($id)
    {
        $usuario = User::findOrFail($id);

        // Proteção opcional: evita apagar a si mesmo
        if (auth()->id() === $usuario->id) {
            return redirect()->back()->with('error', 'Você não pode apagar a si mesmo.');
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuário apagado com sucesso.');
    }


    public function relatorioPdf()
    {
        $usuarios = User::with('roles')->get();
        return view('admin.usuarios.relatorio', compact('usuarios'));
    }
}
