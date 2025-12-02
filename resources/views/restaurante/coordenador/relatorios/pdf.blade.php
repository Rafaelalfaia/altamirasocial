<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #111827;
        }

        h1 {
            font-size: 18px;
            color: #111827;
            margin-bottom: 10px;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 6px;
            text-align: left;
            vertical-align: top;
            word-break: break-word;
        }

        th {
            background-color: #f3f4f6;
        }

        th:nth-child(1) { width: 80px; }    /* Data */
        th:nth-child(2) { width: 100px; }   /* Cliente */
        th:nth-child(3) { width: 90px; }    /* CPF */
        th:nth-child(4) { width: 60px; }    /* Tipo */
        th:nth-child(5) { width: 50px; }    /* Pratos */
        th:nth-child(6), th:nth-child(7) { width: 60px; }  /* Estudante, Doação */
        th:nth-child(8) { width: 70px; }    /* Consumo */
        th:nth-child(9) { width: 70px; }    /* Pagamento */
        th:nth-child(10) { width: 120px; }  /* Atendente */
    </style>
</head>
<body>

    <h1>{{ $titulo }}</h1>

    <p><strong>Restaurante:</strong> {{ $restaurante->nome ?? 'Todos' }}</p>
    <p><strong>Gerado por:</strong> {{ $usuario }}</p>
    <p><strong>Data/Hora:</strong> {{ $data_geracao }}</p>
    <p><strong>Período:</strong> {{ $data_inicial }} até {{ $data_final }}</p>

    <h2 class="mb-4">📊 Resumo</h2>
    <ul>
        <li>Total de Vendas: {{ $totalVendas }}</li>
        <li>Total de Pratos Servidos: {{ $totalPratos }}</li>
        <li>Clientes Normais: {{ $normais }}</li>
        <li>Clientes Temporários: {{ $temporarios }}</li>
        <li>Estudantes: {{ $estudantes }}</li>
        <li>Doações: {{ $doacoes }}</li>
        <li>Pagamentos por PIX: {{ $pix }}</li>
        <li>Pagamentos em Dinheiro: {{ $dinheiro }}</li>
        <li>Consumo no Local: {{ $local }}</li>
        <li>Consumo para Retirada: {{ $retirada }}</li>
    </ul>

    <h2 class="mb-4">📄 Detalhamento de Vendas</h2>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Cliente</th>
                <th>CPF</th>
                <th>Tipo</th>
                <th>Pratos</th>
                <th>Estudante</th>
                <th>Doação</th>
                <th>Consumo</th>
                <th>Pagamento</th>
                <th>Atendente</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($vendas as $venda)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($venda->data_venda)->format('d/m/Y H:i') }}</td>
                    <td>
                        {{ $venda->cidadao->nome ?? $venda->cidadaoTemporario->nome ?? 'N/D' }}
                    </td>
                    <td>
                        {{ $venda->cidadao->cpf ?? $venda->cidadaoTemporario->cpf ?? 'N/D' }}
                    </td>
                    <td>{{ ucfirst($venda->tipo_cliente) }}</td>
                    <td>{{ $venda->numero_pratos }}</td>
                    <td>{{ $venda->estudante ? 'Sim' : 'Não' }}</td>
                    <td>{{ $venda->doacao ? 'Sim' : 'Não' }}</td>
                    <td>{{ ucfirst($venda->tipo_consumo) }}</td>
                    <td>{{ ucfirst($venda->forma_pagamento) }}</td>
                    <td>{{ $venda->user->name ?? 'N/D' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2 class="mb-4">📋 Detalhamento por Atendente</h2>

    <table>
        <thead>
            <tr>
                <th>Atendente</th>
                <th>Restaurante</th>
                <th>Vendas</th>
                <th>Pratos</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dadosPorAtendente ?? [] as $at)
                <tr>
                    <td>{{ $at['atendente'] }}</td>
                    <td>{{ $at['restaurante'] }}</td>
                    <td>{{ $at['total_vendas'] }}</td>
                    <td>{{ $at['total_pratos'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
