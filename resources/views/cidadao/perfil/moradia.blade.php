@extends('layouts.app')

@section('title', 'Dados de Moradia')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
  @includeIf('cidadao.perfil.partials._steps', ['atual' => 'moradia'])

  <h1 class="text-2xl font-bold text-indigo-700 mb-6">2. Moradia</h1>

  @includeIf('cidadao.perfil.partials._alerts')
  @includeIf('cidadao.perfil.partials._errors')

  <form id="form-moradia" action="{{ route('cidadao.perfil.moradia.salvar') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @csrf

    {{-- CEP --}}
    <div>
      <label class="block text-sm font-medium">CEP</label>
      <input type="text" name="cep" value="{{ old('cep', $cidadao->cep ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    {{-- Rua --}}
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Rua / Logradouro</label>
      <input type="text" name="rua" value="{{ old('rua', $cidadao->rua ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    {{-- Número --}}
    <div>
      <label class="block text-sm font-medium">Número</label>
      <input type="text" name="numero" value="{{ old('numero', $cidadao->numero ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    {{-- Complemento --}}
    <div>
      <label class="block text-sm font-medium">Complemento</label>
      <input type="text" name="complemento" value="{{ old('complemento', $cidadao->complemento ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    {{-- BAIRRO com busca (somente válidos) + Cidade/UF --}}
    @php
      $bairroAtualLabel = '';
      if (!empty($cidadao?->bairro)) {
          $uf = optional(optional($cidadao->bairro->cidade)->estado)->sigla;
          $bairroAtualLabel = $cidadao->bairro->nome.' — '.$cidadao->bairro->cidade->nome.($uf ? '/'.$uf : '');
      }
    @endphp

    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Bairro (selecione um da lista)</label>

      <input id="bairro_search" class="w-full border rounded px-3 py-2"
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

      {{-- Campo real enviado no POST (validação server-side garante exists) --}}
      <input type="hidden" name="bairro_id" id="bairro_id" value="{{ old('bairro_id', $cidadao->bairro_id) }}">

      <p id="bairro_help" class="text-xs text-gray-500 mt-1">Apenas bairros cadastrados pelo Coordenador são aceitos.</p>
      <p id="bairro_error" class="hidden text-xs text-red-600 mt-1">Selecione um bairro válido da lista.</p>
    </div>

    {{-- Situação de Moradia (radios) --}}
    @php
    $tm = old('tipo_moradia', $cidadao->tipo_moradia ?? '');
    $opcoesMoradia = ['Própria','Alugada','Cedida/Emprestada','Invasão','Outras'];
    @endphp

    <div class="md:col-span-2">
      <span class="block text-sm font-medium mb-1">Qual a situação de moradia atual?</span>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        @foreach($opcoesMoradia as $op)
          <label class="inline-flex items-center gap-2">
            <input type="radio" name="tipo_moradia" value="{{ $op }}" {{ $tm===$op ? 'checked' : '' }}>
            <span>{{ $op }}</span>
          </label>
        @endforeach
      </div>
    </div>

    {{-- Pontos de referência --}}
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Pontos de Referência</label>
      <input type="text" name="pontos_referencia" value="{{ old('pontos_referencia', $cidadao->pontos_referencia ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    {{-- Qualitativos adicionais --}}
    <div>
      <label class="block text-sm font-medium">Tempo que reside</label>
      @php $tr = old('tempo_reside', $cidadao->tempo_reside ?? ''); @endphp
      <select name="tempo_reside" class="w-full border rounded px-3 py-2">
        <option value="">Selecione</option>
        @foreach (['<1 ano','1–3 anos','4–6 anos','7–10 anos','>10 anos'] as $op)
          <option value="{{ $op }}" {{ $tr===$op?'selected':'' }}>{{ $op }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium">Qtde. de Cômodos</label>
      <input type="number" name="qtde_comodos" min="0" max="50" value="{{ old('qtde_comodos', $cidadao->qtde_comodos ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    <div>
      <label class="block text-sm font-medium">Tipo de Construção</label>
      @php $tc = old('tipo_construcao', $cidadao->tipo_construcao ?? ''); @endphp
      <select name="tipo_construcao" class="w-full border rounded px-3 py-2">
        <option value="">Selecione</option>
        @foreach (['Alvenaria','Madeira','Mista','Barro','Outros'] as $op)
          <option value="{{ $op }}" {{ $tc===$op?'selected':'' }}>{{ $op }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium">Tipo de Via</label>
      @php $tv = old('tipo_via', $cidadao->tipo_via ?? ''); @endphp
      <select name="tipo_via" class="w-full border rounded px-3 py-2">
        <option value="">Selecione</option>
        @foreach (['Asfalto','Bloquete','Piçarra','Terra','Outros'] as $op)
          <option value="{{ $op }}" {{ $tv===$op?'selected':'' }}>{{ $op }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium">Abastecimento de Água</label>
      @php $aa = old('abastecimento_agua', $cidadao->abastecimento_agua ?? ''); @endphp
      <select name="abastecimento_agua" class="w-full border rounded px-3 py-2">
        <option value="">Selecione</option>
        @foreach (['Rede geral de distribuição','Poço','Fonte/Nascente','Carro pipa','Rio/Igarapé','Outro'] as $op)
          <option value="{{ $op }}" {{ $aa===$op?'selected':'' }}>{{ $op }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium">Tipo de Energia</label>
      @php $et = old('energia_tipo', $cidadao->energia_tipo ?? ''); @endphp
      <select name="energia_tipo" class="w-full border rounded px-3 py-2">
        <option value="">Selecione</option>
        @foreach (['Medidor próprio','Improvisado/Gambiarra','Não possui','Outros'] as $op)
          <option value="{{ $op }}" {{ $et===$op?'selected':'' }}>{{ $op }}</option>
        @endforeach
      </select>
    </div>

    {{-- Utilidades (booleans) --}}
    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
      <label class="flex items-center gap-2">
        <input type="hidden" name="tem_esgoto" value="0">
        <input type="checkbox" name="tem_esgoto" value="1" {{ old('tem_esgoto', $cidadao->tem_esgoto ?? false) ? 'checked' : '' }}>
        Esgoto
      </label>

      <label class="flex items-center gap-2">
        <input type="hidden" name="tem_agua_encanada" value="0">
        <input type="checkbox" name="tem_agua_encanada" value="1" {{ old('tem_agua_encanada', $cidadao->tem_agua_encanada ?? false) ? 'checked' : '' }}>
        Água encanada
      </label>

      <label class="flex items-center gap-2">
        <input type="hidden" name="tem_coleta_lixo" value="0">
        <input type="checkbox" name="tem_coleta_lixo" value="1" {{ old('tem_coleta_lixo', $cidadao->tem_coleta_lixo ?? false) ? 'checked' : '' }}>
        Coleta de lixo
      </label>

      <label class="flex items-center gap-2">
        <input type="hidden" name="tem_energia" value="0">
        <input type="checkbox" name="tem_energia" value="1" {{ old('tem_energia', $cidadao->tem_energia ?? false) ? 'checked' : '' }}>
        Energia
      </label>
    </div>

    {{-- Animais (sem "Nº de Animais") --}}
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Possui animais?</label>
      @php $pa = old('possui_animais', $cidadao->possui_animais ?? false); @endphp
      <select name="possui_animais" class="w-full border rounded px-3 py-2">
        <option value="0" {{ !$pa ? 'selected':'' }}>Não</option>
        <option value="1" {{  $pa ? 'selected':'' }}>Sim</option>
      </select>
    </div>

    {{-- Navegação --}}
    <div class="md:col-span-2 flex justify-between mt-4">
      <a href="{{ route('cidadao.perfil.dados') }}" class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">← Voltar</a>
      <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">
        Próxima Etapa: Trabalho e Renda →
      </button>
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
    // UI: erro some quando valida
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
