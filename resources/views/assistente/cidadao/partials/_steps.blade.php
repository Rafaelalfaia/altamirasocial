@php
  $etapas = [
    'dados'          => ['label'=>'Dados',          'route'=>route('assistente.cidadao.dados.editar', $cidadao->id)],
    'moradia'        => ['label'=>'Moradia',        'route'=>route('assistente.cidadao.moradia.editar', $cidadao->id)],
    'trabalho'       => ['label'=>'Trabalho',       'route'=>route('assistente.cidadao.trabalho.editar', $cidadao->id)],
    'acessibilidade' => ['label'=>'Acessibilidade', 'route'=>route('assistente.cidadao.acessibilidade.editar', $cidadao->id)],
  ];
@endphp

<nav class="mb-4" x-data>
  <div class="relative px-1">
    <div class="pointer-events-none absolute left-0 top-0 h-full w-4 bg-gradient-to-r from-white to-transparent md:hidden"></div>
    <div class="pointer-events-none absolute right-0 top-0 h-full w-4 bg-gradient-to-l from-white to-transparent md:hidden"></div>

    <ol class="flex items-center gap-1.5 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory py-1 pr-1 md:py-0 md:pr-0"
        x-init="$nextTick(()=>{ const el=$refs.active?.closest('li'); el?.scrollIntoView({inline:'center',behavior:'instant',block:'nearest'}); })">
      @foreach($etapas as $key => $info)
        @php $ativo = ($atual ?? '') === $key; @endphp
        <li class="snap-start shrink-0">
          <a href="{{ $info['route'] }}"
             @if($ativo) x-ref="active" aria-current="step" @endif
             class="inline-flex items-center gap-1.5 rounded-lg border text-[13px] md:text-sm leading-tight
                    px-2.5 py-1.5 md:px-3.5 md:py-2 transition
                    {{ $ativo ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-indigo-700 border-indigo-200 hover:bg-indigo-50' }}">
            <span class="whitespace-nowrap font-medium">{{ $info['label'] }}</span>
          </a>
        </li>
      @endforeach
    </ol>
  </div>
</nav>

<style>
.no-scrollbar::-webkit-scrollbar{display:none}
.no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
</style>
