@extends('layouts.app')

@section('title', 'Novo Dependente')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-indigo-700 mb-4">👨‍👩‍👧 Novo Dependente</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dependentes.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="nome" class="block font-medium text-sm">Nome Completo</label>
            <input type="text" name="nome" id="nome" required class="w-full rounded border-gray-300 shadow-sm">
        </div>

        <div>
            <label for="data_nascimento" class="block font-medium text-sm">Data de Nascimento</label>
            <input type="date" name="data_nascimento" id="data_nascimento" required class="w-full rounded border-gray-300 shadow-sm">
        </div>

        <div>
            <label for="cpf" class="block font-medium text-sm">CPF </label>
            <input type="text" name="cpf" id="cpf" maxlength="11" class="w-full rounded border-gray-300 shadow-sm">
        </div>

        <div>
            <label for="grau_parentesco" class="block font-medium text-sm">Grau de Parentesco</label>
            <input type="text" name="grau_parentesco" id="grau_parentesco" required class="w-full rounded border-gray-300 shadow-sm">
        </div>

        <div>
            <label for="sexo" class="block font-medium text-sm">Sexo</label>
            <select name="sexo" id="sexo" class="w-full rounded border-gray-300 shadow-sm">
                <option value="">Não informar</option>
                <option value="masculino">Masculino</option>
                <option value="feminino">Feminino</option>
                <option value="outro">Outro</option>
            </select>
        </div>

        <div>
            <label for="escolaridade" class="block font-medium text-sm">Escolaridade</label>
            <input type="text" name="escolaridade" id="escolaridade" class="w-full rounded border-gray-300 shadow-sm">
        </div>

        <div class="text-right">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Salvar Dependente
            </button>
        </div>
    </form>
</div>
@endsection
