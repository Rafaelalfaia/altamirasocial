@extends('layouts.app')

@section('title', 'Atendentes')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Cabeçalho --}}
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-green-800">👨‍🍳 Atendentes</h1>

        <a href="{{ route('restaurante.coordenador.atendentes.create') }}"
           class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 transition">
            ➕ Novo Atendente
        </a>
    </div>

    {{-- Mensagens de sucesso --}}
    @if (session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Lista de atendentes --}}
    <div class="bg-white shadow rounded p-4 overflow-x-auto">
        <table class="w-full text-sm table-auto">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-2">Nome</th>
                    <th class="p-2">E-mail</th>
                    <th class="p-2">CPF</th>
                    <th class="p-2">Restaurante(s)</th>
                    <th class="p-2 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($atendentes as $atendente)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-2">{{ $atendente->name }}</td>
                        <td class="p-2">{{ $atendente->email ?? '—' }}</td>
                        <td class="p-2">{{ $atendente->cpf }}</td>
                        <td class="p-2">
                            @if ($atendente->restaurantes && $atendente->restaurantes->count())
                                <ul class="list-disc pl-4 text-gray-700">
                                    @foreach ($atendente->restaurantes as $rest)
                                        <li>{{ $rest->nome }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-red-600">Não vinculado</span>
                            @endif
                        </td>
                        <td class="p-2 text-center">
                            <div class="flex justify-center items-center gap-4">
                                {{-- Editar --}}
                                <a href="{{ route('restaurante.coordenador.atendentes.edit', $atendente->id) }}"
                                   class="text-blue-600 hover:underline text-sm font-medium">
                                    ✏️ Editar
                                </a>

                                {{-- Remover --}}
                                <form method="POST" action="{{ route('restaurante.coordenador.atendentes.destroy', $atendente->id) }}"
                                      onsubmit="return confirm('Tem certeza que deseja remover este atendente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:underline text-sm font-medium">
                                        🗑️ Remover
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">
                            Nenhum atendente cadastrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
