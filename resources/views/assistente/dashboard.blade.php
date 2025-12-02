@extends('layouts.app')

@section('title', 'Painel do Assistente')

@section('content')
@php
    use App\Models\{ModoPlantao, Cidadao, Programa, Solicitacao, Emergencia, Acompanhamento};

    $modoPlantao = ModoPlantao::where('user_id', auth()->id())->first();
    $ativo = $modoPlantao && $modoPlantao->ativo;

    $cidadaos = Cidadao::where('user_id_responsavel', auth()->id())->take(4)->get();
    $programas = Programa::latest()->take(4)->get();
    $solicitacoes = Solicitacao::where('destinatario_id', auth()->id())->latest()->take(4)->get();
    $acompanhamentos = Acompanhamento::with('cidadao')->where('user_id', auth()->id())->latest()->take(4)->get();
    $emergencias = $ativo ? Emergencia::with('cidadao')->where('status', 'aberto')->latest()->take(3)->get() : collect();
@endphp

<div class="max-w-7xl mx-auto p-6">
    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-6">

        {{-- Esquerda: Título e boas-vindas --}}
        <div class="flex items-center gap-4">
            <img src="{{ Auth::user()->foto_url }}"
                 alt="Foto do Assistente"
                 class="w-16 h-16 rounded-full object-cover border-2 border-green-700 shadow-md">

            <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-800">🎧 Painel do Técnico de Ensino Superior</h1>
                <p class="text-gray-600 text-sm mt-1">👋 Seja bem-vindo, <span class="font-medium text-gray-800">{{ Auth::user()->name }}</span></p>
            </div>
        </div>

        {{-- Direita: Botão Modo Plantão --}}
        <form action="{{ route('assistente.modo-plantao') }}" method="POST">
            @csrf
            <button type="submit"
                class="px-4 py-2 rounded-md text-sm font-medium text-white shadow-sm {{ $ativo ? 'bg-gray-800 hover:bg-gray-900' : 'bg-red-600 hover:bg-red-700' }}">
                🚨 {{ $ativo ? 'Desativar' : 'Ativar' }} Modo Plantão
            </button>
        </form>
    </div>


    @if ($ativo)
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6 text-sm">
            ⚠️ Você está em <strong>Modo Plantão</strong>. Atendimentos emergenciais estão habilitados.
        </div>
    @endif

    {{-- Blocos principais --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Programas Sociais --}}
        <div class="bg-white border border-indigo-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
            <h2 class="text-base font-semibold text-indigo-600 mb-3 flex items-center gap-2">📚 Programas Sociais</h2>
            <ul class="text-sm text-gray-700 space-y-1 mb-3">
                @forelse($programas as $programa)
                    <li>• {{ $programa->nome }}</li>
                @empty
                    <li>Nenhum programa disponível.</li>
                @endforelse
            </ul>
            <a href="{{ route('assistente.programas.index') }}" class="text-indigo-600 text-sm hover:underline">Ver todos</a>
        </div>

        {{-- Cidadãos com Acompanhamento --}}
        <div class="bg-white border border-green-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
            <h2 class="text-base font-semibold text-green-600 mb-3 flex items-center gap-2">🧑‍🤝‍🧑 Cidadãos Acompanhados</h2>
            <ul class="text-sm text-gray-700 space-y-1 mb-3">
                @forelse($acompanhamentos as $ac)
                    <li>• {{ $ac->cidadao->nome ?? 'Sem nome registrado' }}</li>
                @empty
                    <li>Nenhum acompanhamento realizado.</li>
                @endforelse
            </ul>
            <a href="{{ route('assistente.acompanhamentos.index') }}" class="text-green-600 text-sm hover:underline">Ver todos</a>
        </div>

        {{-- Solicitações Recentes --}}
        <div class="bg-white border border-yellow-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
            <h2 class="text-base font-semibold text-yellow-600 mb-3 flex items-center gap-2">📨 Solicitações Recentes</h2>
            <ul class="text-sm text-gray-700 space-y-1 mb-3">
                @forelse($solicitacoes as $sol)
                    <li>• {{ $sol->titulo }}</li>
                @empty
                    <li>Nenhuma solicitação recente.</li>
                @endforelse
            </ul>
            <a href="{{ route('assistente.solicitacoes.index') }}" class="text-yellow-600 text-sm hover:underline">Ver todas</a>
        </div>
    </div>

   {{-- Emergências --}}
@if ($ativo)
<div class="mt-10">
    <h2 class="text-xl font-bold text-red-700 mb-4 flex items-center gap-2">🚨 Atendimentos Emergenciais</h2>
    @forelse($emergencias as $emergencia)
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4 shadow space-y-2">
            <p class="text-sm text-gray-800"><strong>Cidadão:</strong> {{ $emergencia->cidadao->nome ?? 'Desconhecido' }}</p>
            <p class="text-sm text-gray-800"><strong>Motivo:</strong> {{ $emergencia->motivo }}</p>
            <p class="text-sm text-gray-700"><strong>Descrição:</strong> {{ $emergencia->descricao ?? 'Não informada.' }}</p>

            <div class="flex flex-wrap gap-2 mt-2">
                <a href="{{ route('assistente.emergencias.chamada', $emergencia->sala) }}"
                   class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-2 rounded">
                    🎥 Entrar na Sala
                </a>

                <a href="{{ route('assistente.emergencias.relatar', $emergencia->id) }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-2 rounded">
                    📋 Relatar à SEMED
                </a>

                <form action="{{ route('assistente.emergencias.destroy', $emergencia->id) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir esta ocorrência?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white text-sm px-3 py-2 rounded">
                        🗑️ Excluir
                    </button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-sm text-gray-600">Nenhuma emergência aguardando no momento.</p>
    @endforelse
</div>
@endif


</div>
@endsection
