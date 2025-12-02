<?php

namespace App\Http\Controllers\Restaurante\Coordenador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendaRestaurante;
use App\Models\Cidadao;
use App\Models\CidadaoTemporario;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;


class VendaRestauranteController extends Controller
{
    // Lista as vendas do dia
   public function index()
    {
        $hoje = \Carbon\Carbon::today();

       $vendas = VendaRestaurante::with(['cidadao', 'cidadaoTemporario', 'user'])
        ->whereDate('data_venda', now()->toDateString())
        ->where('finalizado', false)
        ->orderBy('data_venda', 'desc')
        ->get();


        return view('restaurante.coordenador.vendas.index', compact('vendas'));
    }



    // Tela de criação de venda
    public function create()
    {
        // Busca e padroniza cidadãos normais
        $cidadaosNormais = \App\Models\Cidadao::select('id', 'nome', 'cpf')
            ->get()
            ->map(function ($c) {
                return (object) [
                    'id' => $c->id,
                    'nome' => $c->nome,
                    'cpf' => $c->cpf,
                    'tipo' => 'normal',
                ];
            });

        // Busca e padroniza cidadãos temporários
        $cidadaosTemporarios = \App\Models\CidadaoTemporario::where('status', 'ativo')
            ->select('id', 'nome', 'cpf')
            ->get()
            ->map(function ($c) {
                return (object) [
                    'id' => $c->id,
                    'nome' => $c->nome,
                    'cpf' => $c->cpf,
                    'tipo' => 'temporario',
                ];
            });

        // Junta tudo
        $cidadaos = $cidadaosNormais->concat($cidadaosTemporarios)->sortBy('nome')->values();

        return view('restaurante.coordenador.vendas.create', compact('cidadaos'));
    }




    // Armazena a venda
    public function store(Request $request)
    {
        $request->validate([
            'tipo_cidadao' => 'required|in:normal,temporario',
            'cidadao_id' => 'nullable|exists:cidadaos,id',
            'cidadao_temporario_id' => 'nullable|exists:cidadaos_temporarios,id',
            'tipo_consumo' => 'required|in:local,retirada',
            'numero_pratos' => 'required|integer|min:1|max:10',
            'forma_pagamento' => 'nullable|in:pix,debito,credito,dinheiro',
        ]);


        $venda = new VendaRestaurante();
        $venda->restaurante_id = Auth::id();
        $venda->tipo_consumo = $request->tipo_consumo;
        $venda->numero_pratos = $request->numero_pratos ?? 1;

        if ($request->tipo_cidadao === 'temporario') {
            $venda->tipo_cliente = 'temporario';
            $venda->cidadao_temporario_id = $request->cidadao_temporario_id;
            $venda->forma_pagamento = 'doacao';
            $venda->doacao = true;
            $venda->estudante = false;
        } else {
            $venda->tipo_cliente = 'cidadao';
            $venda->cidadao_id = $request->cidadao_id;
            $venda->forma_pagamento = $request->forma_pagamento;
            $venda->estudante = $request->has('estudante');
            $venda->doacao = false;
        }

        $venda->data_venda = now();
        $venda->save();

        return redirect()->route('restaurante.coordenador.vendas.index')
                        ->with('mensagem', 'Venda registrada com sucesso.');
    }





    // Finaliza o dia (apenas marca como encerrado)
    public function finalizarDia()
    {
        $hoje = \Carbon\Carbon::today();

        VendaRestaurante::whereDate('data_venda', $hoje)
            ->where('finalizado', false)
            ->update(['finalizado' => true]);

        return redirect()->back()->with('mensagem', 'Todas as vendas do dia foram finalizadas com sucesso.');
    }




    public function destroy($id)
    {
        $venda = VendaRestaurante::findOrFail($id);

        // Apenas se ainda não finalizado
        if ($venda->finalizado) {
            return redirect()->back()->with('mensagem', 'Venda já finalizada não pode ser excluída.');
        }

        $venda->delete();

        return redirect()->route('restaurante.coordenador.vendas.index')
            ->with('mensagem', 'Venda apagada com sucesso!');
    }

}
