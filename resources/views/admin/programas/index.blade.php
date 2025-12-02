@extends('layouts.app')

@section('title', 'Programas Sociais')

@section('content')
@php
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Support\Str;

  $tot = $totais ?? [
    'todos'        => method_exists($programas, 'total') ? $programas->total() : ($programas->count() ?? 0),
    'ativados'     => 0,
    'desativados'  => 0,
    'recomendados' => 0,
  ];

  $fmtRegioes = function($reg) {
      if (is_string($reg)) {
          $json = json_decode($reg, true);
          $arr  = (json_last_error()===JSON_ERROR_NONE && is_array($json)) ? $json : preg_split('/,|;|\|/', $reg);
      } elseif (is_array($reg)) { $arr = $reg; } else { $arr = []; }
      $arr = array_values(array_filter(array_map(fn($v)=> trim((string)$v), $arr)));
      return implode(', ', $arr);
  };

  $podeRecomendar = auth()->user()?->can('programas.recomendar') || auth()->user()?->hasRole('Admin');

  $isAll = !request('status') && !request('rec') && !request('busca');
  $isRec = request('rec') == '1';
  $isAtv = request('status') === 'ativado';
  $isDes = request('status') === 'desativado';
@endphp

<div class="mx-auto max-w-6xl px-3 md:px-4 lg:px-6 py-6 space-y-4">

  {{-- Cabeçalho --}}
  <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0">
      <h1 class="text-xl font-extrabold text-emerald-800">📦 Programas Sociais</h1>
      <p class="text-[12px] text-gray-500">Gerencie programas e destaque (★) no app do cidadão.</p>
    </div>
    @can('programas.criar')
      <a href="{{ route('admin.programas.create') }}"
         class="inline-flex items-center justify-center rounded-md bg-emerald-700 px-3 py-2 text-xs font-semibold text-white shadow hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-600">
        + Novo Programa
      </a>
    @endcan
  </div>

  {{-- Alerts --}}
  @if(session('success') || session('status'))
    <div class="rounded-md bg-emerald-50 px-3 py-2 text-sm text-emerald-800 ring-1 ring-emerald-100" role="status" aria-live="polite">
      {{ session('success') ?? session('status') }}
    </div>
  @endif

  {{-- Totais (compacto) --}}
  <section aria-labelledby="stats" class="grid grid-cols-2 gap-2 md:grid-cols-4">
    <div class="rounded-lg bg-white p-3 shadow-sm ring-1 ring-gray-100">
      <p class="text-[11px] text-gray-500">Total</p>
      <div class="text-xl font-bold text-gray-900">{{ $tot['todos'] }}</div>
    </div>
    <div class="rounded-lg bg-white p-3 shadow-sm ring-1 ring-emerald-100">
      <p class="text-[11px] text-gray-500">Ativados</p>
      <div class="text-xl font-bold text-emerald-700">{{ $tot['ativados'] }}</div>
    </div>
    <div class="rounded-lg bg-white p-3 shadow-sm ring-1 ring-gray-100">
      <p class="text-[11px] text-gray-500">Desativados</p>
      <div class="text-xl font-bold text-gray-700">{{ $tot['desativados'] }}</div>
    </div>
    <div class="rounded-lg bg-white p-3 shadow-sm ring-1 ring-amber-100">
      <p class="text-[11px] text-gray-500">Recomendados</p>
      <div class="text-xl font-bold text-amber-600">{{ $tot['recomendados'] }}</div>
    </div>
  </section>

  {{-- Toolbar: chips + filtros (super compacto) --}}
  <div class="rounded-lg bg-white p-3 shadow-sm ring-1 ring-gray-100 space-y-2">
    {{-- Chips --}}
    <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5">
      <a href="{{ route('admin.programas.index') }}"
         class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-medium border {{ $isAll ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
         Todos
      </a>
      <a href="{{ route('admin.programas.index', array_filter(['rec' => 1, 'busca' => request('busca'), 'status'=>request('status')])) }}"
         class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-medium border {{ $isRec ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
         ★ Recomendados
      </a>
      <a href="{{ route('admin.programas.index', array_filter(['status' => 'ativado', 'busca' => request('busca'), 'rec' => request('rec')])) }}"
         class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-medium border {{ $isAtv ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
         Ativados
      </a>
      <a href="{{ route('admin.programas.index', array_filter(['status' => 'desativado', 'busca' => request('busca'), 'rec' => request('rec')])) }}"
         class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-medium border {{ $isDes ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
         Desativados
      </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('admin.programas.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-2">
      <div class="md:col-span-6">
        <label class="mb-1 block text-[11px] text-gray-500">Buscar</label>
        <div class="relative">
          <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Nome ou descrição…"
                 class="w-full rounded border border-gray-300 px-3 py-2 pe-9 text-xs focus:border-emerald-500 focus:ring-emerald-500">
          @if(request('busca'))
            <a href="{{ route('admin.programas.index', array_filter(request()->except('busca'))) }}"
               class="absolute inset-y-0 right-0 grid w-8 place-items-center text-gray-400 hover:text-gray-600"
               aria-label="Limpar busca">×</a>
          @endif
        </div>
      </div>

      <div class="md:col-span-3">
        <label class="mb-1 block text-[11px] text-gray-500">Status</label>
        <select name="status"
                class="w-full rounded border border-gray-300 px-3 py-2 text-xs focus:border-emerald-500 focus:ring-emerald-500">
          <option value="">Todos</option>
          <option value="ativado" @selected(request('status')==='ativado')>Ativado</option>
          <option value="desativado" @selected(request('status')==='desativado')>Desativado</option>
        </select>
      </div>

      <div class="md:col-span-3 flex items-end justify-between gap-2">
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="rec" value="1" @checked(request('rec')=='1') class="rounded border-gray-300">
          <span class="text-xs">Somente recomendados</span>
        </label>
        <div class="flex gap-1.5">
          <a href="{{ route('admin.programas.index') }}"
             class="rounded-md border border-gray-300 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50">Limpar</a>
          <button class="rounded-md bg-slate-800 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-900">
            Filtrar
          </button>
        </div>
      </div>
    </form>
  </div>

  {{-- Lista MOBILE (compacta; ordem visível quando recomendado) --}}
    <div class="space-y-2 md:hidden">
    @forelse($programas as $programa)
        @php
        $regioesTxt = $fmtRegioes($programa->regioes);
        $temLogo    = $programa->foto_perfil && Storage::disk('public')->exists($programa->foto_perfil);
        $ordemAtual = (int) ($programa->recomendacao_ordem ?? 0);
        @endphp
        <article class="rounded-lg bg-white p-3 shadow-sm ring-1 ring-gray-100">
        <div class="flex items-start gap-2">
            <div class="h-9 w-9 overflow-hidden rounded bg-gray-100 ring-1 ring-gray-200 grid place-items-center">
            @if($temLogo)
                <img src="{{ asset('storage/'.$programa->foto_perfil) }}" class="h-full w-full object-cover" alt="">
            @else
                <span class="text-[9px] leading-3 text-gray-400 text-center">sem<br>logo</span>
            @endif
            </div>

            <div class="min-w-0 flex-1">
            <div class="flex items-center gap-1.5">
                <h3 class="truncate text-[13px] font-semibold text-gray-900">{{ $programa->nome }}</h3>
                <span class="rounded px-1.5 py-0.5 text-[10px] font-semibold
                            {{ $programa->status==='ativado' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-700' }}">
                {{ $programa->status==='ativado' ? 'Ativado' : 'Desativado' }}
                </span>
            </div>

            {{-- Ações --}}
            <div class="mt-2 flex items-center justify-between gap-2">
                <a href="{{ route('admin.programas.show', $programa->id) }}" class="text-[12px] font-medium text-emerald-700 hover:underline">Ver</a>

                <div class="flex items-center gap-1.5">
                @if($podeRecomendar)
                    {{-- estrela --}}
                    <form method="POST" action="{{ route('admin.programas.recomendar', ['programa' => $programa->id]) }}">
                    @csrf
                    <input type="hidden" name="ativar" value="{{ $programa->recomendado ? 0 : 1 }}">
                    <button class="rounded border px-2 py-1 text-[11px] font-semibold transition
                                    {{ $programa->recomendado ? 'border-amber-500 text-amber-600 hover:bg-amber-50' : 'border-slate-300 text-slate-600 hover:bg-slate-50' }}">
                        {{ $programa->recomendado ? '★' : '☆' }}
                    </button>
                    </form>

                    @if($programa->recomendado)
                    {{-- ordem sempre visível no mobile também --}}
                    <form method="POST" action="{{ route('admin.programas.recomendar', ['programa' => $programa->id]) }}">
                        @csrf
                        <input type="hidden" name="ativar" value="1">
                        <input type="hidden" name="ordem" value="{{ max(0, $ordemAtual - 1) }}">
                        <button class="rounded border border-slate-300 px-2 py-1 text-[11px] text-slate-700">−</button>
                    </form>

                    <form method="POST" action="{{ route('admin.programas.recomendar', ['programa' => $programa->id]) }}" class="flex items-center gap-1">
                        @csrf
                        <input type="hidden" name="ativar" value="1">
                        <input type="number" name="ordem" min="0" step="1" value="{{ $ordemAtual }}"
                            class="w-12 rounded border px-2 py-1 text-[11px]">
                        <button class="rounded bg-slate-800 px-2 py-1 text-[11px] font-semibold text-white">OK</button>
                    </form>

                    <form method="POST" action="{{ route('admin.programas.recomendar', ['programa' => $programa->id]) }}">
                        @csrf
                        <input type="hidden" name="ativar" value="1">
                        <input type="hidden" name="ordem" value="{{ $ordemAtual + 1 }}">
                        <button class="rounded border border-slate-300 px-2 py-1 text-[11px] text-slate-700">+</button>
                    </form>
                    @endif
                @endif
                </div>
            </div>
            </div>
        </div>
        </article>
    @empty
        <div class="rounded-lg bg-white p-5 text-center text-gray-500 shadow-sm ring-1 ring-gray-100">Nenhum programa encontrado.</div>
    @endforelse

    <div>{{ $programas->appends(request()->query())->links() }}</div>
    </div>


    {{-- Tabela DESKTOP (super compacta, sem overflow) --}}
    <div class="hidden md:block rounded-lg bg-white shadow-sm ring-1 ring-gray-100">
    <table class="min-w-full table-fixed text-xs">
        <colgroup>
        <col class="w-[62%]">  {{-- Programa --}}
        <col class="w-[14%]">  {{-- Status --}}
        <col class="w-[16%]">  {{-- Destaque & Ordem --}}
        <col class="w-[8%]">   {{-- Ações --}}
        </colgroup>

        <thead class="bg-gray-50 text-left text-gray-700">
        <tr>
            <th class="px-3 py-2">Programa</th>
            <th class="px-3 py-2">Status</th>
            <th class="px-3 py-2">Destaque &amp; Ordem</th>
            <th class="px-3 py-2 text-right">Ações</th>
        </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
        @forelse($programas as $programa)
            @php
            $regioesTxt = $fmtRegioes($programa->regioes);
            $temLogo    = $programa->foto_perfil && Storage::disk('public')->exists($programa->foto_perfil);
            $ordemAtual = (int) ($programa->recomendacao_ordem ?? 0);
            @endphp
            <tr class="hover:bg-gray-50">
            {{-- PROGRAMA (nome) --}}
            <td class="px-3 py-1.5">
                <div class="flex items-start gap-2.5">
                <div class="h-7 w-7 overflow-hidden rounded bg-gray-100 ring-1 ring-gray-200 grid place-items-center">
                    @if($temLogo)
                    <img src="{{ asset('storage/'.$programa->foto_perfil) }}" class="h-full w-full object-cover" alt="">
                    @else
                    <span class="text-[9px] leading-3 text-gray-400 text-center">sem<br>logo</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <span class="block max-w-[60ch] truncate font-semibold text-gray-900 text-[13px]"
                        title="{{ $programa->nome }}">
                    {{ $programa->nome }}
                    </span>
                    {{-- Meta em tooltip para não poluir --}}
                    <span class="block text-[0]"
                        title="Público: {{ $programa->publico_alvo ?: '—' }} &#10;Regiões: {{ $regioesTxt ?: '—' }} &#10;Resumo: {{ \Illuminate\Support\Str::limit(strip_tags($programa->descricao), 160) }}">
                    &nbsp;
                    </span>
                </div>
                </div>
            </td>

            {{-- STATUS --}}
            <td class="px-3 py-1.5 whitespace-nowrap">
                <span class="rounded px-1.5 py-0.5 text-[10px] font-semibold
                            {{ $programa->status==='ativado' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-700' }}">
                {{ $programa->status==='ativado' ? 'Ativado' : 'Desativado' }}
                </span>
            </td>

            {{-- DESTAQUE + ORDEM (sempre visível quando recomendado) --}}
            <td class="px-3 py-1.5">
                <div class="flex flex-wrap items-center gap-1.5">
                @if($podeRecomendar)
                    {{-- Toggle estrela --}}
                    <form method="POST" action="{{ route('admin.programas.recomendar', ['programa' => $programa->id]) }}">
                    @csrf
                    <input type="hidden" name="ativar" value="{{ $programa->recomendado ? 0 : 1 }}">
                    <button class="rounded border px-2 py-1 text-[11px] font-semibold transition
                                    {{ $programa->recomendado ? 'border-amber-500 text-amber-600 hover:bg-amber-50' : 'border-slate-300 text-slate-600 hover:bg-slate-50' }}"
                            title="{{ $programa->recomendado ? 'Remover destaque' : 'Colocar em destaque' }}">
                        {{ $programa->recomendado ? '★' : '☆' }}
                    </button>
                    </form>

                    @if($programa->recomendado)
                    {{-- Ordem: -, input, + (sempre visíveis) --}}
                    <form method="POST" action="{{ route('admin.programas.recomendar', ['programa' => $programa->id]) }}">
                        @csrf
                        <input type="hidden" name="ativar" value="1">
                        <input type="hidden" name="ordem" value="{{ max(0, $ordemAtual - 1) }}">
                        <button class="rounded border border-slate-300 px-2 py-1 text-[11px] text-slate-700 hover:bg-slate-50"
                                title="Diminuir ordem (sobe)">−</button>
                    </form>

                    <form method="POST" action="{{ route('admin.programas.recomendar', ['programa' => $programa->id]) }}" class="flex items-center gap-1">
                        @csrf
                        <input type="hidden" name="ativar" value="1">
                        <input type="number" name="ordem" min="0" step="1" value="{{ $ordemAtual }}"
                            class="w-14 rounded border px-2 py-1 text-[11px]" title="Ordem atual">
                        <button class="rounded bg-slate-800 px-2 py-1 text-[11px] font-semibold text-white hover:bg-slate-900">OK</button>
                    </form>

                    <form method="POST" action="{{ route('admin.programas.recomendar', ['programa' => $programa->id]) }}">
                        @csrf
                        <input type="hidden" name="ativar" value="1">
                        <input type="hidden" name="ordem" value="{{ $ordemAtual + 1 }}">
                        <button class="rounded border border-slate-300 px-2 py-1 text-[11px] text-slate-700 hover:bg-slate-50"
                                title="Aumentar ordem (desce)">+</button>
                    </form>
                    @endif
                @else
                    <span class="text-[11px] text-gray-600">
                    {{ $programa->recomendado ? 'Recomendado (ordem '.$ordemAtual.')' : '—' }}
                    </span>
                @endif
                </div>
            </td>

            {{-- AÇÕES --}}
            <td class="px-3 py-1.5">
                <div class="flex items-center gap-2 justify-end whitespace-nowrap">
                <a href="{{ route('admin.programas.show', $programa->id) }}" class="text-[12px] text-emerald-700 hover:underline">Ver</a>
                @can('programas.editar')
                    <a href="{{ route('admin.programas.edit', $programa->id) }}" class="text-[12px] text-slate-700 hover:underline">Editar</a>
                @endcan
                @can('programas.excluir')
                    <form action="{{ route('admin.programas.destroy', $programa->id) }}" method="POST"
                        onsubmit="return confirm('Tem certeza que deseja excluir este programa?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-[12px] text-red-600 hover:underline">Excluir</button>
                    </form>
                @endcan
                </div>
            </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-3 py-5 text-center text-gray-500">Nenhum programa encontrado.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>


  {{-- Paginação (desktop) --}}
  <div class="hidden md:block">
    {{ $programas->appends(request()->query())->links() }}
  </div>
</div>
@endsection
