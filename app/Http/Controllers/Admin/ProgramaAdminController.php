<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Programa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProgramaAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
        // Ajuste conforme suas policies/permissions:
        // $this->middleware('permission:programas.listar')->only(['index','show']);
        // $this->middleware('permission:programas.excluir')->only(['destroy']);
        $this->middleware('permission:programas.recomendar')->only('recomendar');
    }

    /**
     * Lista de Programas (Admin) com filtros e ordenação pública:
     * - recomendado DESC
     * - recomendacao_ordem ASC (0 sobe)
     * - created_at DESC
     */
    public function index(Request $request)
    {
        $busca   = trim((string) $request->input('busca', ''));
        $status  = (string) $request->input('status', '');
        $recOnly = (bool) $request->boolean('rec', false);

        $q = Programa::query();

        if ($busca !== '') {
            // escapa curingas do LIKE
            $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $busca) . '%';
            $q->where(function ($qq) use ($like) {
                $qq->where('nome', 'like', $like)
                   ->orWhere('descricao', 'like', $like);
            });
        }

        if ($status === 'ativado' || $status === 'desativado') {
            $q->where('status', $status);
        }

        if ($recOnly) {
            $q->where('recomendado', true);
        }

        $q->orderByDesc('recomendado')
          ->orderBy('recomendacao_ordem')    // ASC
          ->orderByDesc('created_at');

        $programas = $q->paginate(12)->withQueryString();

        return view('admin.programas.index', compact('programas'));
    }

    /**
     * Mostrar um programa (rota usa {id} no seu web.php).
     */
    public function show(string $id)
    {
        $programa = Programa::findOrFail($id);
        return view('admin.programas.show', compact('programa'));
    }

    /**
     * Recomendar / definir ordem de recomendação.
     * Rota usa {programa} => Route Model Binding.
     *
     * Regras:
     * - Se vier "ordem" sem "ativar": mantém recomendado=true.
     * - ativar=0 desliga e zera campos.
     * - ativar=1 liga, define recomendado_por e recomendado_em.
     */
    public function recomendar(Request $request, Programa $programa)
    {
        // ativa/desativa
        $ativar = $request->has('ativar')
            ? (bool) $request->input('ativar')
            : (bool) $programa->recomendado;

        $ordem = $request->filled('ordem')
            ? max(0, (int) $request->input('ordem'))
            : (int) ($programa->recomendacao_ordem ?? 0);

        if ($ativar) {
            $programa->recomendado        = true;
            $programa->recomendado_por    = auth()->id();
            $programa->recomendado_em     = now();
            $programa->recomendacao_ordem = $ordem;
        } else {
            $programa->recomendado        = false;
            $programa->recomendado_por    = null;
            $programa->recomendado_em     = null;
            $programa->recomendacao_ordem = 0;
        }

        $programa->save();

        return back()->with('success', $ativar ? 'Programa colocado em destaque.' : 'Destaque removido.');
    }

    /**
     * Excluir programa (rota usa {id} no seu web.php).
     */
    public function destroy(string $id)
    {
        $programa = Programa::findOrFail($id);
        $programa->delete();

        return redirect()
            ->route('admin.programas.index')
            ->with('success', 'Programa excluído com sucesso.');
    }
}
