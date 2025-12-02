@extends('layouts.app')

@section('title', 'Editar Assistente')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-6 space-y-6">

    <h1 class="text-2xl font-bold text-green-900 mb-6">✏️ Editar Assistente</h1>

    <form action="{{ route('admin.assistentes.update', $assistente) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Nome (apenas leitura) --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nome</label>
            <input type="text" value="{{ $assistente->name }}" disabled
                   class="w-full rounded border-gray-300 shadow text-sm px-3 py-2 bg-gray-100 cursor-not-allowed">
        </div>

        {{-- Coordenadores --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Coordenadores Responsáveis</label>
            <select name="coordenadores[]" multiple class="w-full rounded border-gray-300 shadow text-sm">
                @foreach($todosCoordenadores as $coord)
                    <option value="{{ $coord->id }}"
                        @selected($assistente->coordenadores->pluck('id')->contains($coord->id))>
                        {{ $coord->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Roles --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Funções (Roles)</label>
            <select name="roles[]" multiple class="w-full rounded border-gray-300 shadow text-sm">
                @foreach($rolesDisponiveis as $role)
                    <option value="{{ $role }}"
                        @selected($assistente->hasRole($role))>
                        {{ $role }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Botão --}}
        <div class="flex justify-end">
            <button type="submit"
                    class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded text-sm shadow">
                💾 Salvar Alterações
            </button>
        </div>
    </form>
</div>
@endsection
