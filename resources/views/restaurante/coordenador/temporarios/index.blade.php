@extends('layouts.app')

@section('title', 'Cidadãos Temporários')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-green-800">🕒 Cidadãos Temporários</h1>

        <a href="{{ route('restaurante.coordenador.temporarios.create') }}"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm shadow">
            + Novo Temporário
        </a>
    </div>

    {{-- Campo de busca --}}
    <form method="GET" class="mb-4">
        <input type="text" name="search" value="{{ $search }}"
               placeholder="Buscar por nome ou motivo"
               class="px-4 py-2 border rounded w-full md:max-w-sm text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </form>

    {{-- Tabela --}}
    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-100 text-gray-600">
                <tr>
                    <th class="px-4 py-2 text-left">Nome</th>
                    <th class="px-4 py-2 text-left">Motivo</th>
                    <th class="px-4 py-2 text-left">Criado por</th>
                    <th class="px-4 py-2 text-left">Criado em</th>
                    <th class="px-4 py-2 text-left">Validade</th>
                    <th class="px-4 py-2 text-left">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @forelse ($cidadaos as $cidadao)
                    <tr>
                        <td class="px-4 py-2">{{ $cidadao->nome }}</td>
                        <td class="px-4 py-2">{{ $cidadao->motivo ?? '—' }}</td>
                        <td class="px-4 py-2">{{ $cidadao->user->name ?? 'Não informado' }}</td>
                        <td class="px-4 py-2">{{ $cidadao->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($cidadao->fim_validez)->format('d/m/Y') }}</td>

                        <td class="px-4 py-2">
                        <div class="flex flex-col md:flex-row md:items-center gap-2">

                            {{-- Editar --}}
                            <a href="{{ route('restaurante.coordenador.temporarios.edit', $cidadao->id) }}"
                            class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 shadow transition">
                                ✏️ Editar
                            </a>

                            {{-- Renovar --}}
                            <form method="POST"
                                action="{{ route('restaurante.coordenador.temporarios.renovar', $cidadao->id) }}"
                                class="inline-flex items-center gap-2">
                                @csrf
                                @method('PATCH')

                                <select name="prazo"
                                        class="text-xs border-gray-300 rounded px-1.5 py-1 shadow-sm focus:ring-green-500 focus:border-green-500">
                                    <option value="7">+7d</option>
                                    <option value="15">+15d</option>
                                    <option value="30" selected>+30d</option>
                                    <option value="60">+60d</option>
                                </select>

                                <button type="submit"
                                        onclick="return confirm('Deseja renovar a validade por esse prazo?')"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs rounded hover:bg-green-700 shadow transition">
                                    ♻️ Renovar
                                </button>
                            </form>

                            {{-- Apagar --}}
                            <form method="POST"
                                action="{{ route('restaurante.coordenador.temporarios.destroy', $cidadao->id) }}"
                                onsubmit="return confirm('Tem certeza que deseja remover este cidadão temporário?')"
                                class="inline">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs rounded hover:bg-red-700 shadow transition">
                                    🗑️ Apagar
                                </button>
                            </form>

                        </div>
                    </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            Nenhum cidadão temporário encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    <div class="mt-4">
        {{ $cidadaos->appends(['search' => $search])->links() }}
    </div>

</div>
@endsection
