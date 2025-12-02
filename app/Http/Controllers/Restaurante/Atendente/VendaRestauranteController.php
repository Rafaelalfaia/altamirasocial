<?php

namespace App\Http\Controllers\Restaurante\Atendente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VendaRestaurante;
use App\Models\Cidadao;
use App\Models\CidadaoTemporario;
use Illuminate\Support\Carbon;

class VendaRestauranteController extends Controller
{
    public function index()
    {
        $hoje = Carbon::today();

        $vendas = VendaRestaurante::with(['cidadao', 'cidadaoTemporario', 'user'])
            ->whereDate('data_venda', $hoje)
            ->where('user_id', Auth::id())
            ->where('finalizado', false) // 🔥 Aqui está o que faltava!
            ->orderBy('data_venda', 'desc')
            ->get();

        return view('restaurante.atendente.vendas.index', compact('vendas'));
    }


    public function create()
    {
        $cidadaosNormais = Cidadao::select('id', 'nome', 'cpf')
            ->get()
            ->map(fn($c) => (object)[
                'id' => $c->id,
                'nome' => $c->nome,
                'cpf' => $c->cpf,
                'tipo' => 'normal',
            ]);

        $temporarios = CidadaoTemporario::where('status', 'ativo')
            ->select('id', 'nome', 'cpf')
            ->get()
            ->map(fn($t) => (object)[
                'id' => $t->id,
                'nome' => $t->nome,
                'cpf' => $t->cpf,
                'tipo' => 'temporario',
            ]);

        $cidadaos = $cidadaosNormais->concat($temporarios)->sortBy('nome')->values();

        return view('restaurante.atendente.vendas.create', compact('cidadaos'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $restaurante = $user->restaurantes()->first(); // relacionamento N:N

        if (!$restaurante) {
            return back()->withErrors(['restaurante' => 'Usuário não vinculado a um restaurante.'])->withInput();
        }

        $request->validate([
            'tipo_cidadao' => 'required|in:normal,temporario',
            'cidadao_id' => 'required|numeric',
            'tipo_consumo' => 'required|in:local,retirada',
            'numero_pratos' => 'required|integer|min:1|max:10',
            'forma_pagamento' => 'nullable|in:pix,debito,credito,dinheiro',
        ]);

        $venda = new VendaRestaurante();
        $venda->restaurante_id = $restaurante->id;
        $venda->numero_pratos = $request->numero_pratos;
        $venda->tipo_consumo = $request->tipo_consumo;
        $venda->data_venda = now();
        $venda->user_id = $user->id;
        $venda->finalizado = false;

        if ($request->tipo_cidadao === 'temporario') {
            $venda->tipo_cliente = 'temporario';
            $venda->cidadao_temporario_id = $request->cidadao_id;
            $venda->doacao = true;
            $venda->forma_pagamento = 'doacao';
            $venda->estudante = false;
        } else {
            $venda->tipo_cliente = 'cidadao';
            $venda->cidadao_id = $request->cidadao_id;
            $venda->forma_pagamento = $request->forma_pagamento;
            $venda->estudante = $request->has('estudante');
            $venda->doacao = false;
        }

        $venda->save();

        return redirect()->route('restaurante.atendente.vendas.index')
            ->with('success', 'Venda registrada com sucesso.');
    }

    public function destroy($id)
    {
        $venda = VendaRestaurante::findOrFail($id);

        if ($venda->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Você não pode excluir esta venda.');
        }

        $venda->delete();

        return redirect()->route('restaurante.atendente.vendas.index')->with('success', 'Venda excluída com sucesso.');
    }
}
