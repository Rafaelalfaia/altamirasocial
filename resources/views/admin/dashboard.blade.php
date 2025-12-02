@extends('layouts.app')

@section('title', 'Dashboard Administrativo')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10 space-y-14">

    {{-- Cabeçalho com boas-vindas e foto do Admin --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white shadow rounded-xl p-6 border border-green-200 mb-10">

    {{-- Esquerda: Foto e saudação --}}
    <div class="flex items-center gap-5">
        <img src="{{ Auth::user()->foto_url }}"
             alt="Foto do Administrador"
             class="w-20 h-20 rounded-full object-cover border-4 border-green-700 shadow-md">

        <div>
            <p class="text-sm text-gray-500">Olá,</p>
            <h1 class="text-2xl font-bold text-green-800">{{ Auth::user()->name }}</h1>
            <p class="text-sm text-gray-600 mt-1">Bem-vindo ao Painel Administrativo do SEMAPS</p>
        </div>
    </div>

        {{-- Direita: Indicador do papel --}}
        <div class="text-right">
            <p class="text-sm text-gray-600">Perfil</p>
            <span class="inline-block mt-1 px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full shadow-sm">
                Administrador
            </span>
        </div>
    </div>


    {{-- Indicadores principais --}}
    <section>
        <h2 class="text-xl font-semibold text-white bg-green-700 px-4 py-2 rounded shadow inline-block">
            📌 Indicadores Gerais
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 mt-6">
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-3xl font-bold text-green-700">{{ $totalUsuarios }}</div>
                <div class="text-sm text-gray-600 mt-1">Usuários Totais</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-3xl font-bold text-blue-700">{{ $totalAdmins }}</div>
                <div class="text-sm text-gray-600 mt-1">Admins</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-3xl font-bold text-purple-700">{{ $totalCoordenadores }}</div>
                <div class="text-sm text-gray-600 mt-1">Coordenadores</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-3xl font-bold text-orange-700">{{ $totalAssistentes }}</div>
                <div class="text-sm text-gray-600 mt-1">Assistentes</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-3xl font-bold text-gray-700">{{ $totalCidadaos }}</div>
                <div class="text-sm text-gray-600 mt-1">Cidadãos</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-3xl font-bold text-yellow-700">{{ $totalProgramas }}</div>
                <div class="text-sm text-gray-600 mt-1">Programas Sociais</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-3xl font-bold text-red-700">{{ $totalEvolucoes }}</div>
                <div class="text-sm text-gray-600 mt-1">Evoluções</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-3xl font-bold text-red-500">{{ $totalEmergencias }}</div>
                <div class="text-sm text-gray-600 mt-1">Emergências</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-3xl font-bold text-red-600">{{ $totalDenuncias }}</div>
                <div class="text-sm text-gray-600 mt-1">Denúncias</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-3xl font-bold text-green-800">{{ $totalIndicacoes }}</div>
                <div class="text-sm text-gray-600 mt-1">Indicações</div>
            </div>
        </div>
        
        
    </section>
    {{-- Gráficos Analíticos --}}
    <section>
        <h2 class="text-xl font-semibold text-white bg-green-700 px-4 py-2 rounded shadow inline-block mt-12">
            📊 Gráficos Analíticos
        </h2>                   

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">

            {{-- Gráfico 1: Usuários por perfil --}}
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Usuários por Perfil</h3>
                <canvas id="graficoUsuarios" height="300"></canvas>
            </div>

            {{-- Gráfico 2: Cidadãos cadastrados por mês --}}
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Cidadãos Cadastrados (Últimos 6 meses)</h3>
                <canvas id="graficoCidadaosMes" height="300"></canvas>
            </div>

        </div>
    </section>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de Usuários por Perfil
    const ctxUsuarios = document.getElementById('graficoUsuarios').getContext('2d');
    new Chart(ctxUsuarios, {
        type: 'doughnut',
        data: {
            labels: @json($graficoUsuarios['labels']),
            datasets: [{
                data: @json($graficoUsuarios['valores']),
                backgroundColor: [
                    '#047857', '#9333EA', '#2563EB', '#F59E0B', '#10B981'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: false }
            }
        }
    });

    // Gráfico de Cidadãos por Mês
    const ctxCidadaos = document.getElementById('graficoCidadaosMes').getContext('2d');
    new Chart(ctxCidadaos, {
        type: 'line',
        data: {
            labels: @json(array_keys($cidadaosPorMes->toArray())),
            datasets: [{
                label: 'Cidadãos',
                data: @json(array_values($cidadaosPorMes->toArray())),
                backgroundColor: '#10B98133',
                borderColor: '#10B981',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endsection
