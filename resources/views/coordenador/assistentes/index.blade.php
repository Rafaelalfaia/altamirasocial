@extends('layouts.app')

@section('title', 'Assistentes Sociais')

@section('content')
    <div class="max-w-6xl mx-auto p-6 bg-white shadow rounded-lg">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-green-700">🧑‍⚕️ Equipe Técnica</h1>

            <a href="{{ route('coordenador.assistentes.create') }}"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition text-sm">
                ➕ Novo Assistente
            </a>
        </div>

        {{-- Caixa de Pesquisa --}}
        <form method="GET" action="{{ route('coordenador.assistentes.index') }}" class="mb-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar por nome ou CPF..."
                    class="w-full sm:w-80 px-4 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring focus:border-green-500">
                <button type="submit"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                    🔍 Buscar
                </button>
            </div>
        </form>

        {{-- Tabela --}}
        <div class="overflow-x-auto border rounded">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-700 font-semibold">
                    <tr>
                        <th class="py-2 px-4 text-left">Nome</th>
                        <th class="py-2 px-4 text-left">CPF</th>
                        <th class="py-2 px-4 text-left">E-mail</th>
                        <th class="py-2 px-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assistentes as $assistente)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-4">{{ $assistente->name }}</td>
                            <td class="py-2 px-4">{{ $assistente->cpf }}</td>
                            <td class="py-2 px-4">{{ $assistente->email ?? '-' }}</td>
                            <td class="py-2 px-4 text-right space-x-2">
                                <a href="{{ route('coordenador.assistentes.entrar', $assistente->id) }}"
                                    class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition text-xs">
                                    🔑 Entrar
                                </a>

                                <a href="{{ route('coordenador.assistentes.edit', $assistente->id) }}"
                                    class="bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700 transition text-xs">
                                    ✏️ Editar
                                </a>

                                <a href="{{ route('coordenador.assistentes.acompanhamentos', $assistente->id) }}"
                                    class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition text-xs">
                                    📋 Ver Acompanhamentos
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-500">
                                Nenhum assistente encontrado para este coordenador.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        <div class="mt-4">
            {{ $assistentes->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
