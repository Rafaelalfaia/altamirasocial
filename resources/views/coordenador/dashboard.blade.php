@extends('layouts.app')

@section('title', 'Dashboard do Coordenador')

@section('content')
<div class="max-w-6xl mx-auto px-3 md:px-4 lg:px-6 py-6 space-y-6 overflow-x-hidden">

  {{-- Cabeçalho compacto --}}
  <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <img src="{{ Auth::user()->foto_url }}" alt="Foto do Coordenador"
           class="w-12 h-12 rounded-full object-cover ring-2 ring-emerald-700/70 shadow-sm">
      <div class="min-w-0">
        <h1 class="text-lg sm:text-xl font-bold text-emerald-800">📊 Painel do Coordenador</h1>
        <p class="text-[13px] text-gray-600">Bem-vindo, <span class="font-medium text-gray-800">{{ Auth::user()->name }}</span></p>
      </div>
    </div>
  </header>

  {{-- KPIs (coerentes em verde, bem compactos) --}}
  <section class="grid grid-cols-2 md:grid-cols-4 gap-3">
    <div class="rounded-lg bg-gradient-to-br from-emerald-50 to-white p-4 ring-1 ring-emerald-100">
      <p class="text-[11px] text-emerald-800/90 font-semibold">Cidadãos Cadastrados</p>
      <p class="text-2xl font-bold text-emerald-700">{{ $totalCidadaos }}</p>
    </div>
    <div class="rounded-lg bg-gradient-to-br from-emerald-50 to-white p-4 ring-1 ring-emerald-100">
      <p class="text-[11px] text-emerald-800/90 font-semibold">Assistentes Criados</p>
      <p class="text-2xl font-bold text-emerald-700">{{ $assistentesCriados }}</p>
    </div>
    <div class="rounded-lg bg-gradient-to-br from-emerald-50 to-white p-4 ring-1 ring-emerald-100">
      <p class="text-[11px] text-emerald-800/90 font-semibold">Meus Programas</p>
      <p class="text-2xl font-bold text-emerald-700">{{ $meusProgramas }}</p>
    </div>
    <div class="rounded-lg bg-gradient-to-br from-emerald-50 to-white p-4 ring-1 ring-emerald-100">
      <p class="text-[11px] text-emerald-800/90 font-semibold">Ocorrências 48h</p>
      <p class="text-2xl font-bold text-emerald-700">{{ $emergenciasRecentes->count() }}</p>
    </div>
  </section>

  {{-- Plantões & Ocorrências (altura controlada / sem overflow lateral) --}}
  <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    {{-- Plantões Recentes --}}
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-100 p-5">
      <div class="flex items-center justify-between">
        <h2 class="text-base font-semibold text-emerald-700">🕒 Assistente Social de Plantão</h2>
        <a href="{{ route('coordenador.plantoes.historico') }}" class="text-xs text-emerald-700 hover:underline">Histórico</a>
      </div>
      <ul class="mt-3 text-sm text-gray-800 space-y-2">
        @forelse ($plantoesRecentes->take(6) as $plantao)
          <li class="flex items-center justify-between gap-2">
            <span class="truncate"><span class="font-medium">{{ $plantao->user->name }}</span></span>
            <span class="text-[11px] text-gray-500">desde {{ \Carbon\Carbon::parse($plantao->updated_at)->format('d/m H:i') }}</span>
          </li>
        @empty
          <li class="text-gray-500 italic">Nenhum plantonista ativo agora.</li>
        @endforelse
      </ul>
    </div>

    {{-- Ocorrências Emergenciais --}}
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-100 p-5">
      <div class="flex items-center justify-between">
        <h2 class="text-base font-semibold text-red-700">🚨 Ocorrências Emergenciais (48h)</h2>
        <a href="{{ route('coordenador.emergencias.historico') }}" class="text-xs text-red-700 hover:underline">Histórico</a>
      </div>

      <div class="mt-3 space-y-3">
        @forelse ($emergenciasRecentes->take(3) as $emergencia)
          <article class="rounded-lg bg-red-50 ring-1 ring-red-100 p-3">
            <p class="text-sm text-gray-800"><strong>Cidadão:</strong> {{ $emergencia->cidadao->nome ?? 'Desconhecido' }}</p>
            <p class="text-sm text-gray-800"><strong>Motivo:</strong> {{ $emergencia->motivo }}</p>
            <p class="text-[13px] text-gray-600 line-clamp-2"><strong>Descrição:</strong> {{ \Illuminate\Support\Str::limit($emergencia->descricao, 140) }}</p>
            <div class="mt-2">
              <a href="{{ route('coordenador.emergencias.show', $emergencia->id) }}"
                 class="inline-flex items-center rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">
                 👁 Ver
              </a>
            </div>
          </article>
        @empty
          <p class="text-sm text-gray-500 italic">Nenhuma ocorrência registrada nas últimas 48 horas.</p>
        @endforelse
      </div>
    </div>
  </section>

  {{-- Ranking Assistentes (lista densa) --}}
  <section class="bg-white rounded-xl shadow-sm ring-1 ring-gray-100 p-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <h2 class="text-base font-semibold text-emerald-700">🥇 Top 5 Assistentes com Mais Visitas</h2>
      <div class="flex items-center gap-2">
        <form method="GET" action="{{ route('coordenador.dashboard') }}">
          <select name="periodo" onchange="this.form.submit()"
                  class="text-xs border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-emerald-500">
            <option value="30"  {{ request('periodo', 30) == 30  ? 'selected' : '' }}>1 mês</option>
            <option value="90"  {{ request('periodo') == 90  ? 'selected' : '' }}>3 meses</option>
            <option value="180" {{ request('periodo') == 180 ? 'selected' : '' }}>6 meses</option>
            <option value="365" {{ request('periodo') == 365 ? 'selected' : '' }}>1 ano</option>
          </select>
        </form>
        <a href="{{ route('coordenador.assistentes.ranking.index') }}" class="text-xs text-emerald-700 hover:underline">Ver todos</a>
      </div>
    </div>

    @if($top5Assistentes->isEmpty())
      <p class="mt-3 text-gray-500 text-sm italic">Sem dados suficientes para {{ request('periodo', 30) }} dias.</p>
    @else
      <ul class="mt-3 divide-y divide-gray-100">
        @foreach ($top5Assistentes as $index => $assistente)
          <li class="flex items-center gap-3 py-2">
            <span class="w-6 text-center text-lg font-bold text-gray-700">{{ $index + 1 }}</span>
            <img src="{{ $assistente->foto_url ?? asset('default-avatar.png') }}"
                 class="w-9 h-9 rounded-full object-cover ring-1 ring-gray-200" alt="{{ $assistente->name }}">
            <div class="min-w-0">
              <p class="truncate font-medium text-gray-800 text-sm">{{ $assistente->name }}</p>
              <p class="text-[12px] text-gray-500">Visitas: {{ $assistente->total_evolucoes }}</p>
            </div>
            @if ($index === 0)
              <span class="ml-auto text-yellow-500 text-xl">👑</span>
            @endif
          </li>
        @endforeach
      </ul>
    @endif
  </section>

  {{-- Gráfico principal (altura moderada, sem overflow lateral) --}}
  <section class="bg-white rounded-xl shadow-sm ring-1 ring-gray-100 p-5">
    <h2 class="text-base font-semibold text-emerald-700 mb-3">📈 Evolução</h2>
    <div class="relative overflow-hidden min-h-[220px]">
      @include('components.grafico-evolucao')
    </div>
  </section>

  {{-- Conjunto de gráficos secundários (cards iguais, altura controlada) --}}
  <section class="bg-white rounded-xl shadow-sm ring-1 ring-gray-100 p-5">
    <h2 class="text-base font-semibold text-emerald-700 mb-4">📊 Indicadores Visuais</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div class="bg-emerald-50/40 rounded-lg ring-1 ring-emerald-100 p-4">
        <h3 class="text-[13px] font-semibold text-emerald-900 mb-2">📋 Solicitações</h3>
        <div class="relative overflow-hidden min-h-[180px]">
          @include('components.grafico-solicitacoes')
        </div>
      </div>
      <div class="bg-emerald-50/40 rounded-lg ring-1 ring-emerald-100 p-4">
        <h3 class="text-[13px] font-semibold text-emerald-900 mb-2">📍 Ocorrências</h3>
        <div class="relative overflow-hidden min-h-[180px]">
          @include('components.graficos.ocorrencias')
        </div>
      </div>
      <div class="bg-emerald-50/40 rounded-lg ring-1 ring-emerald-100 p-4">
        <h3 class="text-[13px] font-semibold text-emerald-900 mb-2">📌 Indicações e Denúncias</h3>
        <div class="relative overflow-hidden min-h-[180px]">
          <x-graficos.indicacoes-denuncias :dadosIndicacoesDenuncias="$dadosIndicacoesDenuncias" />
        </div>
      </div>
    </div>
  </section>

</div>
@endsection
