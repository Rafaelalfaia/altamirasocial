@props(['cidadao'])

<div class="bg-white p-4 rounded-xl shadow-md border flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="text-lg font-semibold text-gray-800">{{ $cidadao->nome }}</h2>
        <p class="text-sm text-gray-500">
            Tipo: {{ $cidadao->tipo_cliente === 'temporario' ? 'Temporário' : 'Cidadão Regular' }}
        </p>

        @if($cidadao->tipo_cliente === 'temporario' && isset($cidadao->validade))
            <p class="text-sm text-red-500">
                Validade até: {{ \Carbon\Carbon::parse($cidadao->validade)->format('d/m/Y') }}
                ({{ \Carbon\Carbon::parse($cidadao->validade)->diffForHumans() }})
            </p>
        @endif
    </div>

    {{-- Formulário de venda --}}
    <form action="{{ route('restaurante.vendas.store') }}" method="POST" class="flex flex-col md:flex-row gap-3 md:items-center">
        @csrf
        <input type="hidden" name="tipo_cliente" value="{{ $cidadao->tipo_cliente }}">
        <input type="hidden" name="cidadao_id" value="{{ $cidadao->id }}">

        {{-- Número de pratos --}}
        <input type="number" name="numero_pratos" value="1" min="1"
               class="w-24 px-3 py-1.5 rounded border border-gray-300 text-sm" required>

        {{-- Tipo de consumo --}}
        <select name="tipo_consumo" class="px-3 py-1.5 rounded border border-gray-300 text-sm" required>
            <option value="local">Local</option>
            <option value="retirada">Retirada</option>
        </select>

        {{-- Forma de pagamento --}}
        @if($cidadao->tipo_cliente === 'temporario')
            <input type="hidden" name="forma_pagamento" value="doacao">
            <span class="text-sm text-gray-500 italic">Forma: Doação</span>
        @else
            <select name="forma_pagamento" class="px-3 py-1.5 rounded border border-gray-300 text-sm" required>
                <option value="pix">Pix</option>
                <option value="debito">Débito</option>
                <option value="credito">Crédito</option>
                <option value="dinheiro">Dinheiro</option>
            </select>
        @endif

        {{-- Checkbox de Estudante --}}
        <label class="inline-flex items-center text-sm text-gray-700">
            <input type="checkbox" name="estudante" value="1" class="mr-2 rounded border-gray-300">
            Estudante
        </label>

        {{-- Botão de vender --}}
        <button type="submit"
                class="bg-green-700 text-white text-sm px-4 py-1.5 rounded shadow hover:bg-green-800 transition">
            💰 Vender
        </button>
    </form>
</div>
