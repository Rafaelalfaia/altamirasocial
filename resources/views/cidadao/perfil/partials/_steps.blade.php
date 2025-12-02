@php
  $etapas = [
    'dados'          => ['label' => 'Dados',          'route' => 'cidadao.perfil.dados'],
    'moradia'        => ['label' => 'Moradia',        'route' => 'cidadao.perfil.moradia'],
    'trabalho'       => ['label' => 'Trabalho',       'route' => 'cidadao.perfil.trabalho'],
    'acessibilidade' => ['label' => 'Acessibilidade', 'route' => 'cidadao.perfil.acessibilidade'],
  ];
@endphp

<nav class="mb-4" x-data>
  <div class="relative px-1">

    {{-- leves indicações de scroll apenas no mobile (bem estreitas) --}}
    <div class="pointer-events-none absolute left-0 top-0 h-full w-4 bg-gradient-to-r from-white to-transparent md:hidden"></div>
    <div class="pointer-events-none absolute right-0 top-0 h-full w-4 bg-gradient-to-l from-white to-transparent md:hidden"></div>

    <ol
      class="flex items-center gap-1.5 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory
             py-1 pr-1 md:py-0 md:pr-0"
      x-init="
        $nextTick(() => {
          const el = $refs.active?.closest('li');
          el?.scrollIntoView({ inline: 'center', behavior: 'instant', block: 'nearest' });
        })
      "
      role="list"
      aria-label="Etapas do cadastro"
    >
      @foreach($etapas as $key => $info)
        @php
          $ativo = ($atual ?? '') === $key;
          $num   = $loop->iteration;
        @endphp

        <li class="snap-start shrink-0">
          <a
            href="{{ route($info['route']) }}"
            @if($ativo) x-ref="active" aria-current="step" @endif
            class="inline-flex items-center gap-1.5 rounded-lg border text-[13px] md:text-sm leading-tight
                   px-2.5 py-1.5 md:px-3.5 md:py-2 transition
                   {{ $ativo
                      ? 'bg-indigo-600 text-white border-indigo-600'
                      : 'bg-white text-indigo-700 border-indigo-200 hover:bg-indigo-50'
                   }}"
          >
            {{-- número só em md+ (economiza altura no celular) --}}
            <span class="{{ $ativo ? 'bg-white/20 text-white' : 'bg-indigo-50 text-indigo-700' }}
                         hidden md:inline-flex h-5 w-5 items-center justify-center rounded-full text-[11px] font-semibold">
              {{ $num }}
            </span>

            <span class="whitespace-nowrap font-medium">{{ $info['label'] }}</span>
          </a>
        </li>

        {{-- separador discreto só em md+ --}}
        @if(!$loop->last)
          <li class="hidden md:block text-gray-300 text-sm">→</li>
        @endif
      @endforeach
    </ol>
  </div>
</nav>

<style>
.no-scrollbar::-webkit-scrollbar{display:none}
.no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
</style>
