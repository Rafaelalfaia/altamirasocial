@extends('layouts.app')

@section('title', 'Lotes de Pagamento')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-green-800">📄 Lotes de Pagamento</h1>
            <a href="{{ route('coordenador.lotes.create') }}"
                class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded shadow transition">
                ➕ Novo Lote
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4 shadow">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Nome do Lote</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Programa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Região</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Período</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Data de Envio</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($lotes as $lote)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $lote->nome }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                {{ $lote->programa->nome ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                {{ $lote->regiao ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                {{ $lote->periodo_pagamento ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                {{ \Carbon\Carbon::parse($lote->data_envio)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('coordenador.lotes.edit', $lote) }}"
                                    class="text-indigo-600 hover:underline">Editar</a>
                                <a href="{{ route('coordenador.lotes.baixar', $lote) }}"
                                    class="text-green-700 hover:underline">📥 Baixar</a>
                                <form action="{{ route('coordenador.lotes.destroy', $lote) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline"
                                        onclick="return confirm('Excluir este lote?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-6 text-center text-gray-500">Nenhum lote encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
                
            </table>
        </div>

        <div class="mt-6">
            {{ $lotes->links() }}
        </div>
    </div>
@endsection
