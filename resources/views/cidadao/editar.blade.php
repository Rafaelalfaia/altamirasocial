@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
    <h1 class="text-2xl font-bold text-indigo-700 mb-6">Editar Cadastro</h1>

    <form method="POST" action="{{ route('cidadao.atualizar') }}" class="bg-white p-6 rounded shadow-md w-full max-w-3xl">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome Completo</label>
                <input type="text" name="nome" value="{{ old('nome', $cidadao->nome) }}"
                    class="w-full mt-1 p-2 border rounded">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">CPF</label>
                <input type="text" name="cpf" value="{{ old('cpf', $cidadao->cpf) }}"
                    class="w-full mt-1 p-2 border rounded">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                <input type="date" name="data_nascimento" value="{{ old('data_nascimento', $cidadao->data_nascimento) }}"
                    class="w-full mt-1 p-2 border rounded">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Sexo</label>
                <select name="sexo" class="w-full mt-1 p-2 border rounded">
                    <option value="Masculino" {{ $cidadao->sexo == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="Feminino" {{ $cidadao->sexo == 'Feminino' ? 'selected' : '' }}>Feminino</option>
                    <option value="Outro" {{ $cidadao->sexo == 'Outro' ? 'selected' : '' }}>Outro</option>
                </select>
            </div>

            {{-- Endereço --}}
            <div class="mt-6 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Endereço</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label>CEP</label>
                        <input type="text" name="cep" value="{{ old('cep', $cidadao->cep) }}"
                            class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label>Rua</label>
                        <input type="text" name="rua" value="{{ old('rua', $cidadao->rua) }}"
                            class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label>Número</label>
                        <input type="text" name="numero" value="{{ old('numero', $cidadao->numero) }}"
                            class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label>Bairro</label>
                        <input type="text" name="bairro" value="{{ old('bairro', $cidadao->bairro) }}"
                            class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label>Cidade</label>
                        <input type="text" name="cidade" value="{{ old('cidade', $cidadao->cidade) }}"
                            class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label>Estado</label>
                        <input type="text" name="estado" value="{{ old('estado', $cidadao->estado) }}"
                            class="w-full p-2 border rounded">
                    </div>
                    <div class="md:col-span-2">
                        <label>Complemento</label>
                        <input type="text" name="complemento" value="{{ old('complemento', $cidadao->complemento) }}"
                            class="w-full p-2 border rounded">
                    </div>
                </div>
            </div>

            {{-- Moradia --}}
            <div class="mt-6 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Moradia</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label>Tipo de Moradia</label>
                        <select name="tipo_moradia" class="w-full p-2 border rounded">
                            <option value="Própria" {{ $cidadao->tipo_moradia == 'Própria' ? 'selected' : '' }}>Própria
                            </option>
                            <option value="Alugada" {{ $cidadao->tipo_moradia == 'Alugada' ? 'selected' : '' }}>Alugada
                            </option>
                            <option value="Cessão" {{ $cidadao->tipo_moradia == 'Cessão' ? 'selected' : '' }}>Cessão</option>
                            <option value="Outros" {{ $cidadao->tipo_moradia == 'Outros' ? 'selected' : '' }}>Outros</option>
                        </select>
                    </div>
                    <div>
                        <label><input type="checkbox" name="tem_agua_encanada" value="1" {{ $cidadao->tem_agua_encanada ? 'checked' : '' }}> Água Encanada</label><br>
                        <label><input type="checkbox" name="tem_esgoto" value="1" {{ $cidadao->tem_esgoto ? 'checked' : '' }}> Esgoto</label><br>
                        <label><input type="checkbox" name="tem_coleta_lixo" value="1" {{ $cidadao->tem_coleta_lixo ? 'checked' : '' }}> Coleta de Lixo</label><br>
                        <label><input type="checkbox" name="tem_energia" value="1" {{ $cidadao->tem_energia ? 'checked' : '' }}> Energia Elétrica</label>
                    </div>
                </div>
            </div>

            {{-- Renda e Família --}}
            <div class="mt-6 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Renda e Composição Familiar</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label>Renda Familiar Total</label>
                        <input type="number" step="0.01" name="renda_total_familiar"
                            value="{{ old('renda_total_familiar', $cidadao->renda_total_familiar) }}"
                            class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label>Nº de Pessoas na Residência</label>
                        <input type="number" name="pessoas_na_residencia"
                            value="{{ old('pessoas_na_residencia', $cidadao->pessoas_na_residencia) }}"
                            class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label>Ocupação</label>
                        <input type="text" name="ocupacao" value="{{ old('ocupacao', $cidadao->ocupacao) }}"
                            class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label>Grau de Parentesco</label>
                        <input type="text" name="grau_parentesco"
                            value="{{ old('grau_parentesco', $cidadao->grau_parentesco) }}"
                            class="w-full p-2 border rounded">
                    </div>
                </div>
            </div>

            {{-- Deficiência --}}
            <div class="mt-6 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Deficiência</h3>
                <label>
                    <input type="checkbox" name="pcd" value="1" {{ $cidadao->pcd ? 'checked' : '' }}>
                    Pessoa com Deficiência
                </label>

                <div class="mt-2">
                    <label>Tipo de Deficiência</label>
                    <input type="text" name="tipo_deficiencia"
                        value="{{ old('tipo_deficiencia', $cidadao->tipo_deficiencia) }}" class="w-full p-2 border rounded">
                </div>
            </div>

            {{-- Escolaridade e Observações --}}
            <div class="mt-6 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Escolaridade e Observações</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label>Escolaridade</label>
                        <input type="text" name="escolaridade" value="{{ old('escolaridade', $cidadao->escolaridade) }}"
                            class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label>Observações</label>
                        <textarea name="observacoes" rows="3"
                            class="w-full p-2 border rounded">{{ old('observacoes', $cidadao->observacoes) }}</textarea>
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-6">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Salvar Dados
            </button>
        </div>
    </form>
@endsection