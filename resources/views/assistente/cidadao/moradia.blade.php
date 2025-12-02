@extends('layouts.app')

@section('title', 'Dados de Moradia (Assistente)')

@section('content')
@php
  // Rótulo do bairro atual (igual ao cidadão)
  $bairroAtualLabel = '';
  if (!empty($cidadao?->bairro)) {
      $uf = optional(optional($cidadao->bairro->cidade)->estado)->sigla;
      $bairroAtualLabel = $cidadao->bairro->nome.' — '.$cidadao->bairro->cidade->nome.($uf ? '/'.$uf : '');
  }

  $tm = old('tipo_moradia', $cidadao->tipo_moradia ?? '');
  $opcoesMoradia = ['Própria','Alugada','Cedida/Emprestada','Invasão','Outras'];

  $tr = old('tempo_reside', $cidadao->tempo_reside ?? '');
  $tc = old('tipo_construcao', $cidadao->tipo_construcao ?? '');
  $tv = old('tipo_via', $cidadao->tipo_via ?? '');
  $aa = old('abastecimento_agua', $cidadao->abastecimento_agua ?? '');
  $et = old('energia_tipo', $cidadao->energia_tipo ?? '');
  $pa = old('possui_animais', $cidadao->possui_animais ?? false);
@endphp

