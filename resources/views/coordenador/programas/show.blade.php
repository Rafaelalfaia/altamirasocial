@extends('layouts.app')

@section('title', 'Detalhes da Inscrição')

@section('content')
@php
  if (!function_exists('cpfMask')) {
    function cpfMask($v){ $d=preg_replace('/\D+/','',(string)$v); return strlen($d)===11?substr($d,0,3).'.'.substr($d,3,3).'.'.substr($d,6,3).'-'.substr($d,9,2):($v?:'—'); }
  }
  if (!function_exists('cepMask')) {
    function cepMask($v){ $d=preg_replace('/\D+/','',(string)$v); return strlen($d)===8?substr($d,0,5).'-'.substr($d,5):($v?:'—'); }
  }
  if (!function_exists('foneMask')) {
    function foneMask($v){
      $d = preg_replace('/\D+/','',(string)$v);
      if(strlen($d)==11) return '('.substr($d,0,2).') '.substr($d,2,5).'-'.substr($d,7);
      if(strlen($d)==10) return '('.substr($d,0,2).') '.substr($d,2,4).'-'.substr($d,6);
      return $v ?: '—';
    }
  }
@endphp

<div class="max-w-6xl mx-auto bg-white shadow rounded p-6 mt-6">

  {{-- Cabeçalho --}}
  @php
    $inscritoNome = optional($inscricao->dependente)->nome ?? optional($inscricao->cidadao)->nome ?? 'Inscrição';
  @endphp
  <div class="flex items-start justify-between gap-4 mb-4">
    <div>
      <h1 class="text-2xl font-bold text-green-800">Inscrição — {{ $inscritoNome }}</h1>
      <div class="text-sm text-gray-600">Programa: <span class="font-medium text-gray-800">{{ $programa->nome }}</span></div>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('coordenador.programas.inscritos', [$programa->id, 'status' => $inscricao->status]) }}"
         class="px-3 py-2 rounded bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm">← Voltar</a>

      <a href="{{ route('coordenador.programas.inscricoes.edit', [$programa->id, $inscricao->id]) }}"
         class="px-3 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white text-sm">Editar</a>

      <form method="POST"
            action="{{ route('coordenador.programas.inscricoes.destroy', [$programa->id, $inscricao->id]) }}"
            onsubmit="return confirm('Deseja EXCLUIR esta inscrição? Esta ação não pode ser desfeita.');">
        @csrf @method('DELETE')
        <button class="px-3 py-2 rounded bg-red-600 hover:bg-red-700 text-white text-sm">Excluir</button>
      </form>
    </div>
  </div>

  {{-- Cards de status/infos rápidas --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="rounded-lg border bg-gray-50 p-4">
      <div class="text-gray-600 text-sm">Status</div>
      <div>
        <span class="inline-block mt-1 px-3 py-1 text-sm font-semibold rounded-full
          @if($inscricao->status==='aprovado') bg-green-100 text-green-700
          @elseif($inscricao->status==='pendente') bg-yellow-100 text-yellow-700
          @else bg-red-100 text-red-700 @endif">
          {{ ucfirst($inscricao->status) }}
        </span>
      </div>
    </div>

    <div class="rounded-lg border bg-gray-50 p-4">
      <div class="text-gray-600 text-sm">Região</div>
      <div class="text-gray-900 mt-1">{{ $inscricao->regiao ?? '—' }}</div>
    </div>

    <div class="rounded-lg border bg-gray-50 p-4">
      <div class="text-gray-600 text-sm">Registrado em</div>
      <div class="text-gray-900 mt-1">{{ optional($inscricao->created_at)->format('d/m/Y H:i') }}</div>
    </div>
  </div>

  {{-- Blocos: Inscrito e (se houver) Responsável --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="rounded-lg border bg-gray-50 p-4">
      <div class="text-gray-600 text-sm">Inscrito</div>
      <div class="mt-1">
        <div class="font-semibold text-indigo-700">{{ $inscritoNome }}</div>

        @if($inscricao->dependente)
          {{-- Dados do dependente --}}
          @php $dep = $inscricao->dependente; @endphp
          <div class="text-sm text-gray-800 mt-1">CPF: {{ cpfMask($dep->cpf ?? '') }}</div>
          <div class="text-sm text-gray-800">Parentesco: {{ $dep->grau_parentesco ?? '—' }}</div>
          <div class="text-sm text-gray-800">Nascimento:
            {{ $dep->data_nascimento ? \Carbon\Carbon::parse($dep->data_nascimento)->format('d/m/Y') : '—' }}
          </div>
          <div class="text-sm text-gray-800">Sexo: {{ $dep->sexo ? ucfirst($dep->sexo) : '—' }}</div>
        @else
          {{-- Dados do titular --}}
          @php $cid = $inscricao->cidadao; @endphp
          <div class="text-sm text-gray-800 mt-1">CPF: {{ cpfMask($cid->cpf ?? '') }}</div>
          <div class="text-sm text-gray-800">Telefone: {{ foneMask($cid->telefone ?? '') }}</div>
          <div class="text-sm text-gray-800">Renda Familiar:
            @if(!is_null($cid->renda_total_familiar))
              R$ {{ number_format((float)$cid->renda_total_familiar, 2, ',', '.') }}
            @else — @endif
          </div>
          <div class="text-sm text-gray-800">Pessoas na residência: {{ $cid->pessoas_na_residencia ?? '—' }}</div>
        @endif
      </div>
    </div>

    <div class="rounded-lg border bg-gray-50 p-4">
      <div class="text-gray-600 text-sm">{{ $inscricao->dependente ? 'Responsável' : 'Titular' }}</div>
      @php $cid = $inscricao->cidadao; @endphp
      <div class="mt-1">
        <div class="font-semibold text-gray-900">{{ $cid->nome ?? '—' }}</div>
        <div class="text-sm text-gray-800 mt-1">CPF: {{ cpfMask($cid->cpf ?? '') }}</div>
        <div class="text-sm text-gray-800">Telefone: {{ foneMask($cid->telefone ?? '') }}</div>
      </div>
    </div>
  </div>

  {{-- Endereço (sempre mostra; usa domicílio do cidadão/responsável) --}}
  <div class="rounded-lg border bg-gray-50 p-4 mb-6">
    <div class="text-gray-600 text-sm">
      Endereço {{ $inscricao->dependente ? '(domicílio do responsável)' : '' }}
    </div>

    @php
      // Para montar cidade/uf, tente via relações: bairro->cidade->estado
      $cid = $inscricao->cidadao; // garante a variável
        $bRel = $cid?->getRelationValue('bairro'); // pega a RELAÇÃO, não o atributo textual

        $bairroNome = optional($bRel)->nome ?? ($cid->bairro ?? null);
        $cidadeNome = optional($bRel?->cidade)->nome;
        $uf = optional($bRel?->cidade?->estado)->sigla
            ?: optional($bRel?->cidade?->estado)->uf;

      $linha = collect([
        // Rua, número e complemento
        trim(collect([$cid->rua ?? null, $cid->numero ?? null])->filter()->implode(', ')) .
          ($cid->complemento ? (' - '.$cid->complemento) : ''),
        $bairroNome,
        trim(collect([$cidadeNome, $uf])->filter()->implode(' - ')),
        $cid->cep ? 'CEP '.cepMask($cid->cep) : null,
      ])->filter()->implode(', ');
    @endphp

    <p class="text-gray-900 mt-1">{{ $linha !== '' ? $linha : 'Não informado' }}</p>

    {{-- Linha a linha (para conferência) --}}
    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-700">
      <div><span class="font-medium">Logradouro:</span> {{ $cid->rua ?? '—' }}</div>
      <div><span class="font-medium">Número:</span> {{ $cid->numero ?? '—' }}</div>
      <div class="sm:col-span-2"><span class="font-medium">Complemento:</span> {{ $cid->complemento ?? '—' }}</div>
      <div><span class="font-medium">Bairro:</span> {{ $bairroNome ?? '—' }}</div>
      <div><span class="font-medium">Cidade/UF:</span>
        @if($cidadeNome) {{ $cidadeNome }} @else — @endif
        @if($cidadeNome && $uf) - {{ $uf }} @endif
      </div>
      <div class="sm:col-span-2"><span class="font-medium">CEP:</span> {{ $cid->cep ? cepMask($cid->cep) : '—' }}</div>
    </div>
  </div>

  {{-- Ações rápidas --}}
  <div class="flex items-center gap-3">
    @if($inscricao->status !== 'aprovado')
      <form method="POST" action="{{ route('coordenador.programas.aprovar', [$programa->id, $inscricao->id]) }}"
            onsubmit="return confirm('Deseja APROVAR esta inscrição?');">
        @csrf
        <button class="px-4 py-2 rounded bg-green-700 hover:bg-green-800 text-white">Aprovar</button>
      </form>
    @endif

    @if($inscricao->status !== 'reprovado')
      <form method="POST" action="{{ route('coordenador.programas.reprovar', [$programa->id, $inscricao->id]) }}"
            onsubmit="return confirm('Deseja REPROVAR esta inscrição?');">
        @csrf
        <button class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white">Reprovar</button>
      </form>
    @endif
  </div>

</div>
@endsection
