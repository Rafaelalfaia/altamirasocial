@extends('layouts.app')

@section('title', 'Dashboard Coordenador')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">

    <h1 class="text-2xl font-bold text-green-800">🍽️ Dashboard do Coordenador</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Restaurantes --}}
        <div class="bg-white shadow rounded p-4">
            <p class="text-gray-500 text-sm">Restaurantes</p>
            <p class="text-2xl font-bold text-green-700">{{ $restaurantes->count() }}</p>
        </div>

        {{-- Atendentes --}}
        <div class="bg-white shadow rounded p-4">
            <p class="text-gray-500 text-sm">Atendentes Vinculados</p>
            <p class="text-2xl font-bold text-green-700">{{ $atendentes->count() }}</p>
        </div>

        {{--  --}}
        <div class="bg-white shadow rounded p-4">
            <p class="text-gray-500 text-sm">Cidadãos Atendidos</p>
            <p class="text-2xl font-bold text-green-700">{{ $cidadaos }}</p>
        </div>

        {{-- Temporários --}}
        <div class="bg-white shadow rounded p-4">
            <p class="text-gray-500 text-sm">Temporários Atendidos</p>
            <p class="text-2xl font-bold text-green-700">{{ $temporarios }}</p>
        </div>

        {{-- Total de Vendas --}}
        <div class="bg-white shadow rounded p-4">
            <p class="text-gray-500 text-sm">Total de Vendas</p>
            <p class="text-2xl font-bold text-green-700">{{ $totalVendas }}</p>
        </div>

        {{-- Vendas do Mês --}}
        <div class="bg-white shadow rounded p-4">
            <p class="text-gray-500 text-sm">Vendas no Mês</p>
            <p class="text-2xl font-bold text-green-700">{{ $vendasDoMes }}</p>
        </div>

        {{-- Total Arrecadado --}}
        <div class="bg-white shadow rounded p-4">
            <p class="text-gray-500 text-sm">Total Arrecadado</p>
            <p class="text-2xl font-bold text-green-700">R$ {{ number_format($totalReais, 2, ',', '.') }}</p>
        </div>

        {{-- Total de Pratos --}}
        <div class="bg-white shadow rounded p-4">
            <p class="text-gray-500 text-sm">Pratos Servidos</p>
            <p class="text-2xl font-bold text-green-700">{{ $totalPratos }}</p>
        </div>

    </div>
</div>
@endsection
