<?php

namespace App\Http\Controllers\Assistente;

use App\Http\Controllers\Controller;
use App\Models\Emergencia;
use App\Models\ModoPlantao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EmergenciaAssistenteController extends Controller
{
    public function index()
    {
        if (! $this->emPlantao()) {
            return redirect()->route('assistente.dashboard')->with('error', 'Você não está em plantão no momento.');
        }

        $emergencias = Emergencia::where('status', 'aberto')->latest()->get();

        return view('assistente.emergencias.index', compact('emergencias'));
    }

    public function chamada($sala)
{
    if (! $this->emPlantao()) {
        return redirect()->route('assistente.dashboard')->with('error', 'Você não está em plantão no momento.');
    }

    $emergencia = Emergencia::where('sala', $sala)->firstOrFail();

    if (is_null($emergencia->user_id)) {
        $emergencia->user_id = Auth::id(); 
        $emergencia->save();
    }

    return view('assistente.emergencias.chamada', compact('sala'));
}


    public function encerrar($id)
    {
        $emergencia = Emergencia::findOrFail($id);

        if ($emergencia->status === 'encerrado') {
            return redirect()->back()->with('warning', 'Esta emergência já foi encerrada.');
        }

        $emergencia->status = 'encerrado';
        $emergencia->save();

        return redirect()->route('assistente.emergencias.index')->with('success', 'Atendimento encerrado com sucesso.');
    }

    // ✅ Visualiza o formulário de relatório
    public function formRelatar($id)
    {
        $emergencia = Emergencia::findOrFail($id);
        return view('assistente.emergencias.relatar', compact('emergencia'));
    }

    public function enviarRelatorio(Request $request, $id)
    {
        $request->validate([
            'conclusao' => 'required|string',
        ]);
    
        $emergencia = Emergencia::findOrFail($id);
        $emergencia->status = 'encerrado'; // coloque entre aspas!
        $emergencia->conclusao = $request->conclusao;
        $emergencia->save();
    
        return redirect()->route('assistente.dashboard')->with('success', 'Relatório enviado com sucesso.');
    }
    

    public function destroy($id)
    {
        $emergencia = Emergencia::findOrFail($id);
        $emergencia->delete();

        return redirect()->route('assistente.dashboard')->with('success', 'Ocorrência apagada com sucesso.');
    }

    private function emPlantao()
    {
        return ModoPlantao::where('user_id', Auth::id())->where('ativo', true)->exists();
    }
}
