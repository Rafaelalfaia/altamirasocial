@extends('layouts.app')

@section('title', 'Editar Evolução')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow-md">
    <h1 class="text-2xl font-bold text-green-700 mb-6">
        ✏️ Editar Evolução – {{ $acompanhamento->cidadao->nome }}
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

    <form action="{{ route('assistente.evolucoes.update', [$acompanhamento->id, $evolucao->id]) }}"
        method="POST"
        class="space-y-6"
        x-data="{
            emergencial: '{{ $evolucao->caso_emergencial ? 'true' : 'false' }}' === 'true',
            tipo: '{{ $evolucao->caso_emergencial }}',
            showOutro: '{{ $evolucao->local_atendimento }}' !== 'Interno' && '{{ $evolucao->local_atendimento }}' !== 'Em Casa'
        }">
        @csrf
        @method('PATCH')

        {{-- Título --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Título da Evolução</label>
            <input type="text" name="titulo"
                value="{{ old('titulo', $evolucao->titulo) }}"
                class="w-full border px-4 py-2 rounded shadow-sm focus:ring focus:border-green-500" required>
        </div>

        {{-- Resumo --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Resumo</label>
            <textarea name="resumo" rows="6"
                class="w-full border px-4 py-2 rounded shadow-sm focus:ring focus:border-green-500"
                placeholder="Descreva aqui o que mudou, como está a situação atual, etc...">{{ old('resumo', $evolucao->resumo) }}</textarea>
        </div>

        {{-- Tipo de Atendimento --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Atendimento</label>
            <select name="tipo_atendimento"
                class="w-full border px-4 py-2 rounded shadow-sm focus:ring focus:border-green-500"
                x-on:change="showOutro = $event.target.value === 'Outro'" required>
                <option value="">Selecione...</option>
                <option value="Interno" {{ $evolucao->local_atendimento === 'Interno' ? 'selected' : '' }}>Interno</option>
                <option value="Em Casa" {{ $evolucao->local_atendimento === 'Em Casa' ? 'selected' : '' }}>Em Casa</option>
                <option value="Outro" {{ !in_array($evolucao->local_atendimento, ['Interno', 'Em Casa']) ? 'selected' : '' }}>Outro</option>
            </select>
        </div>

        {{-- Outro local --}}
        <div x-show="showOutro" class="transition mt-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Informe o local</label>
            <input type="text" name="outro_local"
                value="{{ !in_array($evolucao->local_atendimento, ['Interno', 'Em Casa']) ? $evolucao->local_atendimento : '' }}"
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
                <option value="violencia_domestica" {{ $evolucao->caso_emergencial === 'violencia_domestica' ? 'selected' : '' }}>Violência Doméstica</option>
                <option value="violencia_sexual" {{ $evolucao->caso_emergencial === 'violencia_sexual' ? 'selected' : '' }}>Violência Sexual</option>
                <option value="problemas_saude" {{ $evolucao->caso_emergencial === 'problemas_saude' ? 'selected' : '' }}>Problemas Graves de Saúde</option>
                <option value="pobreza_extrema" {{ $evolucao->caso_emergencial === 'pobreza_extrema' ? 'selected' : '' }}>Pobreza Extrema (falta de alimentos)</option>
                <option value="tentativa_homicidio" {{ $evolucao->caso_emergencial === 'tentativa_homicidio' ? 'selected' : '' }}>Tentativa de Homicídio</option> 
            </select>
        </div>



        {{-- Descrição da Emergência --}}
        <div x-show="emergencial && tipo !== ''" class="transition mt-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Descrição do Caso</label>
            <textarea name="descricao_emergencial" rows="4"
                class="w-full border px-4 py-2 rounded shadow-sm focus:ring focus:border-green-500"
                placeholder="Detalhe a situação emergencial" :required="emergencial && tipo !== ''">{{ old('descricao_emergencial', $evolucao->descricao_emergencial) }}</textarea>
        </div>

        {{-- Botão --}}
        <div class="text-right pt-4">
            <button type="submit" class="bg-green-700 text-white px-6 py-2 rounded hover:bg-green-800 transition">
                💾 Atualizar Evolução
            </button>
        </div>
    </form>
</div>
@endsection
