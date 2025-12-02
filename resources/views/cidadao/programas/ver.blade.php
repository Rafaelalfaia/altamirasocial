@extends('layouts.app')

@section('title', 'Detalhes do Programa')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Route;

    // ==== Imagens seguras (storage público) ====
    $temCapa = $programa->foto_capa && Storage::disk('public')->exists($programa->foto_capa);
    $temLogo = $programa->foto_perfil && Storage::disk('public')->exists($programa->foto_perfil);

    // ==== Normalização de regiões (json / array / csv / string) ====
    $rawReg = $programa->regioes ?? [];
    if (is_string($rawReg)) {
        $json = json_decode($rawReg, true);
        $arrReg = (json_last_error() === JSON_ERROR_NONE && is_array($json))
            ? $json
            : preg_split('/,|;|\|/', $rawReg);
    } elseif (is_array($rawReg)) {
        $arrReg = $rawReg;
    } else {
        $arrReg = [];
    }
    $regioes = array_values(array_filter(array_map(fn($v)=> trim((string)$v), $arrReg)));

    // ==== Contexto de inscrições do cidadão neste programa ====
    $cidadaoId = optional(auth()->user()->cidadao)->id;

    // Todas as inscrições deste cidadão neste programa (lazy ok)
    $inscricoesDoCidadao = $cidadaoId
        ? $programa->inscricoes()->where('cidadao_id', $cidadaoId)->get()
        : collect();

    // Titular (sem dependente)
    $inscricaoTitular = $inscricoesDoCidadao->firstWhere('dependente_id', null);

    $statusStyles = [
        'pendente'  => 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-200',
        'aprovado'  => 'bg-green-100 text-green-800 ring-1 ring-green-200',
        'reprovado' => 'bg-red-100 text-red-800 ring-1 ring-red-200',
    ];
    $statusKeyTitular = $inscricaoTitular ? strtolower($inscricaoTitular->status) : null;

    // Mapa: dependente_id => status (minúsculo)
    $statusPorDependente = $inscricoesDoCidadao
        ->whereNotNull('dependente_id')
        ->mapWithKeys(fn($i) => [$i->dependente_id => strtolower($i->status)]);

    // Dependentes já inscritos (ids) e disponíveis
    $depInscritosIds = $inscricoesDoCidadao->pluck('dependente_id')->filter()->unique()->values();
    $dependentesDisponiveis = collect($dependentes ?? [])->sortBy('nome')->values(); // controller deve passar $dependentes do cidadão

    // Há pelo menos 1 dependente ainda NÃO inscrito?
    $haDependenteLivre = $programa->aceita_menores
        && $dependentesDisponiveis->pluck('id')->diff($depInscritosIds)->isNotEmpty();

    // Mostrar formulário? (titular ainda não inscrito OU existe dependente livre OU precisa escolher região)
    $podeInscreverTitular     = $cidadaoId && !$inscricaoTitular;
    $podeInscreverDependentes = $haDependenteLivre;
    $deveMostrarFormulario    = $podeInscreverTitular || $podeInscreverDependentes || count($regioes) > 0;

    // Bloqueio (se controller enviou)
    $bloquear = $bloquearInscricao ?? false;
@endphp

