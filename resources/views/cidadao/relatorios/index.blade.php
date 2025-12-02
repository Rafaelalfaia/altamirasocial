@extends('layouts.app')

@section('title', 'Relatórios do Cidadão')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12 space-y-12">

    {{-- Cabeçalho --}}
    <div class="text-center">
        <h1 class="text-3xl font-bold text-green-800">📊 Relatórios do Cidadão</h1>
        <p class="text-sm text-gray-600 mt-1">Acompanhe seus dados de participação e atendimentos</p>
    </div>

    {{-- Bloco: Evoluções e Refeições --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Evoluções --}}
        <div class="bg-white shadow rounded-xl p-6 space-y-3">
            <h2 class="text-lg font-semibold text-green-700">👥 Visitas recebidas</h2>
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div class="text-center">
                    <p class="text-sm text-gray-500">1 mês</p>
                    <p class="text-2xl font-bold text-green-700">{{ $evolucoesUltimoMes }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">3 meses</p>
                    <p class="text-2xl font-bold text-green-700">{{ $evolucoes3Meses }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">6 meses</p>
                    <p class="text-2xl font-bold text-green-700">{{ $evolucoes6Meses }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">1 ano</p>
                    <p class="text-2xl font-bold text-green-700">{{ $evolucoesAno }}</p>
                </div>
            </div>
        </div>

        {{-- Refeições --}}
        <div class="bg-white shadow rounded-xl p-6 flex flex-col justify-center items-center">
            <h2 class="text-lg font-semibold text-green-700 mb-4">🍽️ Refeições no Restaurante Popular</h2>
            <p class="text-5xl font-bold text-green-700">{{ $vendasRestaurante }}</p>
            <p class="text-sm text-gray-500 mt-2">refeições registradas em seu nome</p>
        </div>
    </div>

    {{-- Bloco: Programas sociais --}}
    <div class="space-y-4">
        <h2 class="text-xl font-semibold text-green-700">🧩 Programas que você participa</h2>
        <ul class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse ($programas as $inscricao)
                <li class="bg-white shadow p-4 rounded-xl border border-gray-100">
                    <h3 class="text-base font-bold text-green-800">{{ $inscricao->programa->nome }}</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Status:
                        <span class="font-semibold capitalize">{{ $inscricao->status }}</span>
                    </p>
                </li>
            @empty
                <li class="text-sm text-gray-500">Nenhuma inscrição registrada.</li>
            @endforelse
        </ul>
    </div>

    {{-- Bloco: Perfil preenchido --}}
    <div class="space-y-4">
        <h2 class="text-xl font-semibold text-green-700">📋 Completo do seu cadastro</h2>
        <div class="bg-white p-6 rounded-xl shadow w-full max-w-md mx-auto">
            <canvas id="perfilChart" class="w-full h-64"></canvas>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('perfilChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Preenchido', 'Faltando'],
            datasets: [{
                data: [{{ $porcentagemPreenchida }}, {{ 100 - $porcentagemPreenchida }}],
                backgroundColor: ['#16a34a', '#e5e7eb'],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endsection
