@extends('layouts.app')

@section('title', 'Trabalho e Renda')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
  @includeIf('cidadao.perfil.partials._steps', ['atual' => 'trabalho'])

  <h1 class="text-2xl font-bold text-indigo-700 mb-6">3. Trabalho e Renda</h1>

  @includeIf('cidadao.perfil.partials._alerts')
  @includeIf('cidadao.perfil.partials._errors')

  <form action="{{ route('cidadao.perfil.trabalho.salvar') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @csrf

    <div>
      <label class="block text-sm font-medium">Situação Profissional</label>
      @php $sp = old('situacao_profissional', $cidadao->situacao_profissional ?? ''); @endphp
      <select name="situacao_profissional" class="w-full border rounded px-3 py-2">
        <option value="">Selecione</option>
        @foreach (['CLT','Autônomo','Informal','MEI','Servidor Público','Desempregado','Aposentado','Estudante','Outro'] as $op)
          <option value="{{ $op }}" {{ $sp===$op?'selected':'' }}>{{ $op }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium">Ocupação</label>
      <input type="text" name="ocupacao" value="{{ old('ocupacao', $cidadao->ocupacao ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    <div>
      <label class="block text-sm font-medium">Renda Individual (R$)</label>
      <input type="number" name="renda" min="0" step="0.01" value="{{ old('renda', $cidadao->renda ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    <div>
      <label class="block text-sm font-medium">Renda Total Familiar (R$)</label>
      <input type="number" name="renda_total_familiar" min="0" step="0.01" value="{{ old('renda_total_familiar', $cidadao->renda_total_familiar ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    <div>
      <label class="block text-sm font-medium">Pessoas na Residência</label>
      <input type="number" name="pessoas_na_residencia" min="1" max="50" value="{{ old('pessoas_na_residencia', $cidadao->pessoas_na_residencia ?? 1) }}" class="w-full border rounded px-3 py-2">
    </div>

    <div>
      <label class="block text-sm font-medium">Grau de Parentesco</label>
      <input type="text" name="grau_parentesco" value="{{ old('grau_parentesco', $cidadao->grau_parentesco ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Escolaridade</label>
      <input type="text" name="escolaridade" value="{{ old('escolaridade', $cidadao->escolaridade ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    <div class="md:col-span-2 text-right mt-4">
      <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">Próxima Etapa: Acessibilidade →</button>
    </div>
  </form>
</div>
@endsection