<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-2xl overflow-hidden mt-6">
  {{-- Header com capa (opcional) --}}
  <div class="relative h-44 sm:h-56 md:h-64 w-full bg-white">
    @if($temCapa)
      <img src="{{ asset('storage/' . $programa->foto_capa) }}"
           alt="Capa do programa {{ $programa->nome }}"
           class="w-full h-full object-cover">
    @endif

    {{-- Logo central sobreposta (se houver); senão, mostra o nome --}}
    <div class="absolute inset-0 flex items-center justify-center">
      @if($temLogo)
        <img src="{{ asset('storage/' . $programa->foto_perfil) }}"
             alt="Logo do programa {{ $programa->nome }}"
             class="w-24 h-24 sm:w-28 sm:h-28 rounded-full border-4 border-white shadow-lg object-contain bg-white">
      @else
        <div class="px-4 py-2 rounded-full bg-white/90 border text-center shadow">
          <span class="text-base sm:text-lg font-bold text-indigo-700">{{ $programa->nome }}</span>
        </div>
      @endif
    </div>
  </div>

  {{-- Mensagens --}}
  @if($mensagem)
    <div class="mx-6 mt-4 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded text-sm leading-relaxed">
      {!! $mensagem !!}
    </div>
  @endif
  @if(session('mensagem'))
    <div class="mx-6 mt-4 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded text-sm leading-relaxed">
      {!! session('mensagem') !!}
    </div>
  @endif
  @if(session('success'))
    <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded text-sm leading-relaxed">
      {!! session('success') !!}
    </div>
  @endif
  @if ($errors->any())
    <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded text-sm">
      <ul class="list-disc ml-4">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Conteúdo principal --}}
  <div class="p-6">
    <div class="text-center">
      <h2 class="mt-2 text-2xl font-bold text-gray-900">{{ $programa->nome }}</h2>
      @if(!empty($programa->publico_alvo))
        <p class="text-sm text-gray-500">{{ $programa->publico_alvo }}</p>
      @endif>
    </div>

    {{-- Descrição --}}
    @if(!empty($programa->descricao))
      <div class="mt-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-2">Descrição</h3>
        <div class="prose prose-sm max-w-none text-gray-700">
          {!! nl2br(e($programa->descricao)) !!}
        </div>
      </div>
    @endif

    {{-- Status do TITULAR (se houver) --}}
    @if($inscricaoTitular)
      <div class="mt-6 flex items-center justify-between gap-3 flex-wrap">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-semibold {{ $statusStyles[$statusKeyTitular] ?? 'bg-gray-100 text-gray-700 ring-1 ring-gray-200' }}">
          <span>Status do titular:</span> <span class="capitalize">{{ $inscricaoTitular->status }}</span>
        </span>
      </div>
    @endif

    {{-- Formulário (permanece visível se ainda houver algo a inscrever) --}}
    @if($deveMostrarFormulario && !$bloquear)
      <form action="{{ route('cidadao.programa.inscrever', $programa) }}" method="POST" class="mt-6 space-y-4" novalidate>
        @csrf

        {{-- Região --}}
        @if(count($regioes))
          <div>
            <label for="regiao" class="block text-sm font-semibold text-gray-700 mb-1">
              De qual região você é?
            </label>
            <select name="regiao" id="regiao" required
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
              <option value="">Selecione sua região</option>
              @foreach ($regioes as $regiao)
                <option value="{{ $regiao }}" @selected(old('regiao')===$regiao)>{{ $regiao }}</option>
              @endforeach
            </select>
            @error('regiao')
              <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
          </div>
        @endif

        {{-- Inscrever TITULAR (se ainda não inscrito) --}}
        @if($podeInscreverTitular)
          <label class="flex items-center gap-2">
            <input type="checkbox" name="inscrever_titular" value="1" class="h-4 w-4">
            <span>Inscrever meu nome (titular)</span>
          </label>
        @endif

        {{-- Multi-seleção de DEPENDENTES (se o programa aceitar) --}}
        @if ($programa->aceita_menores)
          <div class="border rounded p-3">
            <div class="flex items-center justify-between gap-3 flex-wrap">
              <div class="font-semibold">Selecionar dependentes</div>

              <div class="flex items-center gap-2">
                @if($dependentesDisponiveis->isNotEmpty())
                  <label class="text-sm text-indigo-700 cursor-pointer select-none">
                    <input id="ck-all-deps" type="checkbox" class="align-middle mr-1"> Selecionar todos
                  </label>
                @endif

                {{-- Botão "Cadastrar dependente" SEMPRE visível quando aceita_menores --}}
                @if(Route::has('cidadao.dependentes.create'))
                  <a href="{{ route('dependentes.create') }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-white bg-green-600 hover:bg-green-700 text-sm">
                    ➕ Cadastrar dependente
                  </a>
                @elseif(Route::has('dependentes.create'))
                  <a href="{{ route('dependentes.create') }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-white bg-green-600 hover:bg-green-700 text-sm">
                    ➕ Cadastrar dependente
                  </a>
                @else
                  <a href="{{ url('/dependentes/create') }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-white bg-green-600 hover:bg-green-700 text-sm">
                    ➕ Cadastrar dependente
                  </a>
                @endif


              </div>
            </div>

            @forelse($dependentesDisponiveis as $dep)
              @php
                $depJaInscrito = $statusPorDependente->has($dep->id);
                $statusDep     = $depJaInscrito ? $statusPorDependente[$dep->id] : null;
              @endphp

              <label class="flex items-center gap-2 py-1">
                <input type="checkbox"
                       name="dependentes[]"
                       value="{{ $dep->id }}"
                       class="h-4 w-4 ck-dep"
                       @disabled($depJaInscrito)>

                <span>
                  {{ $dep->nome }}
                  @if($dep->grau_parentesco)
                    — <span class="text-gray-500">{{ $dep->grau_parentesco }}</span>
                  @endif

                  @if($depJaInscrito)
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-700">
                      já inscrito
                    </span>
                    <span class="ml-1 inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded-full
                                {{ $statusStyles[$statusDep] ?? 'bg-gray-100 text-gray-700 ring-1 ring-gray-200' }}">
                      <span class="hidden sm:inline">status:</span>
                      <span class="capitalize">{{ $statusDep }}</span>
                    </span>
                  @endif
                </span>
              </label>

            @empty
              <p class="text-sm text-gray-600">Você ainda não cadastrou dependentes.</p>
              {{-- Mantido também aqui como fallback --}}
              @if(Route::has('cidadao.dependentes.create'))
                <a href="{{ route('cidadao.dependentes.create') }}" class="text-indigo-600 hover:underline text-sm">
                  Cadastrar dependente
                </a>
              @elseif(Route::has('dependentes.create'))
                <a href="{{ route('dependentes.create') }}" class="text-indigo-600 hover:underline text-sm">
                  Cadastrar dependente
                </a>
              @else
                <a href="{{ url('/dependentes/create') }}" class="text-indigo-600 hover:underline text-sm">
                  Cadastrar dependente
                </a>
              @endif
            @endforelse

            <div class="mt-2 text-xs text-gray-500">
              <span id="depCount">Nenhum dependente selecionado</span>
            </div>
          </div>
        @endif

        <button type="submit"
          class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg transition">
          Confirmar inscrição
        </button>
      </form>
    @else
      {{-- Nada a inscrever: titular já inscrito e não há dependentes livres, ou formulário bloqueado --}}
      <div class="mt-6 p-4 rounded bg-gray-50 text-gray-700 border border-gray-200 text-sm">
        @if($bloquear)
          Para se inscrever, complete os dados obrigatórios do titular.
        @else
          Você já realizou todas as inscrições possíveis neste programa.
        @endif
      </div>
    @endif

    {{-- Ações secundárias --}}
    <div class="mt-6 flex items-center justify-between">
      <a href="{{ url()->previous() }}" class="text-sm text-gray-500 hover:text-gray-700 hover:underline">Voltar</a>
    </div>
  </div>
</div>

{{-- JS nativo: selecionar todos + contador --}}
<script>
document.addEventListener('DOMContentLoaded', function(){
  const all = document.getElementById('ck-all-deps');
  const countLbl = document.getElementById('depCount');

  function boxes() {
    return Array.from(document.querySelectorAll('input.ck-dep:not(:disabled)'));
  }
  function updateCount(){
    const n = document.querySelectorAll('input.ck-dep:checked').length;
    if (countLbl) countLbl.textContent = n > 0 ? `${n} dependente(s) selecionado(s)` : 'Nenhum dependente selecionado';
  }
  if (all){
    all.addEventListener('change', function(){
      boxes().forEach(cb => cb.checked = all.checked);
      updateCount();
    });
  }
  document.addEventListener('change', function(e){
    if (e.target && e.target.classList && e.target.classList.contains('ck-dep')) {
      updateCount();
    }
  });
  updateCount();
});
</script>
@endsection
