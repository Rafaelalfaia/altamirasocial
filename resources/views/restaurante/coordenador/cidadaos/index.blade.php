@extends('layouts.app')

@section('title', 'Lista de Cidadãos')

@section('content')
    <div class="max-w-6xl mx-auto p-6 bg-white shadow rounded">

        {{-- Cabeçalho com botão --}}
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-green-700">👥 Lista de Cidadãos</h1>

            <a href="{{ route('restaurante.coordenador.cidadaos.create') }}"
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition text-sm">
                ➕ Criar Cidadão
            </a>
        </div>

        {{-- Caixa de Pesquisa --}}
        <form method="GET" action="{{ route('restaurante.coordenador.cidadaos.index') }}" class="mb-4">
            <div class="flex items-center gap-2">
                <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar por nome ou CPF..."
                       class="w-full sm:w-80 px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-green-500 text-sm">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                    🔍 Buscar
                </button>
            </div>
        </form>

        {{-- Tabela --}}
        <table class="min-w-full bg-white text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th class="py-2 px-4">Nome</th>
                    <th class="py-2 px-4">CPF</th>
                    <th class="py-2 px-4">Telefone</th>
                    <th class="py-2 px-4">E-mail</th>
                    <th class="py-2 px-4 text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cidadaos as $cidadao)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-4">{{ $cidadao->nome }}</td>
                        <td class="py-2 px-4">
                            {{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cidadao->cpf) }}
                        </td>

                        <td class="py-2 px-4">{{ $cidadao->user->telefone ?? '-' }}</td>
                        <td class="py-2 px-4">{{ $cidadao->user->email ?? '-' }}</td>
                        <td class="py-2 px-4 text-right space-x-2">
                            <a href="{{ route('restaurante.coordenador.cidadaos.edit', $cidadao->id) }}"
                               class="text-blue-600 hover:underline">✏️ Editar</a>

                            <form action="{{ route('restaurante.coordenador.cidadaos.destroy', $cidadao->id) }}"
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Tem certeza que deseja excluir este cidadão?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">🗑️ Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 px-4 text-center text-gray-500">Nenhum cidadão encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginação --}}
        <div class="mt-4">
            {{ $cidadaos->links() }}
        </div>
    </div>
@endsection
