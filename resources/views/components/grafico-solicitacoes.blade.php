<div class="bg-white rounded-xl shadow-lg p-4 w-full max-w-md mx-auto">
    <div class="flex justify-between items-center mb-4">
        <div>
            <p class="text-sm text-gray-500 font-semibold">Solicitações</p>
            <h3 class="text-2xl font-bold text-green-700">Mensal</h3>
        </div>

        <div class="flex space-x-2">
            <button
                id="btn-cidadao"
                onclick="atualizarSolicitacoes('cidadao')"
                class="px-3 py-1 rounded text-sm font-semibold bg-green-600 text-white hover:bg-green-700 focus:outline-none"
            >
                Cidadão
            </button>
            <button
                id="btn-assistente"
                onclick="atualizarSolicitacoes('assistente')"
                class="px-3 py-1 rounded text-sm font-semibold bg-green-100 text-green-700 hover:bg-green-200 focus:outline-none"
            >
                Assistente
            </button>
        </div>
    </div>

    {{-- Aqui está o FIX: altura definida via Tailwind --}}
    <div class="h-64">
        <canvas id="graficoSolicitacoes"></canvas>
    </div>
</div>


<script>
    const dadosSolicitacoes = @json($dadosSolicitacoes);
    const ctxSolicitacoes = document.getElementById('graficoSolicitacoes').getContext('2d');

    let graficoSolicitacoes = new Chart(ctxSolicitacoes, {
        type: 'line',
        data: {
            labels: ['JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'OUT', 'NOV', 'DEZ'],
            datasets: [{
                label: 'Solicitações',
                data: dadosSolicitacoes.cidadao,
                borderColor: '#16a34a',
                backgroundColor: '#16a34a',
                pointBackgroundColor: '#16a34a',
                tension: 0.4,
                fill: false,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#4b5563' } },
                y: { ticks: { color: '#4b5563' } }
            }
        }
    });

    function atualizarSolicitacoes(tipo) {
        // Atualiza dados
        graficoSolicitacoes.data.datasets[0].data = dadosSolicitacoes[tipo];
        graficoSolicitacoes.update();

        // Estilo visual dos botões
        document.getElementById('btn-cidadao').classList.remove('bg-green-600', 'text-white');
        document.getElementById('btn-assistente').classList.remove('bg-green-600', 'text-white');

        document.getElementById('btn-cidadao').classList.add('bg-green-100', 'text-green-700');
        document.getElementById('btn-assistente').classList.add('bg-green-100', 'text-green-700');

        if (tipo === 'cidadao') {
            document.getElementById('btn-cidadao').classList.remove('bg-green-100', 'text-green-700');
            document.getElementById('btn-cidadao').classList.add('bg-green-600', 'text-white');
        } else {
            document.getElementById('btn-assistente').classList.remove('bg-green-100', 'text-green-700');
            document.getElementById('btn-assistente').classList.add('bg-green-600', 'text-white');
        }
    }
</script>
