@extends('layouts.app')

@section('title', 'Vendas do Restaurante')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10 space-y-6">
    <h1 class="text-2xl font-bold text-green-800">🍽️ Vendas do Restaurante</h1>

    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-100 text-gray-700 text-left">
                <tr>
                    <th class="px-4 py-3">Data</th>
                    <th class="px-4 py-3">Cidadão</th>
                    <th class="px-4 py-3">Pratos</th>
                    <th class="px-4 py-3">Tipo</th>
                    <th class="px-4 py-3">Pagamento</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($vendas as $venda)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $venda->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2">{{ $venda->cidadao->nome ?? '—' }}</td>
                        <td class="px-4 py-2">{{ $venda->quantidade_pratos }}</td>
                        <td class="px-4 py-2 capitalize">{{ $venda->tipo_consumo }}</td>
                        <td class="px-4 py-2">{{ ucfirst($venda->forma_pagamento) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-gray-500 text-center">Nenhuma venda registrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $vendas->links() }}
    </div>
</div>
@endsection
