@extends('layouts.app')

@section('title', 'Coordenadores')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-green-800">👩‍🏫 Coordenadores</h1>
    </div>

    {{-- Filtro de busca --}}
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-4 rounded shadow">
        <input type="text" name="busca" value="{{ request('busca') }}"
               placeholder="Buscar nome, e-mail, CPF ou telefone"
               class="border-gray-300 rounded shadow-sm w-full text-sm md:col-span-3">

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            🔍 Buscar
        </button>
    </form>

    {{-- Tabela --}}
    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="w-full text-sm text-left">
            <thead class="bg-green-800 text-white">
                <tr>
                    <th class="px-4 py-2">Nome</th>
                    <th class="px-4 py-2">E-mail</th>
                    <th class="px-4 py-2">CPF</th>
                    <th class="px-4 py-2">Telefone</th>
                    <th class="px-4 py-2 text-center">Ação</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($coordenadores as $coord)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $coord->name }}</td>
                        <td class="px-4 py-2">{{ $coord->email }}</td>
                        <td class="px-4 py-2">{{ $coord->cpf }}</td>
                        <td class="px-4 py-2">{{ $coord->telefone ?? '-' }}</td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('admin.coordenadores.entrar', $coord->id) }}"
                               class="text-green-700 hover:underline">➡ Entrar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center px-4 py-6 text-gray-500">Nenhum coordenador encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    <div class="mt-4">
        {{ $coordenadores->withQueryString()->links() }}
    </div>
</div>
@endsection
