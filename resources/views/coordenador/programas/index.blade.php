@extends('layouts.app')

@section('title', 'Programas Sociais')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-indigo-700">📋 Programas Sociais</h1>
        <a href="{{ route('coordenador.programas.create') }}"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow text-sm">
            + Novo Programa
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 font-semibold">
                <tr>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3 text-center">Vagas</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Tipo</th> <!-- NOVA COLUNA -->
                    <th class="px-4 py-3 text-center">Inscrições</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($programas as $programa)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-indigo-700">
                            {{ $programa->nome }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            {{ $programa->vagas }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($programa->status === 'ativado')
                                <span class="inline-block px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                    Ativado
                                </span>
                            @else
                                <span class="inline-block px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                    Desativado
                                </span>
                            @endif
                        </td>

                        {{-- NOVA COLUNA: tipo de inscrição --}}
                        <td class="px-4 py-3 text-center">
                            @if ($programa->aceita_menores)
                                <span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                    🧒 Dependentes
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                    🧍‍♂️ Cidadão
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-center space-x-1">
                            <a href="{{ route('coordenador.programas.inscritos', ['programa' => $programa->id, 'status' => 'aprovado']) }}"
                                class="text-green-600 hover:underline text-xs">Aprovados</a>
                            <a href="{{ route('coordenador.programas.inscritos', ['programa' => $programa->id, 'status' => 'pendente']) }}"
                                class="text-yellow-600 hover:underline text-xs">Pendentes</a>
                            <a href="{{ route('coordenador.programas.inscritos', ['programa' => $programa->id, 'status' => 'reprovado']) }}"
                                class="text-red-600 hover:underline text-xs">Reprovados</a>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('coordenador.programas.edit', $programa) }}"
                                class="text-blue-600 hover:underline text-sm">Editar</a>
                            <form action="{{ route('coordenador.programas.destroy', $programa) }}" method="POST"
                                class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir este programa?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">
                                    Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">Nenhum programa cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    {{-- Paginação --}}
    <div class="mt-6">
        {{ $programas->links() }}
    </div>
@endsection
