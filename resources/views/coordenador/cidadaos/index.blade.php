@extends('layouts.app')

@section('title', 'Lista de Cidadãos')

@section('content')
    <div class="max-w-6xl mx-auto p-6 bg-white shadow rounded">

        {{-- Cabeçalho com botão --}}
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-green-700">👥 Lista de Cidadãos</h1>

            <a href="{{ route('coordenador.cidadaos.create') }}"
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition text-sm">
                ➕ Criar Cidadão
            </a>
        </div>

        {{-- Caixa de Pesquisa --}}
        <form method="GET" action="{{ route('coordenador.cidadaos.index') }}" class="mb-4">
        <div class="flex items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nome ou CPF..."
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
                @foreach ($cidadaos as $cidadao)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-4">{{ $cidadao->nome }}</td>
                        <td class="py-2 px-4">{{ $cidadao->cpf }}</td>
                        <td class="py-2 px-4">{{ $cidadao->user?->telefone ?? '-' }}</td>
                        <td class="py-2 px-4">{{ $cidadao->user?->email ?? '-' }}</td>
                        <td class="py-2 px-4">
                            <div class="flex items-center justify-end gap-2 flex-wrap">
                                {{-- manter Entrar --}}
                                <a href="{{ route('coordenador.cidadaos.entrar', $cidadao->id) }}"
                                   class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">
                                    🔑 Entrar
                                </a>

                                {{-- novo: Editar (apenas Nome/CPF/E-mail/Senha) --}}
                                <a href="{{ route('coordenador.cidadaos.edit', $cidadao->id) }}"
                                   class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 transition">
                                    ✏️ Editar
                                </a>

                                {{-- novo: Excluir (com confirmação) --}}
                                <form action="{{ route('coordenador.cidadaos.destroy', $cidadao->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Confirmar exclusão deste cidadão?');"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition">
                                        🗑️ Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Paginação (preserva a busca) --}}
        <div class="mt-4">
            {{ $cidadaos->withQueryString()->links() }}
        </div>
    </div>
@endsection
