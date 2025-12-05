@extends('layouts.app')

@section('title', 'Indicadores · '.$programa->nome)
@section('page-title', 'Indicadores do Programa')

@section('content')
<div x-data="{ aba: '{{ request('aba', 'programa') }}' }" class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-semibold text-slate-800">
                Indicadores do programa: {{ $programa->nome }}
            </h1>
            <p class="text-sm text-slate-500">
                Mapeamento de inscrições, perfil socioeconômico e participação em outros programas.
            </p>
        </div>

        <div class="inline-flex rounded-full bg-slate-100 p-1 text-xs">
            <button type="button"
                    @click="aba='programa'"
                    :class="aba==='programa' ? 'bg-white shadow px-3 py-1 rounded-full font-medium text-slate-800' : 'px-3 py-1 text-slate-500'">
                Programa
            </button>
            <button type="button"
                    @click="aba='cidadaos'"
                    :class="aba==='cidadaos' ? 'bg-white shadow px-3 py-1 rounded-full font-medium text-slate-800' : 'px-3 py-1 text-slate-500'">
                Cidadãos selecionados
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Coluna esquerda: busca e seleção --}}
        <div class="lg:col-span-1">
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-4">
                <h2 class="text-sm font-semibold text-slate-800">
                    Buscar cidadãos do programa
                </h2>

                <form method="GET" action="{{ route('coordenador.programas.indicadores', $programa) }}" class="space-y-2">
                    <input type="hidden" name="aba" value="cidadaos">
                    <div class="flex gap-2">
                        <input type="text"
                               name="q"
                               value="{{ $q }}"
                               placeholder="Nome ou CPF"
                               class="flex-1 rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <button type="submit"
                                class="px-3 py-2 rounded-lg bg-emerald-600 text-xs font-semibold text-white hover:bg-emerald-700">
                            Buscar
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-500">
                        A busca está limitada aos cidadãos com inscrição neste programa.
                    </p>
                </form>

                @if($cidadaosBusca->isNotEmpty())
                    <form method="GET" action="{{ route('coordenador.programas.indicadores', $programa) }}" class="space-y-3">
                        <input type="hidden" name="q" value="{{ $q }}">
                        <input type="hidden" name="aba" value="cidadaos">

                        <div class="max-h-64 overflow-y-auto space-y-2">
                            @foreach($cidadaosBusca as $cid)
                                <label class="flex items-start gap-2 text-xs cursor-pointer">
                                    <input type="checkbox"
                                           name="cidadaos[]"
                                           value="{{ $cid->id }}"
                                           class="mt-0.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                           {{ in_array($cid->id, $cidadaosIdsSelecionados ?? []) ? 'checked' : '' }}>
                                    <span class="flex-1">
                                        <span class="font-medium text-slate-800">{{ $cid->nome }}</span>
                                        <span class="block text-slate-500">
                                            CPF: {{ $cid->cpf ?? '—' }}
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <button type="submit"
                                class="w-full px-3 py-2 rounded-lg bg-slate-900 text-xs font-semibold text-white hover:bg-slate-800">
                            Ver indicadores dos selecionados
                        </button>
                    </form>
                @else
                    @if($q !== '')
                        <p class="text-xs text-slate-500">
                            Nenhum cidadão encontrado para "{{ $q }}".
                        </p>
                    @else
                        <p class="text-xs text-slate-500">
                            Digite um nome ou CPF para começar.
                        </p>
                    @endif
                @endif

                @if($cidadaosSelecionados->isNotEmpty())
                    <div class="border-t border-slate-200 pt-3 mt-2">
                        <h3 class="text-xs font-semibold text-slate-700 mb-1">
                            Selecionados ({{ $cidadaosSelecionados->count() }})
                        </h3>
                        <ul class="space-y-1 max-h-40 overflow-y-auto text-[11px] text-slate-600">
                            @foreach($cidadaosSelecionados as $cid)
                                <li class="flex justify-between gap-2">
                                    <span class="truncate">• {{ $cid->nome }}</span>
                                    <a href="{{ route('cidadao.ficha', $cid->id) }}"
                                       target="_blank"
                                       class="text-[10px] text-emerald-700 hover:underline">
                                        Ficha
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        {{-- Coluna direita: indicadores --}}
        <div class="lg:col-span-2 space-y-6" x-cloak>
            {{-- ABA PROGRAMA --}}
            <div x-show="aba === 'programa'" class="space-y-6">
                {{-- Cards principais --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-white rounded-2xl border border-slate-200 p-3">
                        <div class="text-[11px] text-slate-500">Total de inscrições</div>
                        <div class="mt-1 text-xl font-semibold text-slate-900">
                            {{ $metricsPrograma['total_inscricoes'] }}
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-emerald-100 p-3">
                        <div class="text-[11px] text-emerald-700">Aprovadas</div>
                        <div class="mt-1 text-xl font-semibold text-emerald-800">
                            {{ $metricsPrograma['aprovadas'] }}
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-amber-100 p-3">
                        <div class="text-[11px] text-amber-700">Pendentes</div>
                        <div class="mt-1 text-xl font-semibold text-amber-800">
                            {{ $metricsPrograma['pendentes'] }}
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-rose-100 p-3">
                        <div class="text-[11px] text-rose-700">Reprovadas</div>
                        <div class="mt-1 text-xl font-semibold text-rose-800">
                            {{ $metricsPrograma['reprovadas'] }}
                        </div>
                    </div>
                </div>

                {{-- Cards de perfil socioeconômico --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-white rounded-2xl border border-slate-200 p-3">
                        <div class="text-[11px] text-slate-500">Cidadãos únicos</div>
                        <div class="mt-1 text-xl font-semibold text-slate-900">
                            {{ $metricsProgramaExtra['total_cidadaos_programa'] }}
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200 p-3">
                        <div class="text-[11px] text-slate-500">Idade média</div>
                        <div class="mt-1 text-xl font-semibold text-slate-900">
                            {{ $metricsProgramaExtra['idade_media'] ? $metricsProgramaExtra['idade_media'].' anos' : '—' }}
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200 p-3">
                        <div class="text-[11px] text-slate-500">Renda média familiar</div>
                        <div class="mt-1 text-xl font-semibold text-slate-900">
                            @if(!is_null($metricsProgramaExtra['renda_media_familiar']))
                                R$ {{ number_format($metricsProgramaExtra['renda_media_familiar'], 2, ',', '.') }}
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200 p-3">
                        <div class="text-[11px] text-slate-500">Renda média per capita</div>
                        <div class="mt-1 text-xl font-semibold text-slate-900">
                            @if(!is_null($metricsProgramaExtra['renda_media_per_capita']))
                                R$ {{ number_format($metricsProgramaExtra['renda_media_per_capita'], 2, ',', '.') }}
                            @else
                                —
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="bg-white rounded-2xl border border-slate-200 p-3">
                        <div class="text-[11px] text-slate-500">Média de acompanhamentos</div>
                        <div class="mt-1 text-xl font-semibold text-slate-900">
                            {{ $metricsProgramaExtra['media_acompanhamentos'] ?? '—' }}
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200 p-3">
                        <div class="text-[11px] text-slate-500">Com outros programas</div>
                        <div class="mt-1 text-xl font-semibold text-slate-900">
                            {{ $metricsProgramaExtra['cidadaos_outros_programas'] }}
                        </div>
                        @if($metricsProgramaExtra['total_cidadaos_programa'] > 0)
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                {{ round($metricsProgramaExtra['cidadaos_outros_programas'] / $metricsProgramaExtra['total_cidadaos_programa'] * 100, 1) }}%
                                dos cidadãos também participam de outros programas.
                            </div>
                        @endif
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200 p-3">
                        <div class="text-[11px] text-slate-500">PCD</div>
                        <div class="mt-1 text-sm text-slate-900">
                            Com deficiência: <span class="font-semibold">{{ $pcdPrograma['Com deficiência'] }}</span><br>
                            Sem deficiência: <span class="font-semibold">{{ $pcdPrograma['Sem deficiência'] }}</span>
                        </div>
                    </div>
                </div>

                {{-- Gráficos principais --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="bg-white rounded-2xl border border-slate-200 p-4">
                        <h2 class="text-sm font-semibold text-slate-800 mb-3">
                            Perfil de sexo dos inscritos
                        </h2>
                        <div class="h-64">
                            <canvas id="chartSexoPrograma"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 p-4">
                        <h2 class="text-sm font-semibold text-slate-800 mb-3">
                            Faixa etária dos inscritos
                        </h2>
                        <div class="h-64">
                            <canvas id="chartIdadePrograma"></canvas>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="bg-white rounded-2xl border border-slate-200 p-4">
                        <h2 class="text-sm font-semibold text-slate-800 mb-3">
                            Renda familiar por faixa
                        </h2>
                        <div class="h-64">
                            <canvas id="chartRendaPrograma"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 p-4">
                        <h2 class="text-sm font-semibold text-slate-800 mb-3">
                            Principais bairros
                        </h2>
                        <div class="h-64">
                            <canvas id="chartBairrosPrograma"></canvas>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="bg-white rounded-2xl border border-slate-200 p-4">
                        <h2 class="text-sm font-semibold text-slate-800 mb-3">
                            Escolaridade
                        </h2>
                        <div class="h-64">
                            <canvas id="chartEscolaridadePrograma"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 p-4">
                        <h2 class="text-sm font-semibold text-slate-800 mb-3">
                            Situação profissional / ocupação
                        </h2>
                        <div class="h-64">
                            <canvas id="chartEmpregoPrograma"></canvas>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="bg-white rounded-2xl border border-slate-200 p-4">
                        <h2 class="text-sm font-semibold text-slate-800 mb-3">
                            Rotina de acompanhamentos
                        </h2>
                        <div class="h-64">
                            <canvas id="chartAcompPrograma"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 p-4">
                        <h2 class="text-sm font-semibold text-slate-800 mb-3">
                            Outros programas mais comuns
                        </h2>
                        <div class="h-64">
                            <canvas id="chartOutrosProgramas"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ABA CIDADÃOS SELECIONADOS --}}
            <div x-show="aba === 'cidadaos'" class="space-y-6">
                @if($cidadaosSelecionados->isEmpty())
                    <div class="bg-white border border-dashed border-slate-300 rounded-2xl p-8 text-center text-sm text-slate-500">
                        Selecione um ou mais cidadãos na coluna ao lado para visualizar os indicadores combinados.
                    </div>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <div class="bg-white rounded-2xl border border-slate-200 p-3">
                            <div class="text-[11px] text-slate-500">Cidadãos selecionados</div>
                            <div class="mt-1 text-xl font-semibold text-slate-900">
                                {{ $metricsCidadaos['total'] }}
                            </div>
                        </div>
                        <div class="bg-white rounded-2xl border border-slate-200 p-3">
                            <div class="text-[11px] text-slate-500">Idade média</div>
                            <div class="mt-1 text-xl font-semibold text-slate-900">
                                {{ $metricsCidadaos['idade_media'] ? $metricsCidadaos['idade_media'].' anos' : '—' }}
                            </div>
                        </div>
                        <div class="bg-white rounded-2xl border border-slate-200 p-3">
                            <div class="text-[11px] text-slate-500">Renda média familiar</div>
                            <div class="mt-1 text-xl font-semibold text-slate-900">
                                @if(!is_null($metricsCidadaos['renda_media_familiar']))
                                    R$ {{ number_format($metricsCidadaos['renda_media_familiar'], 2, ',', '.') }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                        <div class="bg-white rounded-2xl border border-slate-200 p-3">
                            <div class="text-[11px] text-slate-500">Renda média per capita</div>
                            <div class="mt-1 text-xl font-semibold text-slate-900">
                                @if(!is_null($metricsCidadaos['renda_media_per_capita']))
                                    R$ {{ number_format($metricsCidadaos['renda_media_per_capita'], 2, ',', '.') }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                        <div class="bg-white rounded-2xl border border-slate-200 p-3">
                            <div class="text-[11px] text-slate-500">Total de acompanhamentos</div>
                            <div class="mt-1 text-xl font-semibold text-slate-900">
                                {{ $metricsCidadaos['total_acompanhamentos'] }}
                            </div>
                        </div>
                        <div class="bg-white rounded-2xl border border-slate-200 p-3">
                            <div class="text-[11px] text-slate-500">Programas diferentes</div>
                            <div class="mt-1 text-xl font-semibold text-slate-900">
                                {{ $metricsCidadaos['total_programas_distintos'] }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="bg-white rounded-2xl border border-slate-200 p-4">
                            <h2 class="text-sm font-semibold text-slate-800 mb-3">
                                Sexo (cidadãos selecionados)
                            </h2>
                            <div class="h-64">
                                <canvas id="chartSexoSelecionados"></canvas>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl border border-slate-200 p-4">
                            <h2 class="text-sm font-semibold text-slate-800 mb-3">
                                Faixa etária (cidadãos selecionados)
                            </h2>
                            <div class="h-64">
                                <canvas id="chartIdadeSelecionados"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="bg-white rounded-2xl border border-slate-200 p-4">
                            <h2 class="text-sm font-semibold text-slate-800 mb-3">
                                PCD (cidadãos selecionados)
                            </h2>
                            <div class="h-64">
                                <canvas id="chartPcdSelecionados"></canvas>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl border border-slate-200 p-4">
                            <h2 class="text-sm font-semibold text-slate-800 mb-3">
                                Participação em programas
                            </h2>
                            <div class="h-64">
                                <canvas id="chartProgramasCidadaos"></canvas>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function criaDoughnut(canvasId, dataObj) {
        const el = document.getElementById(canvasId);
        if (!el) return;
        const ctx = el.getContext('2d');
        const values = Object.values(dataObj || {});
        if (!values.length || values.every(v => v === 0)) return;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(dataObj),
                datasets: [{ data: values }]
            },
            options: {
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    function criaBar(canvasId, dataObj, horizontal = false) {
        const el = document.getElementById(canvasId);
        if (!el) return;
        const ctx = el.getContext('2d');
        const values = Object.values(dataObj || {});
        if (!values.length || values.every(v => v === 0)) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(dataObj),
                datasets: [{ data: values }]
            },
            options: {
                indexAxis: horizontal ? 'y' : 'x',
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // Programa - sexo
    criaDoughnut('chartSexoPrograma', @json($sexoPrograma));

    // Programa - idade
    criaBar('chartIdadePrograma', @json($idadeFaixasPrograma));

    // Programa - renda
    criaBar('chartRendaPrograma', @json($rendaFaixasPrograma));

    // Programa - bairros (horizontal)
    criaBar('chartBairrosPrograma', @json($bairrosPrograma), true);

    // Programa - escolaridade (horizontal)
    criaBar('chartEscolaridadePrograma', @json($escolaridadePrograma), true);

    // Programa - emprego (horizontal)
    criaBar('chartEmpregoPrograma', @json($empregoPrograma), true);

    // Programa - acompanhamentos
    criaBar('chartAcompPrograma', @json($acompanhamentoDistribuicao));

    // Programa - outros programas
    criaBar('chartOutrosProgramas', @json($outrosProgramasTop), true);

    // Selecionados - sexo
    criaDoughnut('chartSexoSelecionados', @json($sexoSelecionados));

    // Selecionados - idade
    criaBar('chartIdadeSelecionados', @json($idadeFaixasSelecionados));

    // Selecionados - PCD
    criaDoughnut('chartPcdSelecionados', @json($pcdSelecionados));

    // Selecionados - participação em programas
    (function () {
        const el = document.getElementById('chartProgramasCidadaos');
        if (!el) return;
        const ctx = el.getContext('2d');
        const labels = @json($chartProgramasCidadaos['labels'] ?? []);
        const data   = @json($chartProgramasCidadaos['data'] ?? []);
        if (!data.length || data.every(v => v === 0)) return;

        new Chart(ctx, {
            type: 'bar',
            data: { labels, datasets: [{ data }] },
            options: {
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { display: false } }
            }
        });
    })();
});
</script>
@endpush
