@extends('layouts.app')

@section('title', 'Temporários')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <h1 class="text-2xl font-bold text-green-800">🕒 Cidadãos Temporários</h1>

    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome ou CPF"
            class="border rounded px-3 py-2 w-1/3 text-sm">
        <button class="bg-green-700 text-white px-4 py-2 rounded">Buscar</button>
    </form>

    <div class="bg-white shadow rounded overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-green-700 text-white">
                <tr>
                    <th class="px-4 py-2 text-left">Nome</th>
                    <th class="px-4 py-2">CPF</th>
                    <th class="px-4 py-2">Validez</th>
                    <th class="px-4 py-2">Criado por</th>
                    <th class="px-4 py-2">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cidadaos as $cidadao)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $cidadao->nome }}</td>
                        <td class="px-4 py-2">{{ $cidadao->cpf }}</td>
                        <td class="px-4 py-2">
                            {{ \Carbon\Carbon::parse($cidadao->fim_validez)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            {{ optional($cidadao->user)->name ?? '-' }}
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('admin.cidadaos-temporarios.show', $cidadao->id) }}"
                               class="text-blue-600 hover:underline">Ver</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $cidadaos->links() }}
</div>
@endsection
