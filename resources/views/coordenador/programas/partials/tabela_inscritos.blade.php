{{-- FILTROS DE STATUS --}}
<div class="mb-4 space-x-2">
    <a href="{{ route('coordenador.programas.inscritos', ['programa' => $programa->id, 'status' => 'aprovado']) }}"
       class="px-3 py-1 rounded {{ ($statusSelecionado ?? null) === 'aprovado' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700' }}">
        ✅ Aprovados
    </a>
    <a href="{{ route('coordenador.programas.inscritos', ['programa' => $programa->id, 'status' => 'pendente']) }}"
       class="px-3 py-1 rounded {{ ($statusSelecionado ?? null) === 'pendente' ? 'bg-yellow-400 text-white' : 'bg-gray-200 text-gray-700' }}">
        ⏳ Pendentes
    </a>
    <a href="{{ route('coordenador.programas.inscritos', ['programa' => $programa->id, 'status' => 'reprovado']) }}"
       class="px-3 py-1 rounded {{ ($statusSelecionado ?? null) === 'reprovado' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700' }}">
        ❌ Reprovados
    </a>
</div>

{{-- AÇÃO EM MASSA (sem Alpine) --}}
<form id="bulk-form" method="POST"
      action="{{ route('coordenador.programas.inscricoes.bulk-status', ['programa' => $programa->id]) }}"
      class="flex items-center gap-2 mb-3">
    @csrf
    <select name="status" class="border rounded px-2 py-1 text-sm">
        <option value="aprovado">Aprovar selecionados</option>
        <option value="pendente">Marcar como pendente</option>
        <option value="reprovado">Reprovar selecionados</option>
    </select>

    <button id="applyBtn" type="submit" disabled
            class="px-3 py-1 rounded text-white bg-green-700 opacity-50 cursor-not-allowed">
        Aplicar
    </button>

    <span id="selCount" class="ml-2 text-sm text-gray-600">Nenhum selecionado</span>
</form>

{{-- TABELA --}}
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
                        <input type="checkbox" class="h-4 w-4" onclick="toggleAll(this)">
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
                                   form="bulk-form"
                                   onchange="updateCount()">
                        </td>

                        {{-- Nome do inscrito --}}
                        <td class="px-4 py-2 font-medium text-indigo-700">
                            {{ optional($inscricao->dependente)->nome ?? optional($inscricao->cidadao)->nome ?? '—' }}
                        </td>

                        @if ($aceitaDependentes)
                            <td class="px-4 py-2">{{ optional($inscricao->cidadao)->nome ?? '—' }}</td>
                            <td class="px-4 py-2">{{ ucfirst(optional($inscricao->dependente)->grau_parentesco ?? '—') }}</td>
                        @else
                            <td class="px-4 py-2">{{ optional($inscricao->cidadao)->cpf ?? '—' }}</td>
                            <td class="px-4 py-2">{{ optional($inscricao->cidadao)->telefone ?? '—' }}</td>
                        @endif

                        <td class="px-4 py-2">
                            <span class="inline-block px-2 py-1 text-xs rounded-full font-semibold
                                @if($inscricao->status === 'aprovado') bg-green-100 text-green-700
                                @elseif($inscricao->status === 'pendente') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($inscricao->status) }}
                            </span>
                        </td>

                        <td class="px-4 py-2">{{ $inscricao->regiao ?? '—' }}</td>

                        {{-- Ações individuais (mesmo esquema) --}}
                        <td class="px-4 py-2 text-right space-x-2">
                            <a href="{{ route('coordenador.programas.inscricoes.show', [$programa->id, $inscricao->id]) }}"
                               class="text-blue-600 hover:underline text-sm">Ver</a>

                            <a href="{{ route('coordenador.programas.inscricoes.edit', [$programa->id, $inscricao->id]) }}"
                               class="text-indigo-700 hover:underline text-sm">Editar</a>

                            <form method="POST"
                                  action="{{ route('coordenador.programas.inscricoes.destroy', [$programa->id, $inscricao->id]) }}"
                                  class="inline"
                                  onsubmit="return confirm('Deseja EXCLUIR esta inscrição? Esta ação não pode ser desfeita.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">Excluir</button>
                            </form>

                            @if($inscricao->status !== 'aprovado')
                                <form method="POST"
                                      action="{{ route('coordenador.programas.aprovar', [$programa->id, $inscricao->id]) }}"
                                      class="inline"
                                      onsubmit="return confirm('Deseja APROVAR esta inscrição?');">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:underline text-sm">Aprovar</button>
                                </form>
                            @endif

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

{{-- JS nativo para contador/selecionar todos --}}
<script>
function updateCount() {
    const n = document.querySelectorAll('.ck-insc:checked').length;
    const btn = document.getElementById('applyBtn');
    const lbl = document.getElementById('selCount');
    if (!btn || !lbl) return;
    btn.disabled = n === 0;
    btn.classList.toggle('opacity-50', n === 0);
    btn.classList.toggle('cursor-not-allowed', n === 0);
    lbl.textContent = n > 0 ? `${n} selecionado(s)` : 'Nenhum selecionado';
}
function toggleAll(master) {
    document.querySelectorAll('.ck-insc').forEach(cb => { cb.checked = master.checked; });
    updateCount();
}
document.addEventListener('DOMContentLoaded', updateCount);

