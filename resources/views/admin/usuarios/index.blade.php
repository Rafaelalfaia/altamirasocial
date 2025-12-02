@extends('layouts.app')

@section('title', 'Gerenciar Usuários')

@if(session('success'))
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
        {{ session('error') }}
    </div>
@endif


@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-green-900">👤 Lista de Usuários</h1>
        <a href="{{ route('admin.usuarios.create') }}"
           class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded shadow">
            ➕ Novo Usuário
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <div>
            <label for="busca" class="block text-sm font-medium text-gray-700">Buscar</label>
            <input type="text" name="busca" id="busca" value="{{ request('busca') }}"
                   class="w-full rounded border-gray-300 shadow-sm text-sm">
        </div>
        <div>
            <label for="filtro_role" class="block text-sm font-medium text-gray-700">Perfil</label>
            <select name="filtro_role" id="filtro_role"
        class="w-full rounded border-gray-300 shadow-sm text-sm">
            <option value="">Todos</option>
            @foreach($todosRoles as $role)
                <option value="{{ $role }}" @selected(request('filtro_role') == $role)>
                    {{ ucfirst(str_replace('_', ' ', $role)) }}
                </option>
            @endforeach
        </select>

        </div>
        <div>
            <button type="submit"
                    class="w-full bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded shadow">
                🔍 Filtrar
            </button>
        </div>
    </form>

    {{-- Tabela --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-green-800 text-white">
                <tr>
                    <th class="px-4 py-2">Nome</th>
                    <th class="px-4 py-2">E-mail</th>
                    <th class="px-4 py-2">CPF</th>
                    <th class="px-4 py-2">Perfil</th>
                    <th class="px-4 py-2">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $usuario->name }}</td>
                        <td class="px-4 py-2">{{ $usuario->email }}</td>
                        <td class="px-4 py-2">{{ $usuario->cpf }}</td>
                        <td class="px-4 py-2">
                            {{ $usuario->getRoleNames()->first() ?? '---' }}
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.usuarios.edit', $usuario->id) }}"
                                class="text-green-700 hover:underline">Editar</a>

                                <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" method="POST"
                                    onsubmit="return confirm('Tem certeza que deseja apagar este usuário?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-700 hover:underline">Apagar</button>
                                </form>
                            </div>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center px-4 py-6 text-gray-500">Nenhum usuário encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    <div class="mt-6">
        {{ $usuarios->withQueryString()->links() }}
    </div>
</div>
@endsection
