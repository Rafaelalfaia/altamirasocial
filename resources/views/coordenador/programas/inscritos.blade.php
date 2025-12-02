@extends('layouts.app')

@section('title', 'Inscrições - ' . $programa->nome)

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Título --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-green-800">Inscrições - {{ $programa->nome }}</h1>
    </div>

    {{-- Métricas do contexto atual (busca/região) --}}
    <div class="flex flex-wrap gap-2 items-center text-sm">
        <span class="px-2 py-1 rounded bg-gray-100">
            Total: <strong>{{ $metrics['total'] ?? 0 }}</strong>
        </span>
        <span class="px-2 py-1 rounded bg-green-100 text-green-800">
            Aprovados: <strong>{{ $metrics['aprovado'] ?? 0 }}</strong>
        </span>
        <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800">
            Pendentes: <strong>{{ $metrics['pendente'] ?? 0 }}</strong>
        </span>
        <span class="px-2 py-1 rounded bg-red-100 text-red-800">
            Reprovados: <strong>{{ $metrics['reprovado'] ?? 0 }}</strong>
        </span>
    </div>

    @php
        // Preserva filtros atuais (exceto status e paginação) para os botões
        $qs = request()->except('status', 'page');
    @endphp

    {{-- Botões de filtro de status --}}
    <div class="mb-4 space-x-2">
        <a href="{{ route('coordenador.programas.inscritos', array_merge(['programa' => $programa->id, 'status' => 'aprovado'], $qs)) }}"
           class="px-3 py-1 rounded {{ $statusSelecionado === 'aprovado' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }}">
            ✅ Aprovados ({{ $metrics['aprovado'] ?? 0 }})
        </a>
        <a href="{{ route('coordenador.programas.inscritos', array_merge(['programa' => $programa->id, 'status' => 'pendente'], $qs)) }}"
           class="px-3 py-1 rounded {{ $statusSelecionado === 'pendente' ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700' }}">
            ⏳ Pendentes ({{ $metrics['pendente'] ?? 0 }})
        </a>
        <a href="{{ route('coordenador.programas.inscritos', array_merge(['programa' => $programa->id, 'status' => 'reprovado'], $qs)) }}"
           class="px-3 py-1 rounded {{ $statusSelecionado === 'reprovado' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700' }}">
            ❌ Reprovados ({{ $metrics['reprovado'] ?? 0 }})
        </a>
    </div>

    {{-- Barra de filtros (GET) --}}
        <form method="GET" action="{{ route('coordenador.programas.inscritos', ['programa' => $programa->id]) }}"
        class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
        <input type="hidden" name="status" value="{{ $statusSelecionado }}">

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Buscar por nome</label>
            <input type="text" name="q" value="{{ request('q') }}"
                placeholder="Digite o nome do inscrito"
                class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Região</label>
            <select name="regiao" class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Todas</option>
                @isset($regioes)
                    @foreach($regioes as $r)
                        <option value="{{ $r }}" @selected(request('regiao') === $r)>{{ $r }}</option>
                    @endforeach
                @endisset
            </select>
        </div>

        {{-- <<< NOVO: filtro por Bairro (cruza com cadastro do cidadão) --}}
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Bairro</label>
            <select name="bairro_id" class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Todos</option>
                @foreach(($bairros ?? collect()) as $b)
                    <option value="{{ $b->id }}" @selected((int)request('bairro_id') === (int)$b->id)>{{ $b->nome }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Ordenar</label>
            <select name="ordem" class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="az" @selected(request('ordem','az')==='az')>Nome (A–Z)</option>
                <option value="za" @selected(request('ordem')==='za')>Nome (Z–A)</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Por página</label>
            <select name="per_page" class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                @foreach([10,15,25,50] as $n)
                    <option value="{{ $n }}" @selected((int)request('per_page',15)===$n)>{{ $n }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2 md:col-span-5">
            <button type="submit"
                    class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">
                Filtrar
            </button>
            <a href="{{ route('coordenador.programas.inscritos', ['programa' => $programa->id, 'status' => $statusSelecionado]) }}"
            class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">
                Limpar
            </a>
        </div>
    </form>


    {{-- AÇÃO EM MASSA --}}
    <form id="bulk-form" method="POST"
          action="{{ route('coordenador.programas.inscricoes.bulk-status', ['programa' => $programa->id]) }}"
          class="flex items-center gap-2 mb-3">
        @csrf

        <select name="status" id="bulkStatus" class="border rounded px-2 py-1 text-sm">
            <option value="aprovado">Aprovar selecionados</option>
            <option value="pendente" selected>Marcar como pendente</option>
            <option value="reprovado">Reprovar selecionados</option>
        </select>

        <button id="applyBtn" type="submit" disabled
                class="px-3 py-1 rounded text-white bg-green-700 opacity-50 cursor-not-allowed">
            Aplicar
        </button>

        <span id="selCount" class="ml-2 text-sm text-gray-600">Nenhum selecionado</span>
    </form>

    {{-- Tabela de Inscrições --}}
    <div class="bg-white shadow rounded p-4">
        @if($inscricoes->isEmpty())
            <p class="text-gray-600">Nenhum inscrito encontrado.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100 text-gray-700 font-semibold">
                        <tr>
                            <th class="px-4 py-2 w-10 text-center">
                                {{-- Selecionar todos desta página --}}
                                <input id="ck-all" type="checkbox" class="h-4 w-4">
                            </th>

                            <th class="px-4 py-2">Nome do Inscrito</th>

                            @if ($aceitaDependentes)
                                <th class="px-4 py-2">Responsável</th>
                                <th class="px-4 py-2">Parentesco</th>
                            @else
                                <th class="px-4 py-2">CPF</th>
                                <th class="px-4 py-2">Telefone</th>
                            @endif

                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Região</th>
                            <th class="px-4 py-2">Bairro</th>

                            <th class="px-4 py-2 text-right">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($inscricoes as $inscricao)
                        <tr class="border-b hover:bg-gray-50">
                            {{-- Checkbox da linha (pertence ao bulk-form) --}}
                            <td class="px-4 py-2 text-center">
                                <input type="checkbox"
                                       class="h-4 w-4 ck-insc"
                                       name="ids[]"
                                       value="{{ $inscricao->id }}"
                                       form="bulk-form">
                            </td>

                            {{-- Nome do inscrito (dependente OU cidadão) --}}
                            <td class="px-4 py-2 font-medium text-indigo-700">
                                {{ optional($inscricao->dependente)->nome ?? optional($inscricao->cidadao)->nome ?? '—' }}
                            </td>

                            @if ($aceitaDependentes)
                                {{-- Responsável --}}
                                <td class="px-4 py-2">{{ optional($inscricao->cidadao)->nome ?? '—' }}</td>
                                {{-- Parentesco --}}
                                <td class="px-4 py-2">{{ ucfirst(optional($inscricao->dependente)->grau_parentesco ?? '—') }}</td>
                            @else
                                {{-- CPF --}}
                                <td class="px-4 py-2">{{ optional($inscricao->cidadao)->cpf ?? '—' }}</td>
                                {{-- Telefone --}}
                                <td class="px-4 py-2">{{ optional($inscricao->cidadao)->telefone ?? '—' }}</td>
                            @endif

                            {{-- Status --}}
                            <td class="px-4 py-2">
                                <span class="inline-block px-2 py-1 text-xs rounded-full font-semibold
                                    @if($inscricao->status === 'aprovado') bg-green-100 text-green-700
                                    @elseif($inscricao->status === 'pendente') bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($inscricao->status) }}
                                </span>
                            </td>

                            {{-- Região --}}
                            <td class="px-4 py-2">{{ $inscricao->regiao ?? '—' }}</td>

                            {{-- Bairro --}}
                            <td class="px-4 py-2">
                                {{ $inscricao->bairro_nome
                                    ?? optional(optional($inscricao->cidadao)->getRelationValue('bairro'))->nome
                                    ?? (optional($inscricao->cidadao)->bairro ?? '—') }}
                            </td>

                            {{-- Ações individuais --}}
                            <td class="px-4 py-2 text-right space-x-2">
                                {{-- Ver --}}
                                <a href="{{ route('coordenador.programas.inscricoes.show', [$programa->id, $inscricao->id]) }}"
                                   class="text-blue-600 hover:underline text-sm">Ver</a>

                                {{-- Editar --}}
                                <a href="{{ route('coordenador.programas.inscricoes.edit', [$programa->id, $inscricao->id]) }}"
                                   class="text-indigo-700 hover:underline text-sm">Editar</a>

                                {{-- Excluir --}}
                                <form method="POST"
                                      action="{{ route('coordenador.programas.inscricoes.destroy', [$programa->id, $inscricao->id]) }}"
                                      class="inline"
                                      onsubmit="return confirm('Deseja EXCLUIR esta inscrição? Esta ação não pode ser desfeita.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-sm">Excluir</button>
                                </form>

                                {{-- Aprovar --}}
                                @if($inscricao->status !== 'aprovado')
                                    <form method="POST"
                                          action="{{ route('coordenador.programas.aprovar', [$programa->id, $inscricao->id]) }}"
                                          class="inline"
                                          onsubmit="return confirm('Deseja APROVAR esta inscrição?');">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:underline text-sm">Aprovar</button>
                                    </form>
                                @endif

                                {{-- Pendente --}}
                                @if($inscricao->status !== 'pendente')
                                    <form method="POST"
                                          action="{{ route('coordenador.programas.atualizar-inscricao', [$programa->id, $inscricao->id]) }}"
                                          class="inline"
                                          onsubmit="return confirm('Definir como PENDENTE?');">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="pendente">
                                        <button type="submit" class="text-yellow-600 hover:underline text-sm">Pendente</button>
                                    </form>
                                @endif

                                {{-- Reprovar --}}
                                @if($inscricao->status !== 'reprovado')
                                    <form method="POST"
                                          action="{{ route('coordenador.programas.reprovar', [$programa->id, $inscricao->id]) }}"
                                          class="inline"
                                          onsubmit="return confirm('Deseja REPROVAR esta inscrição?');">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:underline text-sm">Reprovar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginação (mantém filtros) --}}
            <div class="mt-4">
                {{ $inscricoes->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

{{-- JS nativo para contador/selecionar todos/ativar botão --}}
<script>
(function(){
  const formId   = 'bulk-form';
  const btnApply = document.getElementById('applyBtn');
  const lbl      = document.getElementById('selCount');
  const all      = document.getElementById('ck-all');

  function boxes(selector) {
    // Busca por todos os checkboxes ligados ao form, mesmo fora do DOM do <form>
    return document.querySelectorAll('input.ck-insc[form="'+formId+'"]' + (selector || ''));
  }

  function setBtnState(enabled) {
    btnApply.disabled = !enabled;
    btnApply.classList.toggle('opacity-50', !enabled);
    btnApply.classList.toggle('cursor-not-allowed', !enabled);
    btnApply.classList.toggle('bg-green-700', enabled);
  }

  function updateCount(){
    const n = boxes(':checked').length;
    if (lbl) lbl.textContent = n > 0 ? `${n} selecionado(s)` : 'Nenhum selecionado';
    setBtnState(n > 0);
  }

  if (all){
    all.addEventListener('change', function(){
      boxes('').forEach(cb => { cb.checked = all.checked; });
      updateCount();
    });
  }

  document.addEventListener('change', function(e){
    if (e.target && e.target.matches('input.ck-insc[form="'+formId+'"]')) {
      updateCount();
    }
  });

  document.addEventListener('DOMContentLoaded', updateCount);
})();
</script>
@endsection
