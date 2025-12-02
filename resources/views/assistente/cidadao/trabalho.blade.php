@extends('layouts.app')

@section('title', 'Trabalho e Renda (Assistente)')

@section('content')
@php
  $sp = old('situacao_profissional', $cidadao->situacao_profissional ?? '');
@endphp

<div class="max-w-5xl mx-auto">
  {{-- Passos --}}
  @includeWhen(true, 'assistente.cidadao.partials._steps', ['cidadao'=>$cidadao, 'atual'=>'trabalho'])

  {{-- Alerts/Errors --}}
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

  <form
    action="{{ route('assistente.cidadao.trabalho.salvar', $cidadao->id) }}"
    method="POST"
    class="rounded-2xl bg-white/90 backdrop-blur p-4 sm:p-6 shadow-sm ring-1 ring-gray-100 space-y-6"
  >
    @csrf

    <h1 class="text-lg sm:text-xl font-semibold text-indigo-700">3. Trabalho e Renda</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
      {{-- Situação Profissional (seleção igual ao cidadão) --}}
      <div>
        <label class="text-xs font-medium text-gray-700">Situação Profissional</label>
        <select name="situacao_profissional" class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
          <option value="">Selecione</option>
          @foreach (['CLT','Autônomo','Informal','MEI','Servidor Público','Desempregado','Aposentado','Estudante','Outro'] as $op)
            <option value="{{ $op }}" {{ $sp===$op?'selected':'' }}>{{ $op }}</option>
          @endforeach
        </select>
      </div>

      {{-- Ocupação --}}
      <div>
        <label class="text-xs font-medium text-gray-700">Ocupação</label>
        <input type="text" name="ocupacao"
               value="{{ old('ocupacao', $cidadao->ocupacao ?? '') }}"
               class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
      </div>

      {{-- Renda Individual --}}
      <div>
        <label class="text-xs font-medium text-gray-700">Renda Individual (R$)</label>
        {{-- usamos type="text" para permitir vírgula; o controller normaliza (parseMoneyInput) --}}
        <input type="text" name="renda" inputmode="decimal" autocomplete="off"
               placeholder="Ex.: 1.234,56"
               value="{{ old('renda', $cidadao->renda ?? '') }}"
               class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        <p class="text-[11px] text-gray-500 mt-1">Pode usar vírgula ou ponto; nós convertemos.</p>
      </div>

      {{-- Renda Total Familiar --}}
      <div>
        <label class="text-xs font-medium text-gray-700">Renda Total Familiar (R$)</label>
        <input type="text" name="renda_total_familiar" inputmode="decimal" autocomplete="off"
               placeholder="Ex.: 2.500,00"
               value="{{ old('renda_total_familiar', $cidadao->renda_total_familiar ?? '') }}"
               class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
      </div>

      {{-- Pessoas na Residência --}}
      <div>
        <label class="text-xs font-medium text-gray-700">Pessoas na Residência</label>
        <input type="number" name="pessoas_na_residencia" min="1" max="50"
               value="{{ old('pessoas_na_residencia', $cidadao->pessoas_na_residencia ?? 1) }}"
               class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
      </div>

      {{-- Grau de Parentesco --}}
      <div>
        <label class="text-xs font-medium text-gray-700">Grau de Parentesco</label>
        <input type="text" name="grau_parentesco"
               value="{{ old('grau_parentesco', $cidadao->grau_parentesco ?? '') }}"
               class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
      </div>

      {{-- Escolaridade (igual ao cidadão: campo livre) --}}
      <div class="sm:col-span-2">
        <label class="text-xs font-medium text-gray-700">Escolaridade</label>
        <input type="text" name="escolaridade"
               value="{{ old('escolaridade', $cidadao->escolaridade ?? '') }}"
               class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
      </div>
    </div>

    {{-- Barra de ações fixa (padrão dos outros passos) --}}
    <div class="sticky bottom-0 left-0 right-0 -mx-4 sm:mx-0 bg-white/90 backdrop-blur border-t border-gray-100 px-4 sm:px-0 py-3">
      <div class="flex items-center justify-between">
        <a href="{{ route('assistente.cidadao.moradia.editar', $cidadao->id) }}"
           class="px-4 py-2 rounded-full border text-sm text-gray-700 hover:bg-gray-50">← Voltar</a>

        <div class="flex items-center gap-2">
          <a href="{{ route('assistente.cidadao.acessibilidade.editar', $cidadao->id) }}"
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
