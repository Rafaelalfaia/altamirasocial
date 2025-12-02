@extends('layouts.app')

@section('title', 'Cidadãos')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-2xl font-bold text-green-800 mb-6">👤 Cidadãos Cadastrados</h1>

    <form method="GET" class="mb-6">
        <input type="text" name="busca" value="{{ $busca }}" placeholder="Buscar por nome ou email"
            class="px-4 py-2 border rounded w-full max-w-md">
    </form>

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-green-800 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Telefone</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($cidadaos as $cidadao)
                    <tr>
                        <td class="px-6 py-4">{{ $cidadao->user->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $cidadao->user->telefone ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.cidadaos.entrar', $cidadao->user->id) }}"
                                class="bg-green-600 text-white px-3 py-1.5 rounded hover:bg-green-700 transition">
                                👁 Entrar
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Nenhum cidadão encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $cidadaos->links() }}
    </div>
</div>
@endsection
