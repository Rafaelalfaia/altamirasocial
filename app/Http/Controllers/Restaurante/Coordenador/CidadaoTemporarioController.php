<?php

namespace App\Http\Controllers\Restaurante\Coordenador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CidadaoTemporario;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CidadaoTemporarioController extends Controller
{
    // Listagem
    public function index(Request $request)
    {
        $search = $request->input('search');

        $cidadaos = CidadaoTemporario::with('user')
        ->when($search, fn($query) =>
            $query->where('nome', 'like', "%{$search}%")
                ->orWhere('motivo', 'like', "%{$search}%")
        )
        ->orderByDesc('created_at')
        ->paginate(15);

        return view('restaurante.coordenador.temporarios.index', compact('cidadaos', 'search'));
    }

    // Formulário de criação
    public function create()
    {
        return view('restaurante.coordenador.temporarios.create');
    }

    // Armazenar novo
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'motivo' => 'nullable|string|max:255',
            'prazo_validade' => 'nullable|integer|min:1',
        ]);

        $dias = $request->prazo_validade ?? 30;

        $inicio = now();
        $fim = now()->addDays($dias);

        CidadaoTemporario::create([
            'nome' => $request->nome,
            'motivo' => $request->motivo,
            'prazo_validade' => $dias,
            'inicio_validez' => $inicio,
            'fim_validez' => $fim,
            'status' => 'ativo',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('restaurante.coordenador.temporarios.index')
                        ->with('mensagem', 'Cidadão temporário cadastrado com sucesso!');
    }


    // Formulário de edição
   public function edit($id)
    {
        $cidadao = CidadaoTemporario::findOrFail($id);
        return view('restaurante.coordenador.temporarios.edit', compact('cidadao'));
    }


    // Atualização
    public function update(Request $request, $id)
    {
        $cidadao = CidadaoTemporario::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'motivo' => 'nullable|string|max:255',
        ]);

        $cidadao->update([
            'nome' => $request->nome,
            'motivo' => $request->motivo,
        ]);

        return redirect()->route('restaurante.coordenador.temporarios.index')
                        ->with('mensagem', 'Cidadão temporário atualizado com sucesso!');
    }



    // Renovar validade manualmente
   public function renovar(Request $request, $id)
    {
        $cidadao = CidadaoTemporario::findOrFail($id);

        $dias = intval($request->input('prazo', 30));

        $cidadao->fim_validez = \Carbon\Carbon::parse($cidadao->fim_validez)->addDays($dias);
        $cidadao->save();

        return redirect()->route('restaurante.coordenador.temporarios.index')
                        ->with('mensagem', 'Prazo renovado por mais ' . $dias . ' dias!');
    }


    // Exclusão
   public function destroy($id)
    {
        $cidadao = CidadaoTemporario::findOrFail($id);
        $cidadao->delete();

        return redirect()->route('restaurante.coordenador.temporarios.index')
                        ->with('mensagem', 'Cidadão temporário removido com sucesso!');
    }

}
