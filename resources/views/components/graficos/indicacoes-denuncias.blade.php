<div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-auto">
    <div class="flex items-center justify-between mb-5">
        <div>
            <p class="text-xs uppercase font-semibold text-gray-400 tracking-wide">Indicadores do Ano</p>
            <h2 class="text-xl font-bold text-green-700 mt-1 flex items-center">
                📊 Indicações & Denúncias
            </h2>
        </div>
    </div>

    <div class="h-64">
        <canvas id="graficoIndDen"></canvas>
    </div>
</div>

<script>
    const dadosIndDen = @json($dadosIndicacoesDenuncias);

    const ctxIndDen = document.getElementById('graficoIndDen').getContext('2d');

    new Chart(ctxIndDen, {
        type: 'bar',
        data: {
            labels: ['JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'OUT', 'NOV', 'DEZ'],
            datasets: [
                {
                    label: 'Indicações',
                    data: dadosIndDen.indicacoes,
                    backgroundColor: '#16a34a', // verde intenso
                    borderRadius: 8,
                    barThickness: 14
                },
                {
                    label: 'Denúncias',
                    data: dadosIndDen.denuncias,
                    backgroundColor: '#dc2626', // vermelho intenso
                    borderRadius: 8,
                    barThickness: 14
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: 10,
                    bottom: 10,
                    left: 0,
                    right: 0
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#1e293b', // Slate-800
                        font: {
                            size: 12,
                            weight: '600',
                            family: 'Inter, sans-serif'
                        },
                        boxWidth: 12
                    }
                },
                tooltip: {
                    backgroundColor: '#f9fafb',
                    titleColor: '#1e293b',
                    bodyColor: '#1e293b',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    titleFont: {
                        weight: 'bold'
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: '#475569', // Slate-600
                        font: {
                            weight: '500',
                            size: 12,
                            family: 'Inter, sans-serif'
                        },
                        padding: 6
                    },
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#64748b', // Slate-500
                        font: {
                            weight: '500',
                            size: 12,
                            family: 'Inter, sans-serif'
                        },
                        padding: 6
                    },
                    grid: {
                        color: '#e5e7eb' // Gray-200
                    }
                }
            }
        }
    });
</script>
