@extends('layouts.app')

@section('title', 'Detalhes do Programa')

@section('content')
@php
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Support\Str;

  // Helper: normaliza "regioes" (string/json/array) em texto curto
  $fmtRegioes = function($reg) {
      if (is_string($reg)) {
          $json = json_decode($reg, true);
          $arr  = (json_last_error()===JSON_ERROR_NONE && is_array($json)) ? $json : preg_split('/,|;|\|/', $reg);
      } elseif (is_array($reg)) { $arr = $reg; } else { $arr = []; }
      $arr = array_values(array_filter(array_map(fn($v)=> trim((string)$v), $arr)));
      return implode(', ', $arr);
  };

  $temLogo    = $programa->foto_perfil && Storage::disk('public')->exists($programa->foto_perfil);
  $regioesTxt = $fmtRegioes($programa->regioes);
  $ordemAtual = (int) ($programa->recomendacao_ordem ?? 0);
@endphp

<div class="mx-auto max-w-6xl px-4 md:px-6 py-8 space-y-6">

  {{-- Cabeçalho --}}
  <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div class="min-w-0">
      <div class="flex items-center gap-3">
        <div class="h-14 w-14 overflow-hidden rounded-lg ring-1 ring-gray-200 bg-gray-100 flex items-center justify-center">
          @if($temLogo)
            <img src="{{ asset('storage/'.$programa->foto_perfil) }}" class="h-full w-full object-cover" alt="">
          @else
            <span class="text-[10px] leading-3 text-gray-400 text-center">sem<br>logo</span>
          @endif
        </div>
        <div>
          <h1 class="text-2xl font-extrabold text-emerald-800">
            {{ $programa->nome }}
          </h1>
          <div class="mt-1 flex flex-wrap items-center gap-2">
            @if($programa->status === 'ativado')
              <span class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Ativado</span>
            @else
              <span class="rounded bg-gray-200 px-2 py-0.5 text-xs font-semibold text-gray-700">Desativado</span>
            @endif

            @if($programa->recomendado)
              <span class="rounded-full bg-amber-500 px-2 py-0.5 text-[10px] font-bold text-white">★ Recomendado</span>
              <span class="text-xs text-gray-500">Ordem: <b>{{ $ordemAtual }}</b></span>
            @endif
          </div>
        </div>
      </div>
      @if(!empty($programa->publico_alvo) || !empty($regioesTxt))
        <p class="mt-2 text-sm text-gray-600">
          @if(!empty($programa->publico_alvo)) <span class="mr-3">🎯 <b>Público:</b> {{ $programa->publico_alvo }}</span>@endif
          @if(!empty($regioesTxt)) <span>📍 <b>Regiões:</b> {{ $regioesTxt }}</span>@endif
        </p>
      @endif
    </div>

    <div class="flex flex-wrap items-center gap-2 md:justify-end">
      <a href="{{ route('admin.programas.index') }}"
         class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">← Voltar</a>

      @can('programas.excluir')
        <form action="{{ route('admin.programas.destroy', $programa->id) }}" method="POST"
              onsubmit="return confirm('Tem certeza que deseja excluir este programa?');">
          @csrf @method('DELETE')
          <button type="submit" class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
            Excluir
          </button>
        </form>
      @endcan
    </div>
  </div>

  {{-- Recomendar / Ordem --}}
  @can('programas.recomendar')
    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
      <h2 class="mb-2 text-sm font-semibold text-gray-800">Destaque (index do cidadão)</h2>
      <div class="flex flex-wrap items-center gap-2">
        {{-- Toggle --}}
        <form method="POST" action="{{ route('admin.programas.recomendar', ['programa' => $programa->id]) }}">
          @csrf
          <input type="hidden" name="ativar" value="{{ $programa->recomendado ? 0 : 1 }}">
          <button class="rounded border px-3 py-1.5 text-sm font-semibold
                         {{ $programa->recomendado ? 'border-amber-500 text-amber-700 hover:bg-amber-50' : 'border-slate-300 text-slate-700 hover:bg-slate-50' }}">
            {{ $programa->recomendado ? '★ Remover destaque' : '☆ Colocar em destaque' }}
          </button>
        </form>

        {{-- Ordem (se estiver recomendado) --}}
        @if($programa->recomendado)
          <form method="POST" action="{{ route('admin.programas.recomendar', ['programa' => $programa->id]) }}">
            @csrf
            <input type="hidden" name="ativar" value="1">
            <input type="hidden" name="ordem" value="{{ max(0, $ordemAtual - 1) }}">
            <button class="rounded border border-slate-300 px-2 py-1 text-xs text-slate-600 hover:bg-slate-50" title="Diminuir ordem (sobe)">−</button>
          </form>

          <form method="POST" action="{{ route('admin.programas.recomendar', ['programa' => $programa->id]) }}" class="inline-flex items-center gap-2">
            @csrf
            <input type="hidden" name="ativar" value="1">
            <label class="text-xs text-gray-500">Ordem</label>
            <input type="number" name="ordem" min="0" step="1" value="{{ $ordemAtual }}"
                   class="w-20 rounded border px-2 py-1 text-xs">
            <button class="rounded bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-900">Salvar</button>
          </form>

          <form method="POST" action="{{ route('admin.programas.recomendar', ['programa' => $programa->id]) }}">
            @csrf
            <input type="hidden" name="ativar" value="1">
            <input type="hidden" name="ordem" value="{{ $ordemAtual + 1 }}">
            <button class="rounded border border-slate-300 px-2 py-1 text-xs text-slate-600 hover:bg-slate-50" title="Aumentar ordem (desce)">+</button>
          </form>
          <p class="ml-2 text-[11px] text-gray-500">Dica: 0–2 aparecem no topo do cidadão.</p>
        @endif
      </div>

      @if($programa->recomendado)
        <div class="mt-2 text-xs text-gray-500">
          <span>Recomendado em: <b>{{ optional($programa->recomendado_em)->format('d/m/Y H:i') ?? '—' }}</b></span>
          @if(!empty($programa->recomendado_por))
            <span class="ml-3">por ID <b>{{ $programa->recomendado_por }}</b></span>
          @endif
        </div>
      @endif
    </div>
  @endcan

  {{-- Conteúdo --}}
  <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
    {{-- Coluna esquerda (principal) --}}
    <div class="md:col-span-2 space-y-6">
      {{-- Descrição --}}
      <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
        <h2 class="mb-2 text-sm font-semibold text-gray-800">Descrição</h2>
        <div class="prose max-w-none prose-sm prose-headings:mt-4 prose-headings:mb-2 prose-p:my-2">
          @if(!empty($programa->descricao))
            {!! $programa->descricao !!}
          @else
            <p class="text-gray-500">—</p>
          @endif
        </div>
      </div>

      {{-- Regras / Público / Vagas / Regiões (resumo) --}}
      <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
        <h2 class="mb-3 text-sm font-semibold text-gray-800">Informações</h2>
        <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div>
            <dt class="text-xs text-gray-500">Público-alvo</dt>
            <dd class="text-sm text-gray-800">{{ $programa->publico_alvo ?: '—' }}</dd>
          </div>
          <div>
            <dt class="text-xs text-gray-500">Vagas</dt>
            <dd class="text-sm text-gray-800">{{ $programa->vagas ?? '—' }}</dd>
          </div>
          <div class="sm:col-span-2">
            <dt class="text-xs text-gray-500">Regiões</dt>
            <dd class="text-sm text-gray-800">{{ $regioesTxt ?: '—' }}</dd>
          </div>
        </dl>
      </div>
    </div>

    {{-- Coluna direita (metadados) --}}
    <div class="space-y-6">
      <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
        <h2 class="mb-3 text-sm font-semibold text-gray-800">Status & Metadados</h2>
        <dl class="space-y-2 text-sm">
          <div class="flex items-center justify-between">
            <dt class="text-gray-500">Status</dt>
            <dd>
              @if($programa->status==='ativado')
                <span class="rounded bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Ativado</span>
              @else
                <span class="rounded bg-gray-200 px-2 py-0.5 text-xs font-semibold text-gray-700">Desativado</span>
              @endif
            </dd>
          </div>
          <div class="flex items-center justify-between">
            <dt class="text-gray-500">Criado em</dt>
            <dd class="text-gray-800">{{ optional($programa->created_at)->format('d/m/Y H:i') ?? '—' }}</dd>
          </div>
          <div class="flex items-center justify-between">
            <dt class="text-gray-500">Atualizado em</dt>
            <dd class="text-gray-800">{{ optional($programa->updated_at)->format('d/m/Y H:i') ?? '—' }}</dd>
          </div>
          <div class="flex items-center justify-between">
            <dt class="text-gray-500">ID</dt>
            <dd class="text-gray-800">#{{ $programa->id }}</dd>
          </div>
        </dl>
      </div>

      {{-- Ações rápidas duplicadas (mobile/scroll longo) --}}
      <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
        <h2 class="mb-3 text-sm font-semibold text-gray-800">Ações</h2>
        <div class="flex flex-wrap gap-2">
          <a href="{{ route('admin.programas.index') }}"
             class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">← Voltar</a>

          @can('programas.excluir')
            <form action="{{ route('admin.programas.destroy', $programa->id) }}" method="POST"
                  onsubmit="return confirm('Tem certeza que deseja excluir este programa?');">
              @csrf @method('DELETE')
              <button type="submit" class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                Excluir
              </button>
            </form>
          @endcan
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
