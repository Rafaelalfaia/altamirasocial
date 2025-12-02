<div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-auto">
    <div class="flex items-center justify-between mb-5">
        <div>
            <p class="text-xs uppercase font-semibold text-gray-400 tracking-wide">Relatório Mensal</p>
            <h2 class="text-xl font-bold text-red-600 mt-1 flex items-center">
                🚨 Ocorrências Emergenciais
            </h2>
        </div>
    </div>

    <div class="h-64">
        <canvas id="graficoEmergencias"></canvas>
    </div>
</div>

<script>
    const dadosEmergencias = @json($dadosEmergencias);

    const ctxEmergencias = document.getElementById('graficoEmergencias').getContext('2d');

    new Chart(ctxEmergencias, {
        type: 'bar',
        data: {
            labels: ['JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'OUT', 'NOV', 'DEZ'],
            datasets: [{
                label: 'Ocorrências',
                data: dadosEmergencias,
                backgroundColor: '#b91c1c',
                borderRadius: 8,
                barThickness: 14
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fef2f2',
                    titleColor: '#b91c1c',
                    bodyColor: '#1e293b',
                    borderColor: '#fecaca',
                    borderWidth: 1
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: '#475569',
                        font: { weight: '500', size: 12, family: 'Inter, sans-serif' },
                        padding: 6
                    },
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#64748b',
                        font: { weight: '500', size: 12, family: 'Inter, sans-serif' },
                        padding: 6
                    },
                    grid: { color: '#f3f4f6' }
                }
            }
        }
    });
</script>
