<?php

namespace App\Http\Controllers\Assistente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cidadao;
use App\Models\Acompanhamento;
use App\Models\ComposicaoFamiliar;
use Illuminate\Support\Facades\Auth;

class AcompanhamentoController extends Controller
{
    // Lista os últimos acompanhamentos (com filtro por cidadão)
    public function index(Request $request)
    {
        $query = Acompanhamento::with(['cidadao', 'assistente']);

        if ($request->filled('busca')) {
            $busca = trim($request->busca);
            $somenteDigitos = preg_replace('/\D+/', '', $busca);

            $query->whereHas('cidadao', function ($q) use ($busca, $somenteDigitos) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('cpf', 'like', "%{$busca}%");

                if ($somenteDigitos && strlen($somenteDigitos) >= 3) {
                    $q->orWhereRaw('REPLACE(REPLACE(REPLACE(cpf, ".", ""), "-", ""), " ", "") LIKE ?', ["%{$somenteDigitos}%"]);
                }
            });
        }

        $acompanhamentos = $query->latest()->paginate(10)->withQueryString();

        return view('assistente.acompanhamentos.index', compact('acompanhamentos'));
    }

    /**
     * Tela para selecionar cidadão:
     * - Lista TODOS os cidadãos com paginação (50 por página)
     * - Filtro por nome/CPF (aceita com e sem máscara)
     * - Envia mapa cidadao_id => acompanhamento_id (do assistente logado) p/ decidir ação na view
     */
    public function create(Request $request)
    {
        $perPage = 50;
        $search  = trim((string) $request->get('search', ''));

        $query = Cidadao::query()->select(['id','nome','cpf']);

        if ($search !== '') {
            $somenteDigitos = preg_replace('/\D+/', '', $search);

            $query->where(function ($q) use ($search, $somenteDigitos) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%");

                if ($somenteDigitos && strlen($somenteDigitos) >= 3) {
                    $q->orWhereRaw('REPLACE(REPLACE(REPLACE(cpf, ".", ""), "-", ""), " ", "") LIKE ?', ["%{$somenteDigitos}%"]);
                }
            });
        }

        $cidadaos = $query
            ->orderBy('nome')
            ->paginate($perPage)
            ->withQueryString(); // mantém ?search= na paginação

        // Mapa cidadao_id => acompanhamento_id do assistente logado (apenas p/ os 50 IDs da página)
        $acompPorCidadao = Acompanhamento::where('user_id', Auth::id())
            ->whereIn('cidadao_id', $cidadaos->pluck('id'))
            ->pluck('id', 'cidadao_id');

        return view('assistente.acompanhamentos.select_cidadao', [
            'cidadaos'        => $cidadaos,          // Paginator (->links funciona)
            'acompPorCidadao' => $acompPorCidadao,   // evita N+1
            'search'          => $search,
        ]);
    }

    // Formulário de novo acompanhamento
    public function form(Cidadao $cidadao)
    {
        return view('assistente.acompanhamentos.create', compact('cidadao'));
    }

    public function edit($id)
    {
        $acompanhamento = Acompanhamento::with(['cidadao', 'assistente'])->findOrFail($id);
        $cidadao = $acompanhamento->cidadao;

        return view('assistente.acompanhamentos.edit', compact('acompanhamento', 'cidadao'));
    }

    public function update(Request $request, Acompanhamento $acompanhamento)
    {
        $validated = $request->validate([
            'observacoes' => 'nullable|string',
        ]);

        $acompanhamento->update($validated);

        return redirect()
            ->route('assistente.acompanhamentos.index')
            ->with('success', 'Acompanhamento atualizado com sucesso.');
    }

    // Armazena um novo acompanhamento
    public function store(Request $request, Cidadao $cidadao)
    {
        $validated = $request->validate([
            // IDENTIFICAÇÃO
            'nome_unidade' => 'nullable|string|max:255',
            'nome_responsavel' => 'nullable|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'sexo' => 'nullable|in:Feminino,Masculino',
            'data_nascimento' => 'nullable|date',
            'naturalidade' => 'nullable|string|max:255',
            'cpf' => 'nullable|string|max:20',
            'nis' => 'nullable|string|max:20',
            'rg' => 'nullable|string|max:20',
            'orgao_emissor' => 'nullable|string|max:50',
            'data_emissao' => 'nullable|date',
            'titulo_eleitor' => 'nullable|string|max:20',
            'zona' => 'nullable|string|max:10',
            'secao' => 'nullable|string|max:10',
            'codigo_cadunico' => 'nullable|string|max:50',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:10',
            'bairro' => 'nullable|string|max:100',
            'ponto_referencia' => 'nullable|string|max:255',
            'estado_civil' => 'nullable|string|max:100',
            'whatsapp' => 'nullable|string|max:30',

            // QUESTIONÁRIO
            'cor' => 'nullable|string|max:50',
            'equipamentos_comunitarios' => 'nullable|array',
            'situacao_moradia' => 'nullable|string|max:100',
            'tempo_residencia' => 'nullable|string|max:50',
            'quantidade_comodos' => 'nullable|string|max:10',
            'tipo_construcao' => 'nullable|string|max:50',
            'energia' => 'nullable|string|max:50',
            'agua' => 'nullable|string|max:50',
            'esgoto' => 'nullable|string|max:50',
            'lixo' => 'nullable|string|max:50',
            'tipo_rua' => 'nullable|string|max:50',
            'possui_gravida' => 'nullable|boolean',
            'nome_gravida' => 'nullable|string|max:255',
            'possui_idoso' => 'nullable|boolean',
            'nome_idoso' => 'nullable|string|max:255',
            'situacao_profissional' => 'nullable|string|max:100',
            'possui_deficiencia' => 'nullable|boolean',
            'tipos_deficiencia' => 'nullable|array',
            'observacoes' => 'nullable|string',

            // COMPOSIÇÃO FAMILIAR
            'composicao' => 'nullable|array',
            'composicao.*.nome' => 'nullable|string|max:255',
            'composicao.*.data_nascimento' => 'nullable|date',
            'composicao.*.parentesco' => 'nullable|string|max:100',
            'composicao.*.escolaridade' => 'nullable|string|max:100',
            'composicao.*.beneficio' => 'nullable|string|max:100',
            'composicao.*.valor_beneficio' => 'nullable|numeric',
            'composicao.*.profissao' => 'nullable|string|max:100',
            'composicao.*.renda_bruta' => 'nullable|numeric',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['cidadao_id'] = $cidadao->id;
        $validated['data'] = now();

        $acompanhamento = Acompanhamento::create($validated);

        if (!empty($validated['composicao'])) {
            foreach ($validated['composicao'] as $membro) {
                if (!empty($membro['nome'])) {
                    $acompanhamento->composicaoFamiliar()->create([
                        'nome' => $membro['nome'],
                        'data_nascimento' => $membro['data_nascimento'] ?? null,
                        'parentesco' => $membro['parentesco'] ?? null,
                        'escolaridade' => $membro['escolaridade'] ?? null,
                        'beneficio' => $membro['beneficio'] ?? null,
                        'valor_beneficio' => $membro['valor_beneficio'] ?? null,
                        'profissao' => $membro['profissao'] ?? null,
                        'renda_bruta' => $membro['renda_bruta'] ?? null,
                    ]);
                }
            }
        }

        return redirect()
            ->route('assistente.acompanhamentos.show', $cidadao->id)
            ->with('success', 'Acompanhamento registrado com sucesso!');
    }

    // Exibe o histórico do cidadão
    public function show(Cidadao $cidadao)
    {
        $acompanhamentos = Acompanhamento::where('cidadao_id', $cidadao->id)
            ->with('assistente')
            ->orderByDesc('data')
            ->get();

        return view('assistente.acompanhamentos.show', compact('cidadao', 'acompanhamentos'));
    }

    // Exibe relatório completo
    public function relatorio(Acompanhamento $acompanhamento)
    {
        $acompanhamento->load('cidadao', 'assistente', 'composicaoFamiliar');

        return view('assistente.acompanhamentos.relatorio', compact('acompanhamento'));
    }

    // Excluir acompanhamento
    public function destroy(Acompanhamento $acompanhamento)
    {
        $acompanhamento->delete();

        return redirect()
            ->route('assistente.acompanhamentos.index')
            ->with('success', 'Acompanhamento excluído com sucesso.');
    }
}
