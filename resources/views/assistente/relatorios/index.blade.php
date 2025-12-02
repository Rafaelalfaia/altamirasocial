@extends('layouts.app')

@section('title', 'Relatórios do Assistente')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12 space-y-16">

    {{-- Cabeçalho --}}
    <div class="text-center space-y-2">
        <h1 class="text-3xl font-bold text-green-900">📊 Relatórios Pessoais</h1>
        <p class="text-sm text-gray-600">Acompanhe seus dados de atendimento com clareza</p>
    </div>

    {{-- Filtro de Período --}}
<form method="GET" class="text-center">
    <label for="periodo" class="text-sm font-medium text-gray-700">Período:</label>
    <select name="periodo" id="periodo"
        class="ml-2 border-gray-300 rounded shadow-sm text-sm px-2 py-1"
        onchange="this.form.submit()">
        <option value="1m" {{ $periodo == '1m' ? 'selected' : '' }}>Último mês</option>
        <option value="3m" {{ $periodo == '3m' ? 'selected' : '' }}>Últimos 3 meses</option>
        <option value="6m" {{ $periodo == '6m' ? 'selected' : '' }}>Últimos 6 meses</option>
        <option value="1a" {{ $periodo == '1a' ? 'selected' : '' }}>Último ano</option>
    </select>
</form>

