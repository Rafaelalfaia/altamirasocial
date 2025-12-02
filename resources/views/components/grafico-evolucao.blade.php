<div class="bg-white p-6 rounded-2xl shadow w-full">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h3 class="text-sm uppercase text-gray-500 tracking-wide font-medium">Evolução Mensal</h3>
            <h2 class="text-2xl font-bold text-green-800">📈 Gráfico de Registros</h2>
        </div>

        {{-- Botões de seleção --}}
        <div class="flex flex-wrap gap-2 justify-start md:justify-end">
            <button id="btn-cidadaos" onclick="atualizarGrafico('cidadaos')" class="botao-grafico bg-green-700 text-white font-bold ring-2 ring-green-300" data-label="👤 Cidadãos">
                👤 <span class="hidden sm:inline">Cidadãos</span>
            </button>
            <button id="btn-assistentes" onclick="atualizarGrafico('assistentes')" class="botao-grafico" data-label="🧑‍💼 Assistentes">
                🧑‍💼 <span class="hidden sm:inline">Assistentes</span>
            </button>
            <button id="btn-programas" onclick="atualizarGrafico('programas')" class="botao-grafico" data-label="📚 Programas">
                📚 <span class="hidden sm:inline">Programas</span>
            </button>
        </div>
    </div>

    <div class="w-full overflow-x-auto">
        <div class="min-w-[500px] h-[300px]">
            <canvas id="graficoEvolucao"></canvas>
        </div>
    </div>
</div>

<style>
    .botao-grafico {
        @apply px-4 py-2 text-sm font-medium rounded-full border border-green-600 bg-white text-green-700 transition duration-200 shadow-sm hover:bg-green-100 hover:shadow-md focus:outline-none;
    }

    .botao-grafico span {
        @apply transition-colors;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dadosMensais = @json($dadosMensais);
        const ctx = document.getElementById('graficoEvolucao').getContext('2d');

        const grafico = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                datasets: [{
                    label: '👤 Cidadãos',
                    data: dadosMensais.cidadaos,
                    borderColor: '#15803d',
                    backgroundColor: '#15803d',
                    fill: false,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#15803d',
                    pointHoverBackgroundColor: '#16a34a',
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: '#374151',
                            font: {
                                size: 12,
                                family: 'sans-serif'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#15803d',
                        titleFont: { weight: 'bold' },
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderWidth: 1,
                        borderColor: '#14532d',
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#4b5563' },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#4b5563' },
                        grid: { color: '#e5e7eb' }
                    }
                }
            }
        });

        window.atualizarGrafico = function(tipo) {
            grafico.data.datasets[0].data = dadosMensais[tipo];
            grafico.data.datasets[0].label = document.querySelector(`#btn-${tipo}`).getAttribute('data-label');
            grafico.update();

            ['cidadaos', 'assistentes', 'programas'].forEach(id => {
                const btn = document.getElementById(`btn-${id}`);
                btn.classList.remove('bg-green-700', 'text-white', 'ring-2', 'ring-green-300', 'font-bold');
                btn.classList.add('bg-white', 'text-green-700');
            });

            const ativo = document.getElementById(`btn-${tipo}`);
            ativo.classList.remove('bg-white', 'text-green-700');
            ativo.classList.add('bg-green-700', 'text-white', 'ring-2', 'ring-green-300', 'font-bold');
        }
    });
</script>
