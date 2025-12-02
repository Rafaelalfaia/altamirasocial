@extends('layouts.app')

@section('title', 'Cadastro de Perfil Socioeconômico')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Cadastro de Perfil</h1>

    <form action="{{ route('cidadao.perfil.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="nome_completo" class="block font-medium">Nome Completo</label>
            <input type="text" name="nome_completo" id="nome_completo" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div>
            <label for="cpf" class="block font-medium">CPF</label>
            <input type="text" name="cpf" id="cpf" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div>
            <label for="data_nascimento" class="block font-medium">Data de Nascimento</label>
            <input type="date" name="data_nascimento" id="data_nascimento" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div>
            <label class="block font-medium">Sexo</label>
            <select name="sexo" class="w-full border px-3 py-2 rounded" required>
                <option value="">Selecione</option>
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
                <option value="Outro">Outro</option>
            </select>
        </div>

        <div>
            <label for="telefone" class="block font-medium">Telefone</label>
            <input type="text" name="telefone" id="telefone" class="w-full border px-3 py-2 rounded">
        </div>

        {{-- Continue com CEP, endereço, tipo de moradia, saneamento, renda, escolaridade, PCD, etc. --}}

        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Salvar Perfil</button>
    </form>
@endsection