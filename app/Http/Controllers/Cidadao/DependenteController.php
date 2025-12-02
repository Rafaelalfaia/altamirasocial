<?php

namespace App\Http\Controllers\Cidadao;

use App\Http\Controllers\Controller;
use App\Models\Dependente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DependenteController extends Controller
{
    /**
     * Lista todos os dependentes do cidadão autenticado.
     */
    public function index()
    {
        $cidadao = Auth::user()->cidadao;

        $dependentes = $cidadao->dependentes()->latest()->get();

        return view('cidadao.dependentes.index', compact('dependentes'));
    }

    /**
     * Mostra o formulário para criar um novo dependente.
     */
    public function create()
    {
        return view('cidadao.dependentes.create');
    }

    /**
     * Armazena o dependente criado pelo cidadão.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'data_nascimento' => 'required|date',
            'cpf' => 'nullable|regex:/^\d{11}$/|unique:dependentes,cpf',
            'grau_parentesco' => 'required|string|max:100',
            'sexo' => 'nullable|in:masculino,feminino,outro',
            'escolaridade' => 'nullable|string|max:100',
        ]);

        Auth::user()->cidadao->dependentes()->create($request->all());

        return redirect()
            ->route('dependentes.index')
            ->with('success', 'Dependente cadastrado com sucesso!');
    }

    /**
     * Mostra o formulário para editar um dependente existente.
     */
    public function edit(Dependente $dependente)
    {
        $this->autorizar($dependente);

        return view('cidadao.dependentes.edit', compact('dependente'));
    }

    /**
     * Atualiza os dados de um dependente.
     */
    public function update(Request $request, Dependente $dependente)
    {
        $this->autorizar($dependente);

        $request->validate([
            'nome' => 'required|string|max:255',
            'data_nascimento' => 'required|date',
            'cpf' => 'nullable|regex:/^\d{11}$/|unique:dependentes,cpf,' . $dependente->id,
            'grau_parentesco' => 'required|string|max:100',
            'sexo' => 'nullable|in:masculino,feminino,outro',
            'escolaridade' => 'nullable|string|max:100',
        ]);

        $dependente->update($request->all());

        return redirect()
            ->route('dependentes.index')
            ->with('success', 'Dependente atualizado com sucesso!');
    }

    /**
     * Remove um dependente do cidadão autenticado.
     */
    public function destroy(Dependente $dependente)
    {
        $this->autorizar($dependente);

        $dependente->delete();

        return redirect()
            ->route('dependentes.index')
            ->with('success', 'Dependente removido com sucesso!');
    }

    /**
     * Verifica se o dependente pertence ao cidadão autenticado.
     */
    private function autorizar(Dependente $dependente)
    {
        $cidadaoId = Auth::user()->cidadao->id;

        if ($dependente->cidadao_id !== $cidadaoId) {
            return redirect()->route('dependentes.index')->with('error', 'Você não tem permissão para acessar esse dependente.');
        }
    }

}
