@extends('layouts.app')

@section('title', 'Cidadãos Atendidos')

@section('content')
    <div class="max-w-7xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold text-green-700 mb-4">👥 Cidadãos Atendidos</h1>

        <a href="{{ route('assistente.cidadao.criar') }}"
            class="bg-green-700 text-white px-4 py-2 rounded shadow hover:bg-green-800 transition text-sm">
            ➕ Novo Cidadão
        </a>

    </div>


    {{-- Filtros --}}
    <form method="GET" action="{{ route('assistente.usuarios.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">

        <input
            type="text"
            name="nome"
            value="{{ request('nome') }}"
            placeholder="🔍 Nome"
            class="border border-gray-300 rounded px-3 py-2"
        >

        <input
            type="text"
            name="cpf"
            value="{{ request('cpf') }}"
            placeholder=" CPF"
            inputmode="numeric"
            autocomplete="off"
            class="border border-gray-300 rounded px-3 py-2"
        >

        <select name="bairro_id" class="border border-gray-300 rounded px-3 py-2">
            <option value="">Todos os Bairros</option>
            @php $bairros = $bairros ?? collect(); @endphp

            @foreach ($bairros as $bairro)
                <option value="{{ $bairro->id }}" {{ (string)request('bairro_id') === (string)$bairro->id ? 'selected' : '' }}>
                    {{ $bairro->nome }} ({{ $bairro->cidade->nome ?? 'Sem cidade' }})
                </option>
            @endforeach
        </select>

        <button type="submit" class="bg-green-600 text-white rounded px-4 py-2 hover:bg-green-700 transition">
            Filtrar
        </button>

        <a href="{{ route('assistente.usuarios.index') }}" class="bg-gray-300 text-gray-800 rounded px-4 py-2 hover:bg-gray-400 transition">
            Limpar
        </a>
    </form>


    {{-- Tabela --}}
    <div class="overflow-x-auto">
        <table class="w-full border text-sm text-left">
            <thead class="bg-green-600 text-white">
                <tr>
                    <th class="px-4 py-2">Nome</th>
                    <th class="px-4 py-2">CPF</th>
                    <th class="px-4 py-2">Bairro</th>
                    <th class="px-4 py-2">Cidade</th>
                    <th class="px-4 py-2">Assistente</th>
                    <th class="px-4 py-2 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cidadaos as $cidadao)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $cidadao->nome }}</td>
                        <td class="px-4 py-2">{{ $cidadao->user->cpf ?? '---' }}</td>
                        <td class="px-4 py-2">{{ $cidadao->bairro->nome ?? '---' }}</td>
                        <td class="px-4 py-2">{{ $cidadao->bairro->cidade->nome ?? '---' }}</td>
                        <td class="px-4 py-2">{{ $cidadao->ultimoAcompanhamento->user->name ?? '---' }}</td>

                        <td class="px-4 py-2 text-center space-x-1">

                            {{-- Editar --}}
                            <a href="{{ route('assistente.cidadao.dados.editar', $cidadao->id) }}"
                                class="inline-block px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs">
                                ✏️ Editar
                            </a>

                            {{-- Cartão --}}
                            <a href="{{ route('assistente.cidadao.cartao', $cidadao->id) }}"
                                class="inline-block px-2 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-xs">
                                📇 Ver Cartão
                            </a>

                            {{-- Senha --}}
                            <a href="{{ route('assistente.cidadao.senha', $cidadao->user_id) }}"
                                class="inline-block px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">
                                🔒 Senha
                            </a>



                            {{-- Excluir --}}
                            <form action="{{ route('assistente.cidadao.destroy', $cidadao->id) }}" method="POST"
                                class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir esta conta?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs">
                                    🗑️ Excluir
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                            Nenhum cidadão encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    <div class="mt-4">
        {{ $cidadaos->appends(request()->query())->links() }}
    </div>
    </div>
@endsection
