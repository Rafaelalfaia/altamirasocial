@extends('layouts.app')

@section('title', 'Acessibilidade & Observações')

@section('content')
@php
  use Illuminate\Support\Carbon;

  // Data da declaração fallback
  $decl = old('data_declaracao');
  if ($decl === null) {
      $raw = $cidadao?->data_declaracao;
      $decl = $raw
        ? ($raw instanceof \Carbon\CarbonInterface
            ? $raw->format('Y-m-d')
            : Carbon::parse($raw)->format('Y-m-d'))
        : '';
  }

  // Tipos padrão
  $tipos = $tipos ?? ['Física','Auditiva','Visual','Intelectual','Psicossocial','Múltipla','TEA','Outra'];

  // Marcados (array)
  $marcados = old('tipos_deficiencia', $marcados ?? ($cidadao->tipos_deficiencia ?? []));
  if (is_string($marcados)) {
      $dec = json_decode($marcados, true);
      $marcados = is_array($dec) ? $dec : array_filter(array_map('trim', explode(',',$marcados)));
  }
  if (!is_array($marcados)) $marcados = [];

  $possui = (bool) old('possui_deficiencia', $cidadao->possui_deficiencia ?? false);
  $haGravida = (bool) old('ha_gravida', $cidadao->ha_gravida ?? false);
  $haIdoso   = (bool) old('ha_idoso',   $cidadao->ha_idoso   ?? false);
@endphp

<div class="max-w-4xl mx-auto bg-white p-5 md:p-6 rounded-lg shadow">
    @includeWhen(true, 'assistente.cidadao.partials._steps', ['cidadao'=>$cidadao, 'atual'=>'acessibilidade'])

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded p-3 mb-4">
            <ul class="list-disc pl-4 space-y-1">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded p-3 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST"
          action="{{ route('assistente.cidadao.acessibilidade.salvar', $cidadao->id) }}"
          x-data="acessibilidadeForm({{ \Illuminate\Support\Js::from($marcados) }}, {{ $possui ? 'true':'false' }}, {{ $haGravida ? 'true':'false' }}, {{ $haIdoso ? 'true':'false' }})"
          class="space-y-6">
        @csrf

        <input type="hidden" name="possui_deficiencia" value="0">
        <input type="hidden" name="pcd" value="0">
        <input type="hidden" name="tipos_deficiencia" value="">

        <section class="rounded-xl border border-gray-200 p-4 bg-gray-50/60">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div>
                    <p class="font-semibold text-gray-900">Possui deficiência (PCD)?</p>
                    <p class="text-xs text-gray-500">Marcar “Sim” habilita a seleção dos tipos.</p>
                </div>
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="possui_deficiencia" value="1" x-model="possuiDef"
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-800">Sim</span>
                </label>
            </div>

            <div x-show="possuiDef" x-transition.opacity x-cloak class="mt-4">
                <p class="font-medium text-gray-800 mb-2">Tipos de Deficiência</p>
                <div class="flex flex-wrap gap-2 md:gap-3">
                    @foreach($tipos as $t)
                        @php $checked = in_array($t, $marcados); @endphp
                        <label
                          class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border transition
                                 hover:bg-indigo-50 hover:border-indigo-300"
                          :class="tipos.includes('{{ $t }}') ? 'bg-indigo-50 border-indigo-300 ring-1 ring-indigo-200' : 'bg-white border-gray-200'">
                            <input type="checkbox"
                                   name="tipos_deficiencia[]"
                                   value="{{ $t }}"
                                   x-model="tipos"
                                   :disabled="!possuiDef"
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                   @checked($checked)>
                            <span class="text-sm"
                                  :class="tipos.includes('{{ $t }}') ? 'text-indigo-900 font-medium' : 'text-gray-800'">
                                {{ $t }}
                            </span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-2">Marcar qualquer tipo também ativa automaticamente “Possui deficiência”.</p>
            </div>
        </section>

        {{-- Gestante / Idoso --}}
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="rounded-xl border border-gray-200 p-4">
                <label class="font-medium text-gray-800 block">Há gestante no domicílio?</label>
                <label class="inline-flex items-center gap-2 mt-2">
                    <input type="hidden" name="ha_gravida" value="0">
                    <input type="checkbox" name="ha_gravida" value="1" x-model="haGravida"
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span>Sim</span>
                </label>
                <div x-show="haGravida" x-transition.opacity x-cloak class="mt-3 space-y-1">
                    <label class="block text-sm text-gray-700">Nome da gestante</label>
                    <input type="text" name="nome_gravida"
                           value="{{ old('nome_gravida', $cidadao->nome_gravida) }}"
                           class="w-full border rounded px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 p-4">
                <label class="font-medium text-gray-800 block">Há idoso (60+) no domicílio?</label>
                <label class="inline-flex items-center gap-2 mt-2">
                    <input type="hidden" name="ha_idoso" value="0">
                    <input type="checkbox" name="ha_idoso" value="1" x-model="haIdoso"
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span>Sim</span>
                </label>
                <div x-show="haIdoso" x-transition.opacity x-cloak class="mt-3 space-y-1">
                    <label class="block text-sm text-gray-700">Nome do idoso</label>
                    <input type="text" name="nome_idoso"
                           value="{{ old('nome_idoso', $cidadao->nome_idoso) }}"
                           class="w-full border rounded px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </section>

        {{-- Declaração / CID / Observações --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium">Data da Declaração</label>
                <input type="date" name="data_declaracao" value="{{ $decl }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium">Observações (cidadão)</label>
                    <textarea name="observacoes" rows="4"
                              class="w-full border rounded px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('observacoes', $cidadao->observacoes) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium">Observações do Entrevistador</label>
                    <textarea name="observacoes_entrevistador" rows="4"
                              class="w-full border rounded px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('observacoes_entrevistador', $cidadao->observacoes_entrevistador) }}</textarea>
                </div>
            </div>
            <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium">Nome do Declarante</label>
                    <input type="text" name="nome_declarente"
                           value="{{ old('nome_declarente', $cidadao->nome_declarente) }}"
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Nome do Entrevistador</label>
                    <input type="text" name="nome_entrevistador"
                           value="{{ old('nome_entrevistador', $cidadao->nome_entrevistador) }}"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>
        </section>

        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('assistente.cidadao.trabalho.editar', $cidadao->id) }}" class="text-gray-600 hover:underline">← Trabalho & Renda</a>
            <button type="submit" class="px-5 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">Salvar</button>
        </div>
    </form>
</div>

{{-- Alpine helpers --}}
<script>
function acessibilidadeForm(tiposIniciais, possuiDefInicial, haGravidaInicial, haIdosoInicial) {
    return {
        tipos: Array.isArray(tiposIniciais) ? tiposIniciais : [],
        possuiDef: !!possuiDefInicial,
        haGravida: !!haGravidaInicial,
        haIdoso: !!haIdosoInicial,
        init() {
            this.syncPossuiDef();
            this.$watch('tipos', () => this.syncPossuiDef());
            this.$watch('possuiDef', (v) => { if (!v) this.tipos = []; });
        },
        syncPossuiDef() {
            if (this.tipos && this.tipos.length > 0) this.possuiDef = true;
        }
    }
}
</script>
@endsection
