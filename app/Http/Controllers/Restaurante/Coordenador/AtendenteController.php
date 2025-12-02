<?php

namespace App\Http\Controllers\Restaurante\Coordenador;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Restaurante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class AtendenteController extends Controller
{
    public function index()
    {
        $usuario = Auth::user()->load('restaurantes'); // carrega restaurantes vinculados

        $restauranteIds = $usuario->restaurantes ? $usuario->restaurantes->pluck('id') : collect();

        $atendentes = User::whereHas('restaurantes', function ($q) use ($restauranteIds) {
            $q->whereIn('restaurante_id', $restauranteIds);
        })->role('Atendente Restaurante')->get();

        return view('restaurante.coordenador.atendentes.index', compact('atendentes'));
    }

    public function create()
    {
        $usuario = Auth::user()->load('restaurantes');
        $restaurantes = $usuario->restaurantes;

        return view('restaurante.coordenador.atendentes.create', compact('restaurantes'));
    }

    public function store(Request $request)
    {
        // Normaliza o CPF (remove pontos e traços, deixa só os números)
        $cpfLimpo = preg_replace('/\D/', '', $request->cpf);
        $request->merge(['cpf' => $cpfLimpo]);

        // Validação
        $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => [
                'required',
                'digits:11',
                Rule::unique('users', 'cpf'),
            ],
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'restaurantes' => 'required|array',
            'restaurantes.*' => 'exists:restaurantes,id',
        ], [
            'cpf.digits' => 'O CPF deve conter exatamente 11 dígitos numéricos.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
        ]);

        // Cria o atendente
        $atendente = User::create([
            'name' => $request->name,
            'cpf' => $cpfLimpo,
            'email' => $request->email ?? null,
            'password' => Hash::make($request->password),
            'restaurante_id' => $request->restaurantes[0],
        ]);


        // Atribui role
        $atendente->assignRole('Atendente Restaurante');

        // Relaciona com restaurantes
        $atendente->restaurantes()->sync($request->restaurantes);

        return redirect()
            ->route('restaurante.coordenador.atendentes.index')
            ->with('success', 'Atendente cadastrado com sucesso.');
    }


    public function edit(User $atendente)
    {
        if (!$atendente->hasRole('Atendente Restaurante')) {
            abort(403, 'Usuário não é um atendente.');
        }

        $usuario = Auth::user()->load('restaurantes');
        $restaurantes = $usuario->restaurantes;

        return view('restaurante.coordenador.atendentes.edit', compact('atendente', 'restaurantes'));
    }

    public function update(Request $request, User $atendente)
    {
        if (!$atendente->hasRole('Atendente Restaurante')) {
            abort(403, 'Usuário não é um atendente.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => [
                'required',
                'string',
                'size:11',
                'regex:/^\d{11}$/',
                Rule::unique('users', 'cpf')->ignore($atendente->id),
            ],
            'email' => 'nullable|email|max:255|unique:users,email,' . $atendente->id,
            'password' => 'nullable|string|min:6|confirmed',
            'restaurantes' => 'required|array',
            'restaurantes.*' => 'exists:restaurantes,id',
        ]);

        $atendente->update([
            'name' => $request->name,
            'cpf' => $request->cpf,
            'email' => $request->email ?? null,
            'password' => $request->password ? Hash::make($request->password) : $atendente->password,
        ]);

        $atendente->restaurantes()->sync($request->restaurantes);

        return redirect()
            ->route('restaurante.coordenador.atendentes.index')
            ->with('success', 'Atendente atualizado com sucesso.');
    }

    public function destroy(User $atendente)
    {
        if (!$atendente->hasRole('Atendente Restaurante')) {
            abort(403, 'Usuário não é um atendente.');
        }

        $atendente->restaurantes()->detach();
        $atendente->delete();

        return redirect()
            ->route('restaurante.coordenador.atendentes.index')
            ->with('success', 'Atendente removido com sucesso.');
    }
}
