{{-- resources/views/coordenador/recebimentos/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Recebimentos e Encaminhamentos')

@section('content')
<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-green-700 mb-6">📋 Recebimentos e Encaminhamentos</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-wrap gap-4 mb-4">
        <a href="{{ route('coordenador.recebimentos.create') }}"
           class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            ➕ Novo Registro
        </a>

        <a href="{{ route('coordenador.orgaos.index') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            🏛️ Gerenciar Órgãos Públicos
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 font-medium text-gray-700">Tipo</th>
                    <th class="px-4 py-2 font-medium text-gray-700">Cidadão</th>
                    <th class="px-4 py-2 font-medium text-gray-700">Órgão</th>
                    <th class="px-4 py-2 font-medium text-gray-700">Programa</th>
                    <th class="px-4 py-2 font-medium text-gray-700">Data</th>
                    <th class="px-4 py-2 font-medium text-gray-700">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($registros as $item)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ ucfirst($item->tipo) }}</td>
                        <td class="px-4 py-2">{{ $item->nome_cidadao }}</td>
                        <td class="px-4 py-2">{{ $item->orgao->nome }}</td>
                        <td class="px-4 py-2">{{ $item->programa->nome ?? '—' }}</td>
                        <td class="px-4 py-2">{{ $item->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('coordenador.recebimentos.show', $item) }}" class="text-blue-600 hover:underline">Ver</a>
                            |
                            <a href="{{ route('coordenador.recebimentos.edit', $item) }}" class="text-yellow-600 hover:underline">Editar</a>
                            |
                            <form action="{{ route('coordenador.recebimentos.destroy', $item) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Tem certeza que deseja excluir este registro?')">
                                    Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">Nenhum registro encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
