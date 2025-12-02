@extends('layouts.app')

@section('title', 'Editar Dependente')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-indigo-700 mb-4">✏️ Editar Dependente</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dependentes.update', $dependente) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="nome" class="block font-medium text-sm">Nome Completo</label>
            <input type="text" name="nome" id="nome" value="{{ old('nome', $dependente->nome) }}" required
                class="w-full rounded border-gray-300 shadow-sm">
        </div>

        <div>
            <label for="data_nascimento" class="block font-medium text-sm">Data de Nascimento</label>
            <input type="date" name="data_nascimento" id="data_nascimento" value="{{ old('data_nascimento', $dependente->data_nascimento) }}" required
                class="w-full rounded border-gray-300 shadow-sm">
        </div>

        <div>
            <label for="cpf" class="block font-medium text-sm">CPF (opcional)</label>
            <input type="text" name="cpf" id="cpf" maxlength="11" value="{{ old('cpf', $dependente->cpf) }}"
                class="w-full rounded border-gray-300 shadow-sm">
        </div>

        <div>
            <label for="grau_parentesco" class="block font-medium text-sm">Grau de Parentesco</label>
            <input type="text" name="grau_parentesco" id="grau_parentesco" value="{{ old('grau_parentesco', $dependente->grau_parentesco) }}" required
                class="w-full rounded border-gray-300 shadow-sm">
        </div>

        <div>
            <label for="sexo" class="block font-medium text-sm">Sexo</label>
            <select name="sexo" id="sexo" class="w-full rounded border-gray-300 shadow-sm">
                <option value="">Não informar</option>
                <option value="masculino" @selected(old('sexo', $dependente->sexo) === 'masculino')>Masculino</option>
                <option value="feminino" @selected(old('sexo', $dependente->sexo) === 'feminino')>Feminino</option>
                <option value="outro" @selected(old('sexo', $dependente->sexo) === 'outro')>Outro</option>
            </select>
        </div>

        <div>
            <label for="escolaridade" class="block font-medium text-sm">Escolaridade</label>
            <input type="text" name="escolaridade" id="escolaridade" value="{{ old('escolaridade', $dependente->escolaridade) }}"
                class="w-full rounded border-gray-300 shadow-sm">
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('dependentes.index') }}" class="text-gray-600 hover:underline text-sm">← Voltar</a>

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Atualizar
            </button>
        </div>
    </form>
</div>
@endsection
