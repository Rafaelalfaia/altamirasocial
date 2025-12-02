@extends('layouts.app')

@section('title', 'Novo Lote de Pagamento')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow-md">
        <h1 class="text-2xl font-bold text-green-800 mb-6">💰 Criar Novo Lote de Pagamento</h1>

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('coordenador.lotes.store') }}" method="POST" class="grid grid-cols-1 gap-6">
            @csrf

            {{-- Nome do Lote --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome do Lote <span class="text-red-500">*</span></label>
                <input type="text" name="nome" value="{{ old('nome') }}" required
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>

            {{-- Programa Social --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Programa Social <span class="text-red-500">*</span></label>
                <select name="programa_id" required
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">Selecione um programa</option>
                    @foreach ($programas as $programa)
                        <option value="{{ $programa->id }}" {{ old('programa_id') == $programa->id ? 'selected' : '' }}>
                            {{ $programa->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Região --}}
            <select name="regiao" required
                class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                <option value="">Selecione</option>
                <option value="Altamira" {{ old('regiao') == 'Altamira' ? 'selected' : '' }}>Altamira</option>
                <option value="Castelo dos Sonhos e Cachoeira da Serra" {{ old('regiao') == 'Castelo dos Sonhos e Cachoeira da Serra' ? 'selected' : '' }}>
                    Castelo dos Sonhos e Cachoeira da Serra
                </option>
                <option value="Altamira e Castelo" {{ old('regiao') == 'Altamira e Castelo' ? 'selected' : '' }}>
                    Altamira, Castelo dos Sonhos e Cachoeira da Serra
                </option>
                <option value="Outros" {{ old('regiao') == 'Outros' ? 'selected' : '' }}>Outros</option>
            </select>


            <div class="mb-4">
                <label for="previsao_pagamento" class="block text-sm font-medium text-gray-700">Previsão de Pagamento</label>
                <input type="date" name="previsao_pagamento" id="previsao_pagamento" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
            </div>
            

            {{-- Valor do Pagamento --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Valor por Pessoa (R$) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="valor_pagamento" required value="{{ old('valor_pagamento') }}"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>

            {{-- Data de Envio --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Data de Envio <span class="text-red-500">*</span></label>
                <input type="date" name="data_envio" required value="{{ old('data_envio', now()->format('Y-m-d')) }}"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>

            {{-- Período de Pagamento --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Período do Pagamento (ex: Junho/2025) <span class="text-red-500">*</span></label>
                <input type="text" name="periodo_pagamento" value="{{ old('periodo_pagamento') }}" required
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500"
                    placeholder="Digite o período do pagamento">
            </div>

            {{-- Formato do CPF --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Formato do CPF</label>
                <select name="formato_cpf" required
                    class="w-full border-gray-300 rounded px-4 py-2 shadow-sm focus:ring focus:ring-indigo-200">
                    <option value="com_pontos" {{ old('formato_cpf') == 'com_pontos' ? 'selected' : '' }}>000.000.000-00</option>
                    <option value="sem_pontos" {{ old('formato_cpf') == 'sem_pontos' ? 'selected' : '' }}>00000000000</option>
                </select>
            </div>

            {{-- Descrição do lote --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Descrição</label>
                <textarea name="descricao" rows="4"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('descricao') }}</textarea>
            </div>

            {{-- Botão --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-green-700 hover:bg-green-800 text-white font-semibold px-6 py-2 rounded shadow transition">
                    💾 Criar Lote
                </button>
            </div>
        </form>
    </div>
@endsection
