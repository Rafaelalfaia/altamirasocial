@extends('layouts.app')

@section('title', 'Evoluções do Acompanhamento')

@section('content')
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow">
        <h1 class="text-2xl font-bold text-green-700 mb-6">
            📈 Evoluções – {{ $acompanhamento->cidadao->nome }}
        </h1>

        {{-- Alerta de sucesso --}}
        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Botão para nova evolução --}}
        <div class="mb-4 text-right">
            <a href="{{ route('assistente.evolucoes.create', $acompanhamento->id) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-green-600 text-green-700 hover:bg-green-50 transition text-sm">
                ➕ Nova Evolução
            </a>
        </div>

        {{-- Lista de evoluções --}}
        @forelse ($evolucoes as $evolucao)
    <div class="mb-4 border-l-4 border-green-600 pl-4 py-2">
        <p class="text-sm text-gray-600 font-semibold">
            📌 {{ $evolucao->titulo }}
        </p>

        <p class="text-sm text-gray-500 italic">
            Tipo de Atendimento: {{ $evolucao->local_atendimento ?? 'Não informado' }}
        </p>

        <p class="text-gray-700 text-sm mt-1 whitespace-pre-line">
            {{ $evolucao->resumo }}
        </p>

        {{-- Casos emergenciais --}}
        @if($evolucao->caso_emergencial)
            <div class="mt-3 bg-red-50 border-l-4 border-red-600 pl-4 py-2 rounded">
                <p class="text-sm font-bold text-red-700">
                    🚨 Caso Emergencial:
                    @switch($evolucao->caso_emergencial)
                        @case('violencia_domestica') Violência Doméstica @break
                        @case('violencia_sexual') Violência Sexual @break
                        @case('problemas_saude') Problemas Graves de Saúde @break
                        @case('pobreza_extrema') Pobreza Extrema @break
                        @default {{ ucfirst($evolucao->caso_emergencial) }}
                    @endswitch
                </p>
                <p class="text-sm text-red-800 mt-1">
                    {{ $evolucao->descricao_emergencial }}
                </p>
            </div>
        @endif

        <p class="text-xs text-gray-400 mt-1">
            Criado em {{ $evolucao->created_at->format('d/m/Y H:i') }}
        </p>

        <div class="mt-2 text-right">
            <a href="{{ route('assistente.evolucoes.edit', [$acompanhamento->id, $evolucao->id]) }}"
                class="text-sm text-blue-600 font-medium hover:underline">
                ✏️ Editar Evolução
            </a>
        </div>
    </div>
@empty
    <p class="text-gray-600 italic">Nenhuma evolução registrada ainda.</p>
@endforelse

