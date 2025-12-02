@extends('layouts.app')

@section('title', 'Relatórios do Sistema')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10 space-y-14">
    {{-- Título --}}
    <div class="text-center space-y-2">
        <h1 class="text-3xl font-bold text-green-900">📊 Relatórios Gerais do Sistema</h1>
        <p class="text-sm text-gray-600">Visualização consolidada dos dados da plataforma</p>
    </div>

    {{-- ======================== PARTE 1: CIDADÃOS ======================== --}}
    <section>
        <h2 class="text-2xl font-bold text-green-800 mb-6">👥 Cidadãos</h2>

        {{-- Indicadores rápidos --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-white rounded-lg p-4 shadow">
                <p class="text-sm text-gray-500">Total Cadastrados</p>
                <p class="text-2xl font-bold text-green-700">{{ \App\Models\Cidadao::count() }}</p>
            </div>
            <div class="bg-white rounded-lg p-4 shadow">
                <p class="text-sm text-gray-500">Temporários</p>
                <p class="text-2xl font-bold text-green-700">{{ \App\Models\CidadaoTemporario::count() }}</p>
            </div>
            <div class="bg-white rounded-lg p-4 shadow">
                <p class="text-sm text-gray-500">PCDs</p>
                <p class="text-2xl font-bold text-green-700">
                    {{ \App\Models\Cidadao::where('pcd', true)->count() }}

                </p>
            </div>
        </div>


        {{-- Gráficos --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">📈 Crescimento Mensal</h3>
                <canvas id="graficoCrescimentoCidadaos"></canvas>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">⚖️ Gênero</h3>
                <canvas id="graficoGeneroCidadaos"></canvas>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">🎂 Faixa Etária</h3>
                <canvas id="graficoFaixaEtariaCidadaos"></canvas>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">♿ Pessoas com Deficiência</h3>
                <canvas id="graficoPcdCidadaos"></canvas>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">📍 Por Região</h3>
                <canvas id="graficoCidadaosPorRegiao"></canvas>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">✅ Preenchimento do Cadastro</h3>
                <canvas id="graficoPreenchimentoCadastro"></canvas>
            </div>
        </div>
    </section>

    {{-- ======================== PARTE 2: PROGRAMAS SOCIAIS ======================== --}}
<section class="mt-20">
    <h2 class="text-2xl font-bold text-green-800 mb-6">📦 Programas Sociais</h2>

    {{-- Indicadores rápidos --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-lg p-4 shadow">
            <p class="text-sm text-gray-500">Programas Ativos</p>
            <p class="text-2xl font-bold text-green-700">{{ \App\Models\Programa::count() }}</p>
        </div>
        <div class="bg-white rounded-lg p-4 shadow">
            <p class="text-sm text-gray-500">Inscrições</p>
            <p class="text-2xl font-bold text-green-700">{{ \App\Models\ProgramaInscricao::count() }}</p>
        </div>
        <div class="bg-white rounded-lg p-4 shadow">
            <p class="text-sm text-gray-500">Denúncias</p>
            <p class="text-2xl font-bold text-red-700">{{ \App\Models\DenunciaPrograma::count() }}</p>
        </div>
        <div class="bg-white rounded-lg p-4 shadow">
            <p class="text-sm text-gray-500">Indicações</p>
            <p class="text-2xl font-bold text-blue-700">{{ \App\Models\IndicacaoPrograma::count() }}</p>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">👥 Inscrições por Programa</h3>
            <canvas id="graficoInscricoesPorPrograma"></canvas>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">📊 Status das Inscrições</h3>
            <canvas id="graficoStatusInscricoes"></canvas>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">⚠️ Denúncias por Programa</h3>
            <canvas id="graficoDenunciasPorPrograma"></canvas>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">✅ Indicações por Programa</h3>
            <canvas id="graficoIndicacoesPorPrograma"></canvas>
        </div>



        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">💰 Média de Renda por Programa</h3>
            <canvas id="graficoMediaRendaBeneficiarios"></canvas>
        </div>
    </div>
</section>

{{-- ======================== PARTE 3: ASSISTENTES SOCIAIS ======================== --}}
<section class="mt-20">
    <h2 class="text-2xl font-bold text-green-800 mb-6">🧑‍⚕️ Assistentes Sociais</h2>

    {{-- Indicadores rápidos --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-lg p-4 shadow">
            <p class="text-sm text-gray-500">Total Assistentes</p>
            <p class="text-2xl font-bold text-green-700">{{ \App\Models\User::role('Assistente')->count() }}</p>
        </div>


    </div>

    {{-- Gráficos --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">📋 Evoluções por Assistente (Top 5)</h3>
            <canvas id="graficoEvolucoesPorAssistente"></canvas>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">🧑‍⚕️ Ranking Geral de Atendimentos</h3>
            <canvas id="graficoAssistenteMaisAtivo"></canvas>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">⏱️ Plantão Ativo (últimas 48h)</h3>
            <canvas id="graficoPlantaoAtivo"></canvas>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">📩 Respostas a Solicitações</h3>
            <canvas id="graficoRespostasSolicitacoesAssistente"></canvas>
        </div>

        <div class="bg-white p-4 rounded-lg shadow md:col-span-2">
            <h3 class="text-lg font-semibold mb-2">🏆 Ranking de Produtividade</h3>
            <canvas id="graficoRankingAssistentesProdutividade"></canvas>
        </div>
    </div>
</section>

{{-- ======================== PARTE 4: RESTAURANTE & EMERGÊNCIAS ======================== --}}
<section class="mt-20">
    <h2 class="text-2xl font-bold text-green-800 mb-6">🍽️ Restaurante & Emergências</h2>

    {{-- Indicadores rápidos --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-lg p-4 shadow">
            <p class="text-sm text-gray-500">Total Vendas Restaurante</p>
            <p class="text-2xl font-bold text-green-700">{{ \App\Models\VendaRestaurante::count() }}</p>
        </div>
        <div class="bg-white rounded-lg p-4 shadow">
            <p class="text-sm text-gray-500">Total Emergências</p>
            <p class="text-2xl font-bold text-red-700">{{ \App\Models\Emergencia::count() }}</p>
        </div>
        <div class="bg-white rounded-lg p-4 shadow">
            <p class="text-sm text-gray-500">Encaminhamentos</p>
            <p class="text-2xl font-bold text-blue-700">{{ \App\Models\RecebimentoEncaminhamento::count() }}</p>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">📆 Vendas nos Últimos Dias</h3>
            <canvas id="graficoVendasPorDia"></canvas>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">📦 Tipo de Consumo</h3>
            <canvas id="graficoTipoConsumo"></canvas>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">💳 Formas de Pagamento</h3>
            <canvas id="graficoFormasPagamento"></canvas>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">🚨 Emergências por Mês</h3>
            <canvas id="graficoEmergenciasPorPeriodo"></canvas>
        </div>

        <div class="bg-white p-4 rounded-lg shadow md:col-span-2">
            <h3 class="text-lg font-semibold mb-2">🏛️ Encaminhamentos por Órgão</h3>
            <canvas id="graficoEncaminhamentosPorOrgao"></canvas>
        </div>
    </div>
</section>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    async function carregarGrafico(id, rota, tipo = 'bar') {
        const res = await fetch(rota);
        const dados = await res.json();
        const ctx = document.getElementById(id).getContext('2d');

        new Chart(ctx, {
            type: tipo,
            data: {
                labels: dados.labels,
                datasets: [{
                    label: '',
                    data: dados.data,
                    backgroundColor: 'rgba(34, 197, 94, 0.6)',
                    borderColor: 'rgba(22, 163, 74, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    carregarGrafico('graficoCrescimentoCidadaos', '{{ route('admin.relatorios.cidadaos.crescimento') }}');
    carregarGrafico('graficoGeneroCidadaos', '{{ route('admin.relatorios.cidadaos.genero') }}', 'doughnut');
    carregarGrafico('graficoFaixaEtariaCidadaos', '{{ route('admin.relatorios.cidadaos.faixa_etaria') }}', 'bar');
    carregarGrafico('graficoPcdCidadaos', '{{ route('admin.relatorios.cidadaos.pcd') }}', 'pie');
    carregarGrafico('graficoCidadaosPorRegiao', '{{ route('admin.relatorios.cidadaos.regiao') }}', 'bar');
    carregarGrafico('graficoPreenchimentoCadastro',  '{{ route('admin.relatorios.cidadaos.preenchimento') }}', 'doughnut');

    carregarGrafico('graficoInscricoesPorPrograma', '{{ route('admin.relatorios.programas.inscricoes') }}', 'bar');
    carregarGrafico('graficoStatusInscricoes', '{{ route('admin.relatorios.programas.status') }}', 'doughnut');
    carregarGrafico('graficoDenunciasPorPrograma', '{{ route('admin.relatorios.programas.denuncias') }}', 'bar');
    carregarGrafico('graficoIndicacoesPorPrograma', '{{ route('admin.relatorios.programas.indicacoes') }}', 'bar');

    carregarGrafico('graficoMediaRendaBeneficiarios', '{{ route('admin.relatorios.programas.renda') }}', 'bar');


    carregarGrafico('graficoEvolucoesPorAssistente', '{{ route('admin.relatorios.assistentes.evolucoes') }}', 'bar');
    carregarGrafico('graficoAssistenteMaisAtivo', '{{ route('admin.relatorios.assistentes.ativos') }}', 'bar');
    carregarGrafico('graficoPlantaoAtivo', '{{ route('admin.relatorios.assistentes.plantao') }}', 'doughnut');
    carregarGrafico('graficoRespostasSolicitacoesAssistente', '{{ route('admin.relatorios.assistentes.respostas') }}', 'bar');
    carregarGrafico('graficoRankingAssistentesProdutividade', '{{ route('admin.relatorios.assistentes.ranking') }}', 'bar');

    carregarGrafico('graficoVendasPorDia', '{{ route('admin.relatorios.restaurante.vendas') }}', 'bar');
    carregarGrafico('graficoTipoConsumo', '{{ route('admin.relatorios.restaurante.consumo') }}', 'doughnut');
    carregarGrafico('graficoFormasPagamento', '{{ route('admin.relatorios.restaurante.pagamento') }}', 'pie');
    carregarGrafico('graficoEmergenciasPorPeriodo', '{{ route('admin.relatorios.emergencias.periodo') }}', 'bar');
    carregarGrafico('graficoEncaminhamentosPorOrgao', '{{ route('admin.relatorios.encaminhamentos.orgao') }}', 'bar');


</script>
@endsection
