@extends('layouts.app')

@section('title', 'Meus Dependentes')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-indigo-700">👨‍👩‍👧 Meus Dependentes</h1>
        <a href="{{ route('dependentes.create') }}"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow text-sm">
            + Novo Dependente
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($dependentes->isEmpty())
        <div class="bg-white p-6 rounded shadow text-center text-gray-500">
            Nenhum dependente cadastrado ainda.
        </div>
    @else
        <div class="bg-white shadow rounded overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700 font-semibold">
                    <tr>
                        <th class="px-4 py-3">Nome</th>
                        <th class="px-4 py-3">Nascimento</th>
                        <th class="px-4 py-3">Parentesco</th>
                        <th class="px-4 py-3">CPF</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dependentes as $dependente)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $dependente->nome }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($dependente->data_nascimento)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $dependente->grau_parentesco }}</td>
                            <td class="px-4 py-3">{{ $dependente->cpf ?? '—' }}</td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('dependentes.edit', $dependente) }}"
                                    class="text-blue-600 hover:underline text-sm">Editar</a>

                                <form action="{{ route('dependentes.destroy', $dependente) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Tem certeza que deseja remover este dependente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-sm">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
