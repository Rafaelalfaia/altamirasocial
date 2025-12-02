<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use App\Models\Emergencia;
use Illuminate\Http\Request;

class EmergenciaCoordenadorController extends Controller
{
    /**
     * Lista emergências das últimas 48 horas.
     */
    public function index()
    {
        $emergencias = Emergencia::with('cidadao')
            ->where('created_at', '>=', now()->subHours(48))
            ->orderByDesc('created_at')
            ->get();

        return view('coordenador.emergencias.index', compact('emergencias'));
    }

    /**
     * Mostra o histórico completo de emergências.
     */
    public function historico()
    {
        $emergencias = Emergencia::with('cidadao')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('coordenador.emergencias.historico', compact('emergencias'));
    }

    /**
     * Exibe detalhes de uma emergência específica.
     */
    public function show($id)
    {
        $emergencia = Emergencia::with(['cidadao', 'user'])->findOrFail($id);

        return view('coordenador.emergencias.show', compact('emergencia'));
    }

    /**
     * Exclui uma ocorrência emergencial.
     */
    public function destroy($id)
    {
        $emergencia = Emergencia::findOrFail($id);
        $emergencia->delete();

        return redirect()
            ->route('coordenador.dashboard')
            ->with('success', 'Ocorrência emergencial excluída com sucesso.');
    }
}
