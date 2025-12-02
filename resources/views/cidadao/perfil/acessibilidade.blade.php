@extends('layouts.app')

@section('title', 'Acessibilidade e Observações')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow">

    @includeIf('cidadao.perfil.partials._steps', ['atual' => 'cidadao.perfil.acessibilidade'])

    <h1 class="text-2xl font-bold text-indigo-700 mb-6">4. Acessibilidade e Observações</h1>

    @includeIf('cidadao.perfil.partials._alerts')
    @includeIf('cidadao.perfil.partials._errors')

    @php
        $tipos = ['Física','Auditiva','Visual','Intelectual','Psicossocial','Múltipla','TEA','Outra'];

        $marcados = old('tipos_deficiencia', $cidadao->tipos_deficiencia ?? []);
        if (is_string($marcados)) {
            $dec = json_decode($marcados, true);
            $marcados = is_array($dec) ? $dec : [];
        }

        $possuiDef = (bool) old('possui_deficiencia', $cidadao->possui_deficiencia ?? false);
        $haGravida = (bool) old('ha_gravida', $cidadao->ha_gravida ?? false);
        $haIdoso   = (bool) old('ha_idoso',   $cidadao->ha_idoso   ?? false);

        // Data da declaração com fallback seguro
        $decl = old('data_declaracao');
        if ($decl === null) {
            $raw = $cidadao?->data_declaracao;
            $decl = $raw
                ? ($raw instanceof \Carbon\CarbonInterface
                    ? $raw->format('Y-m-d')
                    : \Illuminate\Support\Carbon::parse($raw)->format('Y-m-d'))
                : '';
        }
    @endphp

    <form
        method="POST"
        action="{{ route('cidadao.perfil.acessibilidade.salvar', $cidadao->id ?? null) }}"
        x-data="acessibilidadeForm(
            {{ \Illuminate\Support\Js::from($marcados) }},
            {{ $possuiDef ? 'true' : 'false' }},
            {{ $haGravida ? 'true' : 'false' }},
            {{ $haIdoso ? 'true' : 'false' }}
        )"
        class="space-y-8"
    >
        @csrf

        {{-- garante envio mesmo quando desmarcado --}}
        <input type="hidden" name="possui_deficiencia" value="0">
        {{-- mantém compat com backend que também aceita "pcd" --}}
        <input type="hidden" name="pcd" :value="(possuiDef || (tipos && tipos.length)) ? 1 : 0">
        {{-- chave dos tipos mesmo vazia (backend normaliza) --}}
        <input type="hidden" name="tipos_deficiencia" value="">

        {{-- Possui deficiência + Tipos --}}
        <section class="rounded-xl border border-gray-200 p-4 md:p-5 bg-gray-50/60">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div>
                    <p class="font-semibold text-gray-900">Possui deficiência (PCD)?</p>
                    <p class="text-xs text-gray-500">Marcar “Sim” habilita a seleção dos tipos.</p>
                </div>

                <label class="inline-flex items-center gap-2">
                    <input type="checkbox"
                           name="possui_deficiencia"
                           value="1"
                           x-model="possuiDef"
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-800">Sim</span>
                </label>
            </div>

            {{-- Tipos de Deficiência (visível apenas se possuir) --}}
            <div x-show="possuiDef" x-transition.opacity x-cloak class="mt-4">
                <p class="font-medium text-gray-800 mb-2">Tipos de Deficiência</p>

                <div class="flex flex-wrap gap-2 md:gap-3">
                    @foreach($tipos as $t)
                        @php $checked = in_array($t, $marcados); @endphp
                        <label
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border transition
                                   hover:bg-indigo-50 hover:border-indigo-300
                                   "
                            :class="tipos.includes('{{ $t }}')
                                    ? 'bg-indigo-50 border-indigo-300 ring-1 ring-indigo-200'
                                    : 'bg-white border-gray-200'">
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

                <p class="text-xs text-gray-500 mt-2">
                    Marcar qualquer tipo também ativa automaticamente “Possui deficiência”.
                </p>
            </div>
        </section>

        <hr class="border-gray-200">

        {{-- Gestante / Idoso --}}
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="rounded-xl border border-gray-200 p-4">
                <label class="font-medium text-gray-800 block">Há gestante no domicílio?</label>
                <label class="inline-flex items-center gap-2 mt-2">
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

        <hr class="border-gray-200">

        {{-- Dados da Declaração / CID --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700">Data da Declaração</label>
                <input type="date" name="data_declaracao" value="{{ $decl }}"
                       class="w-full border rounded px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

        </section>

        {{-- Declarante / Entrevistador --}}
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome do Declarante</label>
                <input type="text" name="nome_declarente"
                       value="{{ old('nome_declarente', $cidadao->nome_declarente) }}"
                       class="w-full border rounded px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Nome do Entrevistador</label>
                <input type="text" name="nome_entrevistador"
                       value="{{ old('nome_entrevistador', $cidadao->nome_entrevistador) }}"
                       class="w-full border rounded px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </section>

        {{-- Observações --}}
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Observações (cidadão)</label>
                <textarea name="observacoes" rows="4"
                          class="w-full border rounded px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                >{{ old('observacoes', $cidadao->observacoes) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Observações do Entrevistador</label>
                <textarea name="observacoes_entrevistador" rows="4"
                          class="w-full border rounded px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                >{{ old('observacoes_entrevistador', $cidadao->observacoes_entrevistador) }}</textarea>
            </div>
        </section>

        {{-- Ações --}}
        <div class="flex items-center justify-between pt-4">
            <a href="{{ route('cidadao.perfil.moradia') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border text-gray-700 hover:bg-gray-50">
                ← Voltar
            </a>

            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500">
                Salvar
            </button>
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
            // Se houver tipos marcados, garante "possui"
            this.syncPossuiDef();
            // Marcar algum tipo ativa Possui
            this.$watch('tipos', () => this.syncPossuiDef());
            // Ao desmarcar Possui, limpa os tipos e desabilita envio
            this.$watch('possuiDef', (v) => { if (!v) this.tipos = []; });
        },
        syncPossuiDef() {
            if (this.tipos && this.tipos.length > 0) {
                this.possuiDef = true;
            }
        }
    }
}
</script>
@endsection
