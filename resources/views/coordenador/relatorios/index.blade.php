@extends('layouts.app')

@section('title', 'Relatórios do Coordenador')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12 space-y-14">

    {{-- Cabeçalho --}}
    <div class="text-center space-y-2">
        <h1 class="text-3xl font-semibold text-green-900">📊 Relatórios – Coordenador</h1>
        <p class="text-sm text-gray-500">Visualização analítica da atuação dos assistentes</p>
    </div>

    {{-- ======================== SEÇÃO 1: ASSISTENTES ======================== --}}
    <section>
        <h2 class="text-2xl font-bold text-green-800 mb-4">👥 Assistentes</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- 1. Assistentes Criados por Mês --}}
            <x-dashboard.card-grafico id="assistentesCriadosChart" title="Assistentes Criados por Mês" />

            {{-- 2. Visitas por Assistente --}}
            <x-dashboard.card-grafico id="visitasPorAssistenteChart" title="Visitas (Evoluções) por Assistente" />

            {{-- 3. Ocorrências em Plantão --}}
            <x-dashboard.card-grafico id="plantaoOcorrenciasChart" title="Ocorrências em Plantão (por Dia)" />

            {{-- 4. Solicitações Enviadas --}}
            <x-dashboard.card-grafico id="solicitacoesChart" title="Solicitações Enviadas para Assistentes" />

            {{-- 5. Ranking de Visitas --}}
            <x-dashboard.card-grafico id="rankingVisitasChart" title="Top 5 – Assistentes com Mais Visitas" />

            {{-- 6. Denúncias por Assistente --}}
            <x-dashboard.card-grafico id="denunciasAssistenteChart" title="Denúncias Feitas por Assistente" />

            {{-- 7. Indicações por Assistente --}}
            <x-dashboard.card-grafico id="indicacoesAssistenteChart" title="Indicações Feitas por Assistente" />
        </div>
    </section>

    {{-- ======================== SEÇÃO 2: CIDADÃOS ======================== --}}
    <section>
        <h2 class="text-2xl font-bold text-green-800 mb-4">🧑‍🤝‍🧑 Cidadãos</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- 1. Total de Cidadãos --}}
            <div class="bg-white p-4 rounded-xl shadow-md text-center">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Total de Cidadãos Cadastrados</h3>
                <p class="text-4xl font-bold text-green-700">{{ $totalCidadaos }}</p>
            </div>

            {{-- 2. Em Programas (%) --}}
            <div class="bg-white p-4 rounded-xl shadow-md text-center">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">% em Programas Sociais</h3>
                <p class="text-4xl font-bold text-indigo-700">{{ $percentualCidadaosPrograma }}%</p>
            </div>

            {{-- 3. Distribuição por Gênero --}}
            <x-dashboard.card-grafico id="generoCidadaosChart" title="Distribuição por Gênero" />

            {{-- 4. Faixa Etária --}}
            <x-dashboard.card-grafico id="faixaEtariaCidadaosChart" title="Faixa Etária dos Cidadãos" />

            {{-- 5. Cidadãos por Bairro --}}
            <x-dashboard.card-grafico id="cidadaosPorBairroChart" title="Cidadãos por Bairro" />

            {{-- 6. Status de Acompanhamentos --}}
            <x-dashboard.card-grafico id="statusAcompanhamentosChart" title="Status de Acompanhamentos" />

            <div class="bg-white p-4 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-2 text-gray-800">♿ Pessoas com Deficiência</h3>
                <div class="relative h-72">
                    <canvas id="graficoPessoasComDeficiencia"></canvas>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-2 text-gray-800">👥 PCDs em Programas Sociais</h3>
                <div class="relative h-72">
                    <canvas id="participacaoProgramasChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-2 text-gray-800">♿ PCDs por Programa</h3>
                <div class="relative h-72">
                    <canvas id="pcdsPorProgramaChart"></canvas>
                </div>
            </div>
            

        </div>
    </section>

    {{-- ======================== SEÇÃO 3: PROGRAMAS SOCIAIS ======================== --}}
    <section>
        <h2 class="text-2xl font-bold text-green-800 mb-4">🏛️ Programas Sociais</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- 1. Total de Programas --}}
            <div class="bg-white p-4 rounded-xl shadow-md text-center">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Programas Ativos</h3>
                <p class="text-4xl font-bold text-green-700">{{ $totalProgramas }}</p>
            </div>

            {{-- 2. Total de Inscritos --}}
            <div class="bg-white p-4 rounded-xl shadow-md text-center">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Cidadãos Inscritos</h3>
                <p class="text-4xl font-bold text-indigo-700">{{ $totalInscritosProgramas }}</p>
            </div>

            {{-- 3. Média de Renda por Programa --}}
            <x-dashboard.card-grafico id="mediaRendaProgramaChart" title="💰 Média de Renda Total por Programa" />

            <x-dashboard.card-grafico 
                id="mediaRendaPerCapitaChart" 
                title="💰 Média de Renda Per Capita por Programa" 
            />


            {{-- 4. Evoluções por Programa --}}
            <x-dashboard.card-grafico id="evolucoesPorProgramaChart" title="Evoluções Realizadas por Programa" />

            {{-- 5. Inscrições por Região --}}
            <x-dashboard.card-grafico id="inscricoesPorRegiaoChart" title="Inscrições por Região" />
            {{-- 6. Inscrições por bairro --}}
            <x-dashboard.card-grafico id="inscricoesPorBairroChart" title="Inscrições por Bairro" />

            <x-dashboard.card-grafico id="statusInscricoesChart" title="📊 Status das Inscrições" />

            <x-dashboard.card-grafico id="aprovacoesPorProgramaChart" title="✅ Aprovações por Programa" />



        </div>
    </section>

    {{-- ======================== SEÇÃO 4: GERAL ======================== --}}
    <section>
        <h2 class="text-2xl font-bold text-red-700 mb-6">🟥 Geral</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Gráfico: Horas de Plantão por Dia --}}
            <div class="bg-white p-4 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-2 text-gray-800">⏱️ Horas de Plantão por Dia</h3>
                <div class="relative h-72">
                    <canvas id="horasPlantaoPorDiaChart"></canvas>
                </div>
            </div>

            {{-- Emergências em Plantão --}}
            <div class="bg-white p-4 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-2 text-gray-800">🚨 Emergências em Plantão</h3>
                <div class="relative h-72">
                    <canvas id="emergenciasDurantePlantaoChart"></canvas>
                </div>
            </div>

            
            {{-- Recebimentos vs Encaminhamentos --}}
            <div class="bg-white p-4 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-2 text-gray-800">📊 Recebimentos vs Encaminhamentos</h3>
                <div class="relative h-72">
                    <canvas id="recebimentoEncaminhamentoChart"></canvas>
                </div>
            </div>

             {{-- Recebimentos --}}

            <div class="bg-white p-4 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-2 text-gray-800">🏛️ Recebimentos por Órgão</h3>
                <div class="relative h-72">
                    <canvas id="recebimentosPorOrgaoChart"></canvas>
                </div>
            </div>

            {{-- Encaminhamentos --}}

            <div class="bg-white p-4 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-2 text-gray-800">📤 Encaminhamentos por Órgão</h3>
                <div class="relative h-72">
                    <canvas id="encaminhamentosPorOrgaoChart"></canvas>
                </div>
            </div>

            
            
        </div>
    </section>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Assistentes Criados
    new Chart(document.getElementById('assistentesCriadosChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($assistentesCriados->pluck('mes')) !!},
            datasets: [{
                label: 'Criados',
                data: {!! json_encode($assistentesCriados->pluck('total')) !!},
                backgroundColor: 'rgba(34,197,94,0.6)',
                borderColor: 'rgba(34,197,94,1)',
                borderWidth: 1
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // 2. Visitas por Assistente
    new Chart(document.getElementById('visitasPorAssistenteChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($visitasPorAssistente->pluck('user.name')) !!},
            datasets: [{
                label: 'Visitas',
                data: {!! json_encode($visitasPorAssistente->pluck('total')) !!},
                backgroundColor: 'rgba(59,130,246,0.6)',
                borderColor: 'rgba(59,130,246,1)',
                borderWidth: 1
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // 3. Ocorrências em Plantão
    new Chart(document.getElementById('plantaoOcorrenciasChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($plantaoOcorrencias->pluck('data')) !!},
            datasets: [{
                label: 'Ocorrências',
                data: {!! json_encode($plantaoOcorrencias->pluck('total')) !!},
                borderColor: 'rgba(234,88,12,1)',
                backgroundColor: 'rgba(234,88,12,0.2)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // 4. Solicitações Enviadas
    new Chart(document.getElementById('solicitacoesChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($solicitacoesAssistentes->pluck('data')) !!},
            datasets: [{
                label: 'Solicitações',
                data: {!! json_encode($solicitacoesAssistentes->pluck('total')) !!},
                borderColor: 'rgba(99,102,241,1)',
                backgroundColor: 'rgba(99,102,241,0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // 5. Ranking de Visitas
    new Chart(document.getElementById('rankingVisitasChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($rankingVisitas->pluck('name')) !!},
            datasets: [{
                label: 'Visitas',
                data: {!! json_encode($rankingVisitas->pluck('evolucoes_count')) !!},
                backgroundColor: 'rgba(16,185,129,0.7)',
                borderColor: 'rgba(5,150,105,1)',
                borderWidth: 1
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // 6. Denúncias por Assistente
    new Chart(document.getElementById('denunciasAssistenteChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($denunciasPorAssistente->pluck('assistente.name')) !!},
            datasets: [{
                label: 'Denúncias',
                data: {!! json_encode($denunciasPorAssistente->pluck('total')) !!},
                backgroundColor: 'rgba(239,68,68,0.6)',
                borderColor: 'rgba(220,38,38,1)',
                borderWidth: 1
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // 7. Indicações por Assistente
    new Chart(document.getElementById('indicacoesAssistenteChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($indicacoesPorAssistente->pluck('assistente.name')) !!},
            datasets: [{
                label: 'Indicações',
                data: {!! json_encode($indicacoesPorAssistente->pluck('total')) !!},
                backgroundColor: 'rgba(251,191,36,0.6)',
                borderColor: 'rgba(202,138,4,1)',
                borderWidth: 1
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    //SEÇÃO CIDADÃOS

    // 3. Distribuição por Gênero
    new Chart(document.getElementById('generoCidadaosChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($generoCidadaos->pluck('sexo')) !!},
            datasets: [{
                label: 'Cidadãos',
                data: {!! json_encode($generoCidadaos->pluck('total')) !!},
                backgroundColor: ['#60a5fa', '#f472b6', '#c084fc']
            }]
        },
        options: {
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // 4. Faixa Etária
    new Chart(document.getElementById('faixaEtariaCidadaosChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($faixaEtariaCidadaos->pluck('faixa')) !!},
            datasets: [{
                label: 'Cidadãos',
                data: {!! json_encode($faixaEtariaCidadaos->pluck('total')) !!},
                backgroundColor: 'rgba(96,165,250,0.7)',
                borderColor: 'rgba(59,130,246,1)',
                borderWidth: 1
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // 5. Cidadãos por Bairro
    new Chart(document.getElementById('cidadaosPorBairroChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($cidadaosPorBairro->pluck('bairro.nome')) !!},
            datasets: [{
                label: 'Cidadãos',
                data: {!! json_encode($cidadaosPorBairro->pluck('total')) !!},
                backgroundColor: 'rgba(34,197,94,0.6)',
                borderColor: 'rgba(34,197,94,1)',
                borderWidth: 1
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // 6. Status de Acompanhamentos
    new Chart(document.getElementById('statusAcompanhamentosChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($statusAcompanhamentos->pluck('status')) !!},
            datasets: [{
                data: {!! json_encode($statusAcompanhamentos->pluck('total')) !!},
                backgroundColor: ['#10b981', '#f59e0b']
            }]
        },
        options: {
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // Gráfico de Pessoas com Deficiência
fetch('{{ route("coordenador.relatorios.grafico.pessoas_deficiencia") }}')
    .then(response => response.json())
    .then(data => {
        new Chart(document.getElementById('graficoPessoasComDeficiencia'), {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Total',
                    data: data.dados,
                    backgroundColor: [
                        'rgba(34,197,94,0.6)',
                        'rgba(239,68,68,0.6)',
                    ],
                    borderColor: [
                        'rgba(34,197,94,1)',
                        'rgba(239,68,68,1)',
                    ],
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
    });

    
        document.addEventListener('DOMContentLoaded', function () {
        fetch("{{ route('coordenador.relatorios.grafico.participacao_programas') }}")
            .then(response => response.json())
            .then(data => {
                new Chart(document.getElementById('participacaoProgramasChart'), {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Participação',
                            data: data.dados,
                            backgroundColor: [
                                'rgba(34,197,94,0.7)',
                                'rgba(239,68,68,0.7)',
                            ],
                            borderColor: [
                                'rgba(34,197,94,1)',
                                'rgba(239,68,68,1)',
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            });
    });


        document.addEventListener("DOMContentLoaded", function () {
        fetch("{{ route('coordenador.relatorios.grafico.pcds_por_programa') }}")
            .then(res => res.json())
            .then(res => {
                new Chart(document.getElementById("pcdsPorProgramaChart"), {
                    type: "bar",
                    data: {
                        labels: res.labels,
                        datasets: [{
                            label: "Qtd. de PCDs",
                            data: res.dados,
                            backgroundColor: "rgba(34,197,94,0.6)",
                            borderColor: "rgba(34,197,94,1)",
                            borderWidth: 1
                        }]
                    },
                    options: {
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            });
    });
    
    //SEÇÃO PROGRAMAS

    // Média de Renda por Programa
    new Chart(document.getElementById('mediaRendaProgramaChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($mediaRendaPrograma->pluck('nome')) !!},
            datasets: [{
                label: 'Média de Renda',
                data: {!! json_encode($mediaRendaPrograma->pluck('media')) !!},
                backgroundColor: 'rgba(59,130,246,0.6)',
                borderColor: 'rgba(37,99,235,1)',
                borderWidth: 1
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    //Renda per capta

    new Chart(document.getElementById('mediaRendaPerCapitaChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($mediaRendaPerCapitaPorPrograma->pluck('nome')) !!},
            datasets: [{
                label: 'Renda Per Capita Média (R$)',
                data: {!! json_encode($mediaRendaPerCapitaPorPrograma->pluck('media_per_capita')) !!},
                backgroundColor: 'rgba(34,197,94,0.5)',
                borderColor: 'rgba(34,197,94,1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { position: 'bottom' } }
        }
    });



    // Evoluções por Programa
    new Chart(document.getElementById('evolucoesPorProgramaChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($evolucoesPorPrograma->pluck('nome')) !!},
            datasets: [{
                label: 'Evoluções',
                data: {!! json_encode($evolucoesPorPrograma->pluck('total')) !!},
                backgroundColor: 'rgba(34,197,94,0.6)',
                borderColor: 'rgba(22,163,74,1)',
                borderWidth: 1
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Distribuição por Região
    const distribuicaoPorRegiao = @json($distribuicaoPorRegiao);

    const ctx = document.getElementById('inscricoesPorRegiaoChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: distribuicaoPorRegiao.map(d => d.regiao),
            datasets: [{
                label: 'Inscrições',
                data: distribuicaoPorRegiao.map(d => d.total),
                backgroundColor: 'rgba(34,197,94,0.5)',
                borderColor: 'rgba(34,197,94,1)',
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Inscrições por Bairro
new Chart(document.getElementById('inscricoesPorBairroChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($distribuicaoPorBairro->pluck('bairro')) !!},
        datasets: [{
            label: 'Inscrições',
            data: {!! json_encode($distribuicaoPorBairro->pluck('total')) !!},
            backgroundColor: 'rgba(34,197,94,0.4)',
            borderColor: 'rgba(34,197,94,1)',
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            legend: { position: 'bottom' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

        // Gráfico de Status das Inscrições
        document.addEventListener('DOMContentLoaded', function () {
                const ctxStatus = document.getElementById('statusInscricoesChart').getContext('2d');

                new Chart(ctxStatus, {
                    type: 'pie',
                    data: {
                        labels: {!! json_encode($statusInscricoes->pluck('status')) !!},
                        datasets: [{
                            label: 'Status',
                            data: {!! json_encode($statusInscricoes->pluck('total')) !!},
                            backgroundColor: [
                                'rgba(34,197,94,0.6)',   // aprovado
                                'rgba(251,191,36,0.6)',  // em_analise
                                'rgba(239,68,68,0.6)',   // reprovado
                            ],
                            borderColor: 'rgba(255,255,255,0.9)',
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
            });

             // Gráfico de Aprovações por Programa
    document.addEventListener('DOMContentLoaded', function () {
        const ctxAprovacoes = document.getElementById('aprovacoesPorProgramaChart').getContext('2d');

        new Chart(ctxAprovacoes, {
            type: 'bar',
            data: {
                labels: {!! json_encode($aprovacoesPorPrograma->pluck('programa')) !!},
                datasets: [{
                    label: 'Aprovações',
                    data: {!! json_encode($aprovacoesPorPrograma->pluck('total')) !!},
                    backgroundColor: 'rgba(16,185,129,0.6)',
                    borderColor: 'rgba(5,150,105,1)',
                    borderWidth: 1
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });

    //GERAL

        // Horas de Plantão por Dia

        document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('horasPlantaoPorDiaChart').getContext('2d');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($horasPlantaoPorDia->pluck('data')) !!},
                        datasets: [{
                            label: 'Horas em Plantão',
                            data: {!! json_encode($horasPlantaoPorDia->pluck('total_horas')) !!},
                            backgroundColor: 'rgba(185,28,28,0.4)',
                            borderColor: 'rgba(185,28,28,1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        plugins: { legend: { position: 'bottom' } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            });

    // Emergências em Plantão
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('emergenciasDurantePlantaoChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($emergenciasDurantePlantao->pluck('data')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))) !!},
                datasets: [{
                    label: 'Emergências',
                    data: {!! json_encode($emergenciasDurantePlantao->pluck('total')) !!},
                    backgroundColor: 'rgba(239, 68, 68, 0.6)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#1f2937', // cinza escuro
                            font: { size: 14 }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#4b5563',
                            font: { size: 12 }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#4b5563',
                            font: { size: 12 },
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
  
    // Órgãos Mais Acessados
        document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('recebimentoEncaminhamentoChart').getContext('2d');

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Recebimentos', 'Encaminhamentos'],
                datasets: [{
                    data: [
                        {{ $comparativoRecebimentosEncaminhamentos['recebimento'] ?? 0 }},
                        {{ $comparativoRecebimentosEncaminhamentos['encaminhamento'] ?? 0 }}
                    ],
                    backgroundColor: ['rgba(34,197,94,0.6)', 'rgba(59,130,246,0.6)'],
                    borderColor: ['rgba(34,197,94,1)', 'rgba(59,130,246,1)'],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });

     // Recebimento e Órgãos

     document.addEventListener('DOMContentLoaded', function () {
        new Chart(document.getElementById('recebimentosPorOrgaoChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($recebimentosPorOrgao->pluck('orgao')) !!},
                datasets: [{
                    label: 'Total de Recebimentos',
                    data: {!! json_encode($recebimentosPorOrgao->pluck('total')) !!},
                    backgroundColor: 'rgba(34,197,94,0.5)',
                    borderColor: 'rgba(34,197,94,1)',
                    borderWidth: 1
                }]
            },
            options: {
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });

    // Encaminhamentos e Órgãos

    document.addEventListener('DOMContentLoaded', function () {
        new Chart(document.getElementById('encaminhamentosPorOrgaoChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($encaminhamentosPorOrgao->pluck('orgao')) !!},
                datasets: [{
                    label: 'Encaminhamentos',
                    data: {!! json_encode($encaminhamentosPorOrgao->pluck('total')) !!},
                    backgroundColor: 'rgba(59,130,246,0.5)',
                    borderColor: 'rgba(59,130,246,1)',
                    borderWidth: 1
                }]
            },
            options: {
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });


  

</script>

@endsection
