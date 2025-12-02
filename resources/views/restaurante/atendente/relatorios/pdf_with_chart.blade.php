<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background-color: #f3f4f6; }
    </style>
</head>
<body>
    <h1>{{ $titulo }}</h1>
    <p><strong>Atendente:</strong> {{ $usuario }}</p>
    <p><strong>Gerado em:</strong> {{ $data_geracao }}</p>
    <p><strong>Restaurante:</strong> {{ $restaurante }}</p>


    <h3>Métricas</h3>
    <ul>
        <li>Total de Vendas: {{ $totalVendas }}</li>
        <li>Total de Pratos: {{ $totalPratos }}</li>
        <li>Clientes Normais: {{ $normais }}</li>
        <li>Temporários: {{ $temporarios }}</li>
    </ul>


    <h3>Vendas</h3>
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Cliente</th>
                <th>CPF</th>
                <th>Tipo</th>
                <th>Pratos</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($vendas as $venda)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($venda->data_venda)->format('d/m/Y H:i') }}</td>
                    <td>{{ $venda->cidadao->nome ?? $venda->cidadaoTemporario->nome }}</td>
                    <td>{{ $venda->cidadao->cpf ?? $venda->cidadaoTemporario->cpf }}</td>
                    <td>{{ ucfirst($venda->tipo_cliente) }}</td>
                    <td>{{ $venda->numero_pratos }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
