@extends('layouts.app')

@section('title', 'Programas Sociais')

@section('content')
@php use Illuminate\Support\Facades\Storage; @endphp

<style>
  /* ===== CARD LIMPO, FUNDO BRANCO + ARTE FIXA ===== */
  .pcard{
    position:relative; aspect-ratio:1340/768;
    background:#fff; border-radius:20px; overflow:hidden;
    box-shadow:0 8px 22px rgba(0,0,0,.08); outline:1px solid rgba(0,0,0,.06);
    /* escala base (funciona em todos). Em navegadores modernos, sobrescrevemos via container */
    --s: clamp(11px, 2.6vw, 20px);
  }
  @@supports (container-type: inline-size){
    .pcard{ container-type:inline-size; --s: clamp(11px, 2.1cqw, 22px); }
  }

  /* imagem de fundo oficial (programa.png) — cobre tudo */
  .pcard__bg{ position:absolute; inset:0; width:100%; height:100%; object-fit:cover; z-index:1; }

  /* Centro sempre no meio do card (logo ou título) */
  .pcard__center{
    position:absolute; left:50%; top:50%; transform:translate(-50%,-100%);
    text-align:center; z-index:2; pointer-events:none;
  }
  .pcard__logo{ display:block; width:calc(var(--s)*6); height:auto; object-fit:contain; margin:0 auto; }
  .pcard__title{
    display:block; max-width:calc(var(--s)*15); margin:0 auto;
    color:#065f46; font-weight:800; line-height:1.15;
    font-size:calc(var(--s)*1.75);
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }

  /* Rodapé centralizado e proporcional (uma linha com ellipsis) */
  .pcard__foot{
    position:absolute; left:50%; transform:translateX(-50%);
    bottom:calc(var(--s)*2.7); width:50%; text-align:center; z-index:2;
    color:#fff; text-shadow:0 1px 2px rgba(0,0,0,.35);
  }
  .pcard__foot-label{ font-size:calc(var(--s)*0.9); font-weight:700; line-height:1; margin-bottom:calc(var(--s)*0.35); }
  .pcard__foot-text{ font-size:calc(var(--s)*0.82); line-height:1.1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

  /* Área clicável cobre todo o card */
  .pcard__link{ position:absolute; inset:0; z-index:3; }
</style>

<div class="max-w-6xl mx-auto px-4 py-6">
  <h1 class="text-2xl font-bold text-gray-900 mb-6">Programas Sociais Disponíveis</h1>

  @if($programas->isEmpty())
    <div class="rounded-xl border border-gray-200 bg-white p-8 text-center shadow-sm">
      <p class="text-gray-700 font-medium">Nenhum programa disponível no momento.</p>
      <p class="text-gray-500 text-sm">Volte mais tarde — atualizamos esta lista com frequência.</p>
    </div>
  @else
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-2 gap-6">
      @php
        // Fundo oficial com cache-busting (precisa existir em public/imagens/programa.png)
        $fundoPathGlobal = public_path('imagens/programa.png');
        $temFundoGlobal  = file_exists($fundoPathGlobal);
        $fundoGlobal     = $temFundoGlobal ? asset('imagens/programa.png').'?v='.filemtime($fundoPathGlobal) : null;
      @endphp

      @foreach($programas as $programa)
        @php
          // Normaliza regiões
          $raw = $programa->regioes ?? [];
          if (is_string($raw)) {
              $json = json_decode($raw, true);
              $arr = (json_last_error() === JSON_ERROR_NONE && is_array($json)) ? $json : preg_split('/,|;|\|/', $raw);
          } elseif (is_array($raw)) { $arr = $raw; } else { $arr = []; }
          $regioes = array_values(array_filter(array_map(fn($v)=> trim((string)$v), $arr)));

          // Logo
          $fotoRel = $programa->foto_perfil ?? null;
          $temLogo = $fotoRel && Storage::disk('public')->exists($fotoRel);
        @endphp

        <article class="pcard">
          {{-- FUNDO BASE: imagem oficial (sem opacidade) ou branco puro se faltar --}}
          @if($fundoGlobal)
            <img src="{{ $fundoGlobal }}" alt="" class="pcard__bg" loading="lazy" decoding="async">
          @endif

          {{-- Centro: logo OU título (sempre central) --}}
          <div class="pcard__center">
            @if($temLogo)
              <img src="{{ asset('storage/'.$fotoRel) }}" alt="Logo do programa {{ $programa->nome ?? 'Programa' }}" class="pcard__logo" loading="lazy">
            @else
              <span class="pcard__title" title="{{ $programa->nome }}">{{ $programa->nome ?? 'Programa' }}</span>
            @endif
          </div>

          {{-- Rodapé centralizado --}}
          <div class="pcard__foot">
            <div class="pcard__foot-label">Regiões Disponíveis</div>
            <div class="pcard__foot-text" title="{{ empty($regioes) ? '—' : implode(', ', $regioes) }}">
              {{ empty($regioes) ? '—' : implode(', ', $regioes) }}
            </div>
          </div>

          <a href="{{ route('cidadao.programas.ver', $programa->id) }}" class="pcard__link" aria-label="Ver detalhes de {{ $programa->nome ?? 'Programa' }}">
            <span class="sr-only">Ver detalhes</span>
          </a>
        </article>
      @endforeach
    </div>

    <div class="mt-8">
      {{ $programas->links() }}
    </div>
  @endif
</div>
@endsection