<div class="max-w-5xl mx-auto">
  {{-- Passos do assistente --}}
  @includeWhen(true, 'assistente.cidadao.partials._steps', ['cidadao'=>$cidadao, 'atual'=>'moradia'])

  {{-- Alerts/Errors padrão --}}
  @if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl p-3 mb-4">
      <ul class="list-disc pl-4 space-y-1">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
    </div>
  @endif
  @if (session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl p-3 mb-4">
      {{ session('success') }}
    </div>
  @endif

  <form id="form-moradia"
        action="{{ route('assistente.cidadao.moradia.salvar', $cidadao->id) }}"
        method="POST"
        class="rounded-2xl bg-white/90 backdrop-blur p-4 sm:p-6 shadow-sm ring-1 ring-gray-100 space-y-6">
    @csrf

    <h1 class="text-lg sm:text-xl font-semibold text-indigo-700">2. Moradia</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
      {{-- CEP --}}
      <div>
        <label class="text-xs font-medium text-gray-700">CEP</label>
        <input type="text" name="cep" value="{{ old('cep', $cidadao->cep ?? '') }}"
               class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
      </div>

      {{-- Número --}}
      <div>
        <label class="text-xs font-medium text-gray-700">Número</label>
        <input type="text" name="numero" value="{{ old('numero', $cidadao->numero ?? '') }}"
               class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
      </div>

      {{-- Rua --}}
      <div class="sm:col-span-2">
        <label class="text-xs font-medium text-gray-700">Rua / Logradouro</label>
        <input type="text" name="rua" value="{{ old('rua', $cidadao->rua ?? '') }}"
               class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
      </div>

      {{-- Complemento --}}
      <div>
        <label class="text-xs font-medium text-gray-700">Complemento</label>
        <input type="text" name="complemento" value="{{ old('complemento', $cidadao->complemento ?? '') }}"
               class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
      </div>

      {{-- Bairro (com datalist + hidden bairro_id) --}}
      <div class="sm:col-span-2">
        <label class="text-xs font-medium text-gray-700">Bairro (selecione um da lista)</label>
        <input id="bairro_search"
               class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200"
               placeholder="Digite para pesquisar o bairro e selecione na lista..."
               list="lista_bairros"
               autocomplete="off"
               value="{{ old('bairro_search', $bairroAtualLabel) }}">

        <datalist id="lista_bairros">
          @foreach($bairros as $b)
            @php
              $uf = optional(optional($b->cidade)->estado)->sigla;
              $rotulo = $b->nome.' — '.$b->cidade->nome.($uf?'/'.$uf:'');
            @endphp
            <option data-id="{{ $b->id }}" value="{{ $rotulo }}"></option>
          @endforeach
        </datalist>

        <input type="hidden" name="bairro_id" id="bairro_id" value="{{ old('bairro_id', $cidadao->bairro_id) }}">
        <p id="bairro_help" class="text-xs text-gray-500 mt-1">Apenas bairros cadastrados pelo Coordenador são aceitos.</p>
        <p id="bairro_error" class="hidden text-xs text-red-600 mt-1">Selecione um bairro válido da lista.</p>
      </div>
    </div>

    <hr class="border-gray-100">

    {{-- Situação de Moradia (rádios iguais ao cidadão) --}}
    <div class="space-y-2">
      <span class="text-xs font-medium text-gray-700">Qual a situação de moradia atual?</span>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
        @foreach($opcoesMoradia as $op)
          <label class="inline-flex items-center gap-2 bg-white border rounded-lg px-3 py-2">
            <input type="radio" name="tipo_moradia" value="{{ $op }}" {{ $tm===$op ? 'checked' : '' }}
                   class="text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-gray-800">{{ $op }}</span>
          </label>
        @endforeach
      </div>
    </div>

    {{-- Pontos de referência --}}
    <div class="space-y-1">
      <label class="text-xs font-medium text-gray-700">Pontos de Referência</label>
      <input type="text" name="pontos_referencia" value="{{ old('pontos_referencia', $cidadao->pontos_referencia ?? '') }}"
             class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
    </div>

    {{-- Seleções (iguais ao cidadão) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
      <div>
        <label class="text-xs font-medium text-gray-700">Tempo que reside</label>
        <select name="tempo_reside" class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
          <option value="">Selecione</option>
          @foreach (['<1 ano','1–3 anos','4–6 anos','7–10 anos','>10 anos'] as $op)
            <option value="{{ $op }}" {{ $tr===$op?'selected':'' }}>{{ $op }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="text-xs font-medium text-gray-700">Qtde. de Cômodos</label>
        <input type="number" name="qtde_comodos" min="0" max="50"
               value="{{ old('qtde_comodos', $cidadao->qtde_comodos ?? '') }}"
               class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
      </div>

      <div>
        <label class="text-xs font-medium text-gray-700">Tipo de Construção</label>
        <select name="tipo_construcao" class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
          <option value="">Selecione</option>
          @foreach (['Alvenaria','Madeira','Mista','Barro','Outros'] as $op)
            <option value="{{ $op }}" {{ $tc===$op?'selected':'' }}>{{ $op }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="text-xs font-medium text-gray-700">Tipo de Via</label>
        <select name="tipo_via" class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
          <option value="">Selecione</option>
          @foreach (['Asfalto','Bloquete','Piçarra','Terra','Outros'] as $op)
            <option value="{{ $op }}" {{ $tv===$op?'selected':'' }}>{{ $op }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="text-xs font-medium text-gray-700">Abastecimento de Água</label>
        <select name="abastecimento_agua" class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
          <option value="">Selecione</option>
          @foreach (['Rede geral de distribuição','Poço','Fonte/Nascente','Carro pipa','Rio/Igarapé','Outro'] as $op)
            <option value="{{ $op }}" {{ $aa===$op?'selected':'' }}>{{ $op }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="text-xs font-medium text-gray-700">Tipo de Energia</label>
        <select name="energia_tipo" class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
          <option value="">Selecione</option>
          @foreach (['Medidor próprio','Improvisado/Gambiarra','Não possui','Outros'] as $op)
            <option value="{{ $op }}" {{ $et===$op?'selected':'' }}>{{ $op }}</option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- Utilidades (booleans) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
      <label class="flex items-center gap-2">
        <input type="hidden" name="tem_esgoto" value="0">
        <input type="checkbox" name="tem_esgoto" value="1" {{ old('tem_esgoto', $cidadao->tem_esgoto ?? false) ? 'checked' : '' }}
               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="text-sm text-gray-800">Esgoto</span>
      </label>

      <label class="flex items-center gap-2">
        <input type="hidden" name="tem_agua_encanada" value="0">
        <input type="checkbox" name="tem_agua_encanada" value="1" {{ old('tem_agua_encanada', $cidadao->tem_agua_encanada ?? false) ? 'checked' : '' }}
               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="text-sm text-gray-800">Água encanada</span>
      </label>

      <label class="flex items-center gap-2">
        <input type="hidden" name="tem_coleta_lixo" value="0">
        <input type="checkbox" name="tem_coleta_lixo" value="1" {{ old('tem_coleta_lixo', $cidadao->tem_coleta_lixo ?? false) ? 'checked' : '' }}
               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="text-sm text-gray-800">Coleta de lixo</span>
      </label>

      <label class="flex items-center gap-2">
        <input type="hidden" name="tem_energia" value="0">
        <input type="checkbox" name="tem_energia" value="1" {{ old('tem_energia', $cidadao->tem_energia ?? false) ? 'checked' : '' }}
               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="text-sm text-gray-800">Energia</span>
      </label>
    </div>

    {{-- Animais (igual ao cidadão: só Sim/Não) --}}
    <div class="space-y-1">
      <label class="text-xs font-medium text-gray-700">Possui animais?</label>
      <select name="possui_animais" class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        <option value="0" {{ !$pa ? 'selected':'' }}>Não</option>
        <option value="1" {{  $pa ? 'selected':'' }}>Sim</option>
      </select>
    </div>

    {{-- Barra fixa (mobile) --}}
    <div class="sticky bottom-0 left-0 right-0 -mx-4 sm:mx-0 bg-white/90 backdrop-blur border-t border-gray-100 px-4 sm:px-0 py-3">
      <div class="flex items-center justify-between">
        <a href="{{ route('assistente.cidadao.dados.editar', $cidadao->id) }}"
           class="px-4 py-2 rounded-full border text-sm text-gray-700 hover:bg-gray-50">← Voltar</a>

        <div class="flex items-center gap-2">
          <a href="{{ route('assistente.cidadao.trabalho.editar', $cidadao->id) }}"
             class="px-4 py-2 rounded-full border text-sm text-gray-700 hover:bg-gray-50">Pular</a>

          <button type="submit"
                  class="px-5 py-2 rounded-full bg-indigo-600 text-white text-sm hover:bg-indigo-700">
            Salvar e continuar
          </button>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const input  = document.getElementById('bairro_search');
  const hidden = document.getElementById('bairro_id');
  const list   = document.getElementById('lista_bairros');
  const error  = document.getElementById('bairro_error');
  const form   = document.getElementById('form-moradia');

  function syncHidden(){
    hidden.value = '';
    const val = input.value;
    for (const opt of list.options) {
      if (opt.value === val) { hidden.value = opt.dataset.id; break; }
    }
    if (hidden.value) {
      error.classList.add('hidden');
      input.classList.remove('border-red-500');
    }
  }

  input.addEventListener('input', syncHidden);
  input.addEventListener('blur', function(){
    syncHidden();
    if (!hidden.value && input.value.trim() !== '') {
      error.classList.remove('hidden');
      input.classList.add('border-red-500');
    }
  });

  form.addEventListener('submit', function(e){
    syncHidden();
    if (!hidden.value) {
      e.preventDefault();
      error.classList.remove('hidden');
      input.classList.add('border-red-500');
      input.focus();
    }
  });

  // Pré-seleção (editar)
  syncHidden();
});
</script>
@endpush
