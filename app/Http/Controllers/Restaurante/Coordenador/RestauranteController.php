<?php

namespace App\Http\Controllers\Restaurante\Coordenador;

use App\Http\Controllers\Controller;
use App\Models\Restaurante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestauranteController extends Controller
{
    public function index()
    {
        $restaurantes = Auth::user()->restaurantes()->latest()->paginate(10);
        return view('restaurante.coordenador.restaurantes.index', compact('restaurantes'));
    }

    public function create()
    {
        return view('restaurante.coordenador.restaurantes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'nullable|string|max:255',
            'ativo' => 'boolean',
        ]);

        $restaurante = Restaurante::create([
            'nome' => $request->nome,
            'endereco' => $request->endereco,
            'ativo' => $request->boolean('ativo'),
        ]);

        // vincula automaticamente ao coordenador logado
        $restaurante->usuarios()->attach(Auth::id());

        return redirect()->route('restaurante.coordenador.restaurantes.index')
            ->with('success', 'Restaurante criado com sucesso.');
    }

    public function edit(Restaurante $restaurante)
    {
        // autoriza que o restaurante pertença ao coordenador
        if (!$restaurante->usuarios->contains(Auth::id())) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('restaurante.coordenador.restaurantes.edit', compact('restaurante'));
    }

    public function update(Request $request, Restaurante $restaurante)
    {
        if (!$restaurante->usuarios->contains(Auth::id())) {
            abort(403, 'Acesso não autorizado.');
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'nullable|string|max:255',
            'ativo' => 'boolean',
        ]);

        $restaurante->update([
            'nome' => $request->nome,
            'endereco' => $request->endereco,
            'ativo' => $request->boolean('ativo'),
        ]);

        return redirect()->route('restaurante.coordenador.restaurantes.index')
            ->with('success', 'Restaurante atualizado com sucesso.');
    }

    public function destroy(Restaurante $restaurante)
    {
        if (!$restaurante->usuarios->contains(Auth::id())) {
            abort(403, 'Acesso não autorizado.');
        }

        $restaurante->delete();

        return redirect()->route('restaurante.coordenador.restaurantes.index')
            ->with('success', 'Restaurante removido com sucesso.');
    }
}
