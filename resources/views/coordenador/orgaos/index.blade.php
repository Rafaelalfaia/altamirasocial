@extends('layouts.app')

@section('title', 'Órgãos Públicos')

@section('content')
<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-green-700 mb-6">🏛️ Órgãos Públicos</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between mb-4">
        <a href="{{ route('coordenador.orgaos.create') }}"
           class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            ➕ Cadastrar Novo Órgão
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 font-medium text-gray-700">Nome</th>
                    <th class="px-4 py-2 font-medium text-gray-700">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orgaos as $orgao)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $orgao->nome }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('coordenador.orgaos.edit', $orgao) }}" class="text-yellow-600 hover:underline">Editar</a>
                            |
                            <form action="{{ route('coordenador.orgaos.destroy', $orgao) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Tem certeza que deseja excluir este órgão?')">
                                    Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-4 text-center text-gray-500">Nenhum órgão cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection