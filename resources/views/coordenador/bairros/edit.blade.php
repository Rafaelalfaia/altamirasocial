@extends('layouts.app')

@section('title', 'Editar Cidade')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold text-indigo-700 mb-4">Editar Cidade</h1>

        <form action="{{ route('coordenador.cidades.update', $cidade->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium">Nome da Cidade</label>
                <input type="text" name="nome" value="{{ old('nome', $cidade->nome) }}"
                    class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Estado</label>
                <select name="estado_id" class="w-full border px-3 py-2 rounded" required>
                    @foreach ($estados as $estado)
                        <option value="{{ $estado->id }}" {{ $cidade->estado_id == $estado->id ? 'selected' : '' }}>
                            {{ $estado->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="text-right">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Atualizar Cidade
                </button>
            </div>
        </form>
    </div>
@endsection