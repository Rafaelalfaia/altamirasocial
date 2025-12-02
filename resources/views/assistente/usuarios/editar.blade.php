@extends('layouts.app')

@section('title', 'Editar Cidadão')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold text-green-700 mb-6">✏️ Editar Dados do Cidadão</h1>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('assistente.usuarios.editar.salvar', $cidadao->user_id) }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome Completo</label>
                    <input type="text" name="nome" value="{{ old('nome', $cidadao->nome) }}"
                        class="w-full border px-3 py-2 rounded">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                    <input type="date" name="nascimento" value="{{ old('nascimento', $cidadao->nascimento) }}"
                        class="w-full border px-3 py-2 rounded">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Telefone</label>
                    <input type="text" name="telefone" value="{{ old('telefone', $cidadao->telefone) }}"
                        class="w-full border px-3 py-2 rounded">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Gênero</label>
                    <select name="genero" class="w-full border px-3 py-2 rounded">
                        <option value="">Selecione</option>
                        <option value="Masculino" {{ old('genero', $cidadao->genero) == 'Masculino' ? 'selected' : '' }}>
                            Masculino</option>
                        <option value="Feminino" {{ old('genero', $cidadao->genero) == 'Feminino' ? 'selected' : '' }}>
                            Feminino</option>
                        <option value="Outro" {{ old('genero', $cidadao->genero) == 'Outro' ? 'selected' : '' }}>Outro
                        </option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Endereço Completo</label>
                    <input type="text" name="endereco" value="{{ old('endereco', $cidadao->endereco) }}"
                        class="w-full border px-3 py-2 rounded">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Ponto de Referência</label>
                    <input type="text" name="ponto_referencia"
                        value="{{ old('ponto_referencia', $cidadao->ponto_referencia) }}"
                        class="w-full border px-3 py-2 rounded">
                </div>

            </div>

            <div class="mt-6">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">
                    💾 Salvar Dados
                </button>
            </div>
        </form>
    </div>
@endsection