{{-- Indicadores principais --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-xl shadow text-center">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">📈 Total de Evoluções</h2>
        <p class="text-4xl font-extrabold text-green-700">{{ $totalEvolucoes }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow text-center">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">🆘 Emergências</h2>
        <p class="text-4xl font-extrabold text-red-600">{{ $emergencias }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow text-center">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">♿ Total de PCDs</h2>
        <p class="text-4xl font-extrabold text-indigo-700">{{ $totalPCDs }}</p>
    </div>
</div>

{{-- Cidadãos mais visitados --}}
<div class="bg-white p-6 rounded-xl shadow">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">🏆 Cidadãos Mais Visitados</h2>
    <ul class="space-y-2 text-sm text-gray-700">
        @forelse ($maisVisitados as $cidadaoId => $total)
            <li class="flex justify-between items-center border-b pb-1">
                <span class="font-medium text-gray-600">ID: {{ $cidadaoId }}</span>
                <span class="font-semibold text-green-700">{{ $total }} visitas</span>
            </li>
        @empty
            <li class="text-gray-400">Sem dados disponíveis</li>
        @endforelse
    </ul>
</div>

    {{-- Gráficos principais --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- 1. Gênero --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">⚧️ Gênero</h2>
            <div class="relative w-full h-[240px]">
                <canvas id="generoChart" class="absolute inset-0 w-full h-full"></canvas>
            </div>
        </div>

        {{-- 2. Faixa Etária --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">🎂 Faixa Etária</h2>
            <div class="relative w-full h-[240px]">
                <canvas id="idadeChart" class="absolute inset-0 w-full h-full"></canvas>
            </div>
        </div>

        {{-- 3. Bairros --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">🏘️ Bairros dos Cidadãos</h2>
            <div class="relative w-full h-[240px]">
                <canvas id="bairrosChart" class="absolute inset-0 w-full h-full"></canvas>
            </div>
        </div>

        {{-- 4. Programas Sociais --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">📋 Programas Sociais</h2>
            <div class="relative w-full h-[240px]">
                <canvas id="programasChart" class="absolute inset-0 w-full h-full"></canvas>
            </div>
        </div>

        {{-- 5. Ocorrências nas Evoluções --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">🚨 Ocorrências nas Evoluções</h2>
            <div class="relative w-full h-[240px]">
                <canvas id="ocorrenciasChart" class="absolute inset-0 w-full h-full"></canvas>
            </div>
        </div>

        {{-- 6. Renda --}}
        <div class="bg-white p-6 rounded-xl shadow text-center flex items-center justify-center flex-col h-[240px]">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">💰 Renda per Capita Média</h2>
            <p class="text-3xl font-extrabold text-indigo-600">R$ {{ number_format($rendaPerCapita, 2, ',', '.') }}</p>
        </div>
    </div>

    {{-- Indicadores adicionais --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow text-center">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">📋 PCDs em Programas</h2>
            <p class="text-2xl font-bold text-green-700">{{ $pcdsEmProgramas }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow text-center">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">📤 Indicações / 📥 Denúncias</h2>
            <p class="text-base text-gray-700">Indicações: <span class="font-bold text-blue-600">{{ $indicacoes }}</span></p>
            <p class="text-base text-gray-700">Denúncias: <span class="font-bold text-yellow-600">{{ $denuncias }}</span></p>
        </div>
    </div>

    {{-- Gráficos PCDs --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- 7. Programas dos PCDs --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">📊 Programas dos PCDs</h2>
            <div class="relative w-full h-[240px]">
                <canvas id="pcdProgramasChart" class="absolute inset-0 w-full h-full"></canvas>
            </div>
        </div>

        {{-- 8. Bairros dos PCDs --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">🏘️ Bairros dos PCDs</h2>
            <div class="relative w-full h-[240px]">
                <canvas id="pcdBairrosChart" class="absolute inset-0 w-full h-full"></canvas>
            </div>
        </div>
    </div>

        @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const gerarChart = (id, tipo, labels, dados, cor, horizontal = false) => {
                    new Chart(document.getElementById(id).getContext('2d'), {
                        type: tipo,
                        data: {
                            labels: labels,
                            datasets: [{
                                data: dados,
                                backgroundColor: cor,
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 10
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (ctx) {
                                            return `${ctx.label}: ${ctx.raw}`;
                                        }
                                    }
                                }
                            },
                            scales: horizontal
                                ? { x: { beginAtZero: true } }
                                : tipo === 'bar'
                                    ? { y: { beginAtZero: true } }
                                    : {}
                        }
                    });
                };

                // Gráfico de Gênero
                gerarChart(
                    'generoChart',
                    'pie',
                    {!! json_encode($generos->keys()) !!},
                    {!! json_encode($generos->values()) !!},
                    ['#34D399', '#60A5FA', '#FBBF24', '#F87171']
                );

                // Faixa Etária
                gerarChart(
                    'idadeChart',
                    'bar',
                    {!! json_encode($faixaEtaria->keys()) !!},
                    {!! json_encode($faixaEtaria->values()) !!},
                    ['#4ADE80']
                );

                // Bairros dos Cidadãos
                gerarChart(
                    'bairrosChart',
                    'bar',
                    {!! json_encode($bairros->keys()) !!},
                    {!! json_encode($bairros->values()) !!},
                    ['#60A5FA'],
                    true
                );

                // Programas Sociais
                gerarChart(
                    'programasChart',
                    'bar',
                    {!! json_encode($programas->keys()) !!},
                    {!! json_encode($programas->values()) !!},
                    ['#38BDF8']
                );

                // Ocorrências nas Evoluções
                gerarChart(
                    'ocorrenciasChart',
                    'doughnut',
                    ['Casos Emergenciais', 'Tentativas de Homicídio'],
                    [{{ $ocorrenciasEvolucao['casos_emergenciais'] }}, {{ $ocorrenciasEvolucao['tentativas_homicidio'] }}],
                    ['#F87171', '#FBBF24']
                );

                // Programas dos PCDs
                gerarChart(
                    'pcdProgramasChart',
                    'bar',
                    {!! json_encode($programasPCDs->keys()) !!},
                    {!! json_encode($programasPCDs->values()) !!},
                    ['#818CF8'],
                    true
                );

                // Bairros dos PCDs
                gerarChart(
                    'pcdBairrosChart',
                    'bar',
                    {!! json_encode($bairrosPCDs->keys()) !!},
                    {!! json_encode($bairrosPCDs->values()) !!},
                    ['#34D399'],
                    true
                );
            });
        </script>
    @endsection
@endsection