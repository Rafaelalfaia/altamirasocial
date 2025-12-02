<?php

namespace App\Http\Controllers\Cidadao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Emergencia;

class EmergenciaController extends Controller
{
    /**
     * Exibe o formulário para solicitar atendimento emergencial.
     */
    public function form()
    {
        return view('cidadao.emergencia.form');
    }
    
    public function create()
    {
        return view('cidadao.emergencia.form');
    }


    /**
     * Armazena a solicitação de emergência e gera a sala.
     */
    public function store(Request $request)
    {
        $request->validate([
            'motivo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
        ]);
    
        $cidadao = Auth::user()->cidadao;
    
        if (!$cidadao) {
            return back()->withErrors('Seu perfil de cidadão não foi encontrado.');
        }
    
        $emergencia = Emergencia::create([
            'cidadao_id' => $cidadao->id,
            'user_id' => null,
            'motivo' => $request->motivo,
            'descricao' => $request->descricao,
            'sala' => 'semaps-emergencia-' . uniqid(),
            'status' => 'aberto',
        ]);
    
        return redirect()->route('cidadao.emergencia.chamada', ['sala' => $emergencia->sala]);
    }
    

    public function chamada($sala)
    {
        return view('cidadao.emergencia.chamada', compact('sala'));
    }



    /**
     * Exibe a sala de atendimento via Jitsi.
     */
    public function sala($sala)
    {
        return view('cidadao.emergencia.sala', compact('sala'));
    }
}
