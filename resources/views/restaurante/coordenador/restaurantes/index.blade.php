@extends('layouts.app')

@section('title', 'Restaurantes')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Cabeçalho --}}
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-green-800">🏢 Restaurantes</h1>
        <a href="{{ route('restaurante.coordenador.restaurantes.create') }}"
           class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 transition">
            ➕ Novo Restaurante
        </a>
    </div>

    {{-- Alerta de sucesso --}}
    @if (session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded shadow text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabela --}}
    <div class="bg-white shadow rounded overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-green-800 text-white">
                <tr>
                    <th class="px-4 py-2">Nome</th>
                    <th class="px-4 py-2">Endereço</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2 text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($restaurantes as $restaurante)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $restaurante->nome }}</td>
                        <td class="px-4 py-2">{{ $restaurante->endereco ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-block px-2 py-1 rounded text-xs font-medium {{ $restaurante->ativo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $restaurante->ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-right space-x-2">
                            <a href="{{ route('restaurante.coordenador.restaurantes.edit', $restaurante) }}"
                               class="text-blue-600 hover:underline text-sm">✏️ Editar</a>

                            <form action="{{ route('restaurante.coordenador.restaurantes.destroy', $restaurante) }}"
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Tem certeza que deseja excluir este restaurante?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">
                                    🗑️ Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                            Nenhum restaurante encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    <div>
        {{ $restaurantes->links() }}
    </div>

</div>
@endsection
