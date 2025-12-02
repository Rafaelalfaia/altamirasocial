<?php

namespace App\Http\Controllers\Assistente;

use App\Http\Controllers\Controller;
use App\Models\Cidadao;
use Illuminate\Http\Request;
use App\Models\Acompanhamento;
use App\Models\Evolucao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class EvolucaoController extends Controller
{
    // Lista de evoluções de um acompanhamento (somente do assistente logado)
    public function index(Acompanhamento $acompanhamento)
    {
        $evolucoes = $acompanhamento->evolucoes()
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('assistente.evolucoes.index', compact('acompanhamento', 'evolucoes'));
    }

    // Formulário de nova evolução
    public function create(Acompanhamento $acompanhamento)
    {
        return view('assistente.evolucoes.create', compact('acompanhamento'));
    }

    public function selecionarCidadao(Request $request)
    {
        $perPage = 50;
        $search  = trim((string) $request->get('search', ''));

        $query = Cidadao::query()->select(['id','nome','cpf']);

        if ($search !== '') {
            // se tiver CPF com pontuação, normaliza p/ buscar só dígitos também
            $somenteDigitos = preg_replace('/\D+/', '', $search);

            $query->where(function($q) use ($search, $somenteDigitos) {
                $q->where('nome', 'like', "%{$search}%")
                ->orWhere('cpf', 'like', "%{$search}%");

                // Busca adicional por dígitos do CPF sem máscara
                if ($somenteDigitos && strlen($somenteDigitos) >= 3) {
                    $q->orWhereRaw('REPLACE(REPLACE(REPLACE(cpf, ".", ""), "-", ""), " ", "") LIKE ?', ["%{$somenteDigitos}%"]);
                }
            });
        }

        $cidadaos = $query
            ->orderBy('nome')
            ->paginate($perPage)
            ->withQueryString(); // mantém ?search= na paginação

        // Mapa cidadao_id => acompanhamento_id (somente do assistente logado) para os IDs desta página
        $acompPorCidadao = Acompanhamento::where('user_id', Auth::id())
            ->whereIn('cidadao_id', $cidadaos->pluck('id'))
            ->pluck('id', 'cidadao_id'); // [cidadao_id => acompanhamento_id]

        return view('assistente.acompanhamentos.select_cidadao', [
            'cidadaos'        => $cidadaos,
            'acompPorCidadao' => $acompPorCidadao,
            'search'          => $search,
        ]);
    }

    // Armazenar nova evolução
    public function store(Request $request, Acompanhamento $acompanhamento)
    {
        $dados = $request->validate([
            'titulo' => 'required|string|max:255',
            'resumo' => 'nullable|string',
            'tipo_atendimento' => 'required|string',
            'outro_local' => 'nullable|string|max:255',
            'caso_emergencial' => [
                'nullable',
                'string',
                Rule::in([
                    'violencia_domestica',
                    'violencia_sexual',
                    'problemas_saude',
                    'pobreza_extrema',
                    'tentativa_homicidio',
                ])
            ],
            'descricao_emergencial' => 'nullable|string|max:1000',
        ]);

        $dados['local_atendimento'] = $dados['tipo_atendimento'] === 'Outro'
            ? $dados['outro_local']
            : $dados['tipo_atendimento'];

        unset($dados['outro_local']);
        $dados['user_id'] = Auth::id();

        $acompanhamento->evolucoes()->create($dados);

        return redirect()
            ->route('assistente.evolucoes.index', $acompanhamento)
            ->with('success', 'Evolução registrada com sucesso.');
    }

    // Formulário de edição
    public function edit($acompanhamentoId, $evolucaoId)
    {
        $acompanhamento = Acompanhamento::with('cidadao')->findOrFail($acompanhamentoId);
        $evolucao = Evolucao::where('id', $evolucaoId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('assistente.evolucoes.edit', compact('acompanhamento', 'evolucao'));
    }

    // Atualização de evolução
    public function update(Request $request, $acompanhamentoId, $evolucaoId)
    {
        $evolucao = Evolucao::where('id', $evolucaoId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $dados = $request->validate([
            'titulo' => 'required|string|max:255',
            'resumo' => 'nullable|string',
            'tipo_atendimento' => 'required|string',
            'outro_local' => 'nullable|string|max:255',
            'caso_emergencial' => [
                'nullable',
                'string',
                Rule::in([
                    'violencia_domestica',
                    'violencia_sexual',
                    'problemas_saude',
                    'pobreza_extrema',
                    'tentativa_homicidio',
                ])
            ],
            'descricao_emergencial' => 'nullable|string|max:1000',
        ]);

        $dados['local_atendimento'] = $dados['tipo_atendimento'] === 'Outro'
            ? $dados['outro_local']
            : $dados['tipo_atendimento'];

        unset($dados['outro_local']);

        $evolucao->update($dados);

        return redirect()
            ->route('assistente.evolucoes.index', $acompanhamentoId)
            ->with('success', 'Evolução atualizada com sucesso!');
    }

    // Iniciar novo acompanhamento para um cidadão
    public function iniciar($cidadaoId)
    {
        $acompanhamento = Acompanhamento::where('cidadao_id', $cidadaoId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$acompanhamento) {
            $acompanhamento = Acompanhamento::create([
                'cidadao_id'    => $cidadaoId,
                'user_id'       => auth()->id(),
            ]);
        }

        return redirect()->route('assistente.evolucoes.index', $acompanhamento->id);
    }



}
