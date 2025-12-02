@extends('layouts.app')

@section('title', '🏠 Dashboard do Atendente')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- Cabeçalho --}}
    <div class="bg-white p-6 rounded shadow flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h1 class="text-2xl font-bold text-green-800">🏠 Dashboard</h1>
            <p class="text-sm text-gray-600">
                Bem-vindo, <strong>{{ $usuario->name }}</strong>. Você está operando no restaurante:
                <strong class="text-blue-700">{{ $restaurante->nome }}</strong>.
            </p>
        </div>
    </div>

    {{-- Loop de períodos --}}
    @foreach ([
        ['titulo' => 'Hoje', 'dados' => $metricasHoje],
        ['titulo' => 'Semana', 'dados' => $metricasSemana],
        ['titulo' => 'Mês', 'dados' => $metricasMes],
    ] as $bloco)
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">📅 Métricas do {{ $bloco['titulo'] }}</h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 text-sm text-gray-800">
            <div class="bg-gray-100 p-4 rounded shadow-sm">
                <strong>Total de Vendas:</strong><br>{{ $bloco['dados']['total_vendas'] }}
            </div>
            <div class="bg-gray-100 p-4 rounded shadow-sm">
                <strong>Pratos Servidos:</strong><br>{{ $bloco['dados']['total_pratos'] }}
            </div>
            <div class="bg-gray-100 p-4 rounded shadow-sm">
                <strong>Clientes Normais:</strong><br>{{ $bloco['dados']['clientes_normais'] }}
            </div>
            <div class="bg-gray-100 p-4 rounded shadow-sm">
                <strong>Temporários:</strong><br>{{ $bloco['dados']['clientes_temporarios'] }}
            </div>
            <div class="bg-gray-100 p-4 rounded shadow-sm">
                <strong>Pagamento PIX:</strong><br>{{ $bloco['dados']['pagamento_pix'] }}
            </div>
            <div class="bg-gray-100 p-4 rounded shadow-sm">
                <strong>Pagamento Dinheiro:</strong><br>{{ $bloco['dados']['pagamento_dinheiro'] }}
            </div>
            <div class="bg-gray-100 p-4 rounded shadow-sm">
                <strong>Cartão Crédito:</strong><br>{{ $bloco['dados']['pagamento_credito'] }}
            </div>
            <div class="bg-gray-100 p-4 rounded shadow-sm">
                <strong>Cartão Débito:</strong><br>{{ $bloco['dados']['pagamento_debito'] }}
            </div>
        </div>
    </div>
    @endforeach

</div>
@endsection
