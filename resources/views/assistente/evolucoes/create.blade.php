@extends('layouts.app')

@section('title', 'Nova Evolução')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow-md">
    <h1 class="text-2xl font-bold text-green-700 mb-6">
        ➕ Nova Evolução – {{ $acompanhamento->cidadao->nome }}
    </h1>

    {{-- Validação --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-2 rounded mb-4">
            <ul class="list-disc pl-4 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('assistente.evolucoes.store', $acompanhamento->id) }}" method="POST"
          class="space-y-6"
          x-data="{ emergencial: false, tipo: '', showOutro: false }">
        @csrf

        {{-- Título --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Título da Evolução</label>
            <input type="text" name="titulo"
                class="w-full border px-4 py-2 rounded shadow-sm focus:ring focus:border-green-500" required>
        </div>

        {{-- Resumo --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Resumo</label>
            <textarea name="resumo" rows="6"
                class="w-full border px-4 py-2 rounded shadow-sm focus:ring focus:border-green-500"
                placeholder="Descreva aqui o que mudou, como está a situação atual, etc..."></textarea>
        </div>

        {{-- Tipo de Atendimento --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Atendimento</label>
            <select name="tipo_atendimento" id="tipo_atendimento"
                class="w-full border px-4 py-2 rounded shadow-sm focus:ring focus:border-green-500"
                x-on:change="showOutro = $event.target.value === 'Outro'" required>
                <option value="">Selecione...</option>
                <option value="Interno">Interno</option>
                <option value="Em Casa">Em Casa</option>
                <option value="Outro">Outro</option>
            </select>
        </div>

        {{-- Campo "Outro" personalizado --}}
        <div x-show="showOutro" class="transition mt-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Informe o local</label>
            <input type="text" name="outro_local"
                class="w-full border px-4 py-2 rounded shadow-sm focus:ring focus:border-green-500"
                placeholder="Descreva o local do atendimento">
        </div>

        {{-- Casos Emergenciais --}}
        <div class="mt-6">
            <label class="flex items-center space-x-2">
                <input type="checkbox" x-model="emergencial" class="text-green-600 focus:ring-green-500">
                <span class="text-sm text-gray-700 font-medium">Casos Emergenciais</span>
            </label>
        </div>

        {{-- Tipo de Emergência --}}
        <div x-show="emergencial" class="transition mt-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Emergência</label>
            <select name="caso_emergencial"
                class="w-full border px-4 py-2 rounded shadow-sm focus:ring focus:border-green-500"
                x-model="tipo" :required="emergencial">
                <option value="">Selecione...</option>
                <option value="violencia_domestica">Violência Doméstica</option>
                <option value="violencia_sexual">Violência Sexual</option>
                <option value="problemas_saude">Problemas Graves de Saúde</option>
                <option value="pobreza_extrema">Pobreza Extrema (falta de alimentos)</option>
                <option value="tentativa_homicidio">Tentativa de Homicídio</option>

            </select>
        </div>

        {{-- Descrição do Caso Emergencial --}}
        <div x-show="emergencial && tipo !== ''" class="transition mt-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Descrição do Caso</label>
            <textarea name="descricao_emergencial" rows="4"
                class="w-full border px-4 py-2 rounded shadow-sm focus:ring focus:border-green-500"
                placeholder="Detalhe a situação emergencial" :required="emergencial && tipo !== ''"></textarea>
        </div>

        {{-- Botão --}}
        <div class="text-right pt-4">
            <button type="submit" class="bg-green-700 text-white px-6 py-2 rounded hover:bg-green-800 transition">
                💾 Salvar Evolução
            </button>
        </div>
    </form>
</div>
@endsection
