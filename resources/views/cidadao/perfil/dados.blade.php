@extends('layouts.app')

@section('title', 'Dados Pessoais')

@section('content')
@php
  use Illuminate\Support\Carbon;

  $fotoUrl = $cidadao?->foto
      ? asset('storage/fotos/'.$cidadao->foto).'?v='.optional($cidadao->updated_at)->timestamp
      : asset('imagens/avatar-padrao.png');

  // Datas
  $nascimento = old('data_nascimento');
  if ($nascimento === null) {
      $raw = $cidadao?->data_nascimento;
      $nascimento = $raw
        ? ($raw instanceof \Carbon\CarbonInterface
              ? $raw->format('Y-m-d')
              : \Illuminate\Support\Carbon::parse($raw)->format('Y-m-d'))
        : '';
  }
  $emissao = old('data_emissao_rg');
  if ($emissao === null) {
      $raw = $cidadao?->data_emissao_rg;
      $emissao = $raw
        ? ($raw instanceof \Carbon\CarbonInterface
              ? $raw->format('Y-m-d')
              : \Illuminate\Support\Carbon::parse($raw)->format('Y-m-d'))
        : '';
  }

  // CPF (visual com máscara)
  $cpfDigits = preg_replace('/\D/','', old('cpf', $cidadao->cpf));
  $cpfMasked = $cpfDigits && strlen($cpfDigits)===11
      ? substr($cpfDigits,0,3).'.'.substr($cpfDigits,3,3).'.'.substr($cpfDigits,6,3).'-'.substr($cpfDigits,9,2)
      : old('cpf', $cidadao->cpf);

  // Chips padrão
  $sexoOpts  = ['Masculino','Feminino','Outro'];
  $civilOpts = ['Solteiro(a)','Casado(a)','União estável','Divorciado(a)','Separado(a)','Viúvo(a)','Outro'];
  $racaOpts  = ['Branca','Preta','Parda','Amarela','Indígena','Não informada','Outro'];

  $sexoSel  = old('sexo', $cidadao->sexo);
  $civilSel = old('situacao_civil', $cidadao->situacao_civil);
  $racaSel  = old('cor_raca', $cidadao->cor_raca);

  $sexoKnown  = in_array((string)$sexoSel, $sexoOpts, true);
  $civilKnown = in_array((string)$civilSel, $civilOpts, true);
  $racaKnown  = in_array((string)$racaSel, $racaOpts, true);

  $sexoOutro  = $sexoKnown  ? '' : (string)$sexoSel;
  $civilOutro = $civilKnown ? '' : (string)$civilSel;
  $racaOutro  = $racaKnown  ? '' : (string)$racaSel;
@endphp

<div class="max-w-5xl mx-auto">
  @includeIf('cidadao.perfil.partials._steps', ['atual' => 'dados'])

  @includeIf('cidadao.perfil.partials._alerts')
  @includeIf('cidadao.perfil.partials._errors')

  <form
    action="{{ route('cidadao.perfil.dados.salvar') }}"
    method="POST"
    enctype="multipart/form-data"
    x-data="dadosForm({
      cpfInicial: '{{ $cpfMasked }}',
      fotoInicial: '{{ $fotoUrl }}',

      sexoInicial: '{{ $sexoKnown ? e($sexoSel) : 'Outro' }}',
      sexoOutroInicial: @js($sexoOutro),

      civilInicial: '{{ $civilKnown ? e($civilSel) : 'Outro' }}',
      civilOutroInicial: @js($civilOutro),

      racaInicial: '{{ $racaKnown ? e($racaSel) : 'Outro' }}',
      racaOutroInicial: @js($racaOutro),
    })"
    x-init="init()"
    class="rounded-2xl bg-white/90 backdrop-blur p-4 sm:p-6 shadow-sm ring-1 ring-gray-100 space-y-6"
  >
    @csrf

    {{-- Header com foto/gradiente (padrão assistente) --}}
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-indigo-600 to-sky-600 p-4 sm:p-5">
      <div class="flex items-center gap-4">
        <div class="relative shrink-0">
          <img :src="fotoPreview" alt="Foto"
               class="h-16 w-16 sm:h-20 sm:w-20 rounded-full object-cover ring-2 ring-white shadow-md">
          <label class="absolute -bottom-1 -right-1 inline-flex items-center justify-center h-8 w-8 rounded-full bg-white/90 text-indigo-700 shadow cursor-pointer hover:bg-white"
                 title="Trocar foto">
            <input type="file" class="hidden" name="foto" accept="image/png,image/jpeg,image/jpg,image/webp" @change="onFotoChange">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5.5a1 1 0 0 1 .894.553l.724 1.447h2.382a1 1 0 0 1 .894.553L18 10h1a1 1 0 1 1 0 2h-1.126l-.93 7.441A2 2 0 0 1 14.962 21H9.038a2 2 0 0 1-1.982-1.559L6.126 12H5a1 1 0 1 1 0-2h1l1.106-1.947A1 1 0 0 1 8 7.5h2.382l.724-1.447A1 1 0 0 1 12 5.5Z"/></svg>
          </label>
        </div>
        <div class="min-w-0">
          <div class="flex items-center gap-2">
            <h1 class="text-white font-semibold text-lg sm:text-xl truncate">{{ $cidadao->nome ?? 'Seu nome' }}</h1>
          </div>
          <p class="text-white/80 text-xs mt-0.5">Mantenha seus dados atualizados</p>
        </div>
      </div>
    </div>

    {{-- Identificação --}}
    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-800">Identificação</h2>
        <span class="text-xs text-gray-400">Campos principais</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="sm:col-span-3">
          <label class="text-xs font-medium text-gray-700">Nome Completo <span class="text-red-500">*</span></label>
          <input type="text" name="nome" value="{{ old('nome', $cidadao->nome ?? '') }}"
                 class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400" required>
        </div>

        <div>
          <label class="text-xs font-medium text-gray-700">Apelido</label>
          <input type="text" name="apelido" value="{{ old('apelido', $cidadao->apelido ?? '') }}"
                 class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>

        <div>
          <label class="text-xs font-medium text-gray-700">Naturalidade</label>
          <input type="text" name="naturalidade" value="{{ old('naturalidade', $cidadao->naturalidade ?? '') }}"
                 class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>

        <div>
          <label class="text-xs font-medium text-gray-700">Data de Nascimento</label>
          <input type="date" name="data_nascimento" value="{{ $nascimento }}"
                 class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>
      </div>
    </div>

    <hr class="border-gray-100">

    {{-- Contatos --}}
    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-800">Contatos</h2>
        <span class="text-xs text-gray-400">Telefone e e-mail</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
          <label class="text-xs font-medium text-gray-700">Telefone</label>
          <input type="text" name="telefone" value="{{ old('telefone', $cidadao->telefone ?? '') }}"
                 class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>

        <div>
          <label class="text-xs font-medium text-gray-700">WhatsApp</label>
          <input type="text" name="whatsapp" value="{{ old('whatsapp', $cidadao->whatsapp ?? '') }}"
                 class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>

        <div>
          <label class="text-xs font-medium text-gray-700">E-mail</label>
          <input type="email" name="email" value="{{ old('email', $cidadao->email ?? '') }}"
                 class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>
      </div>
    </div>

    <hr class="border-gray-100">

    {{-- Documentos (CPF com máscara) --}}
    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-800">Documentos</h2>
        <span class="text-xs text-gray-400">CPF, RG e afins</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
          <label class="text-xs font-medium text-gray-700">CPF</label>
          <input type="text" name="cpf" inputmode="numeric" maxlength="14" autocomplete="off"
                 x-model="cpf" @input="formatCpf()" @blur="validateCpf()" @paste.prevent="onCpfPaste($event)"
                 :class="['w-full border rounded-lg px-3 py-2 focus:ring-2',
                          cpfValido===false ? 'border-red-500 focus:ring-red-200'
                                            : 'border-gray-300 focus:ring-indigo-200']"
                 placeholder="000.000.000-00">
          <p x-show="cpfValido===false" x-cloak class="text-xs text-red-600 mt-1">CPF inválido.</p>
        </div>

        <div>
          <label class="text-xs font-medium text-gray-700">RG</label>
          <input type="text" name="rg" value="{{ old('rg', $cidadao->rg ?? '') }}"
                 class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>

        <div>
          <label class="text-xs font-medium text-gray-700">Órgão Emissor</label>
          <input type="text" name="orgao_emissor" value="{{ old('orgao_emissor', $cidadao->orgao_emissor ?? '') }}"
                 class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>

        <div>
          <label class="text-xs font-medium text-gray-700">Data Emissão RG</label>
          <input type="date" name="data_emissao_rg" value="{{ $emissao }}"
                 class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>

        <div>
          <label class="text-xs font-medium text-gray-700">NIS</label>
          <input type="text" name="nis" value="{{ old('nis', $cidadao->nis ?? '') }}"
                 class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>
      </div>
    </div>

    <hr class="border-gray-100">

    {{-- Chips no padrão do cidadão (Sexo / Situação Civil / Cor/Raça) --}}
    <div class="space-y-6">
      {{-- Sexo --}}
      <div class="space-y-2">
        <label class="text-xs font-medium text-gray-700">Sexo</label>
        <input type="hidden" name="sexo" :value="sexoChoice==='Outro' ? (sexoOutro||'Outro') : sexoChoice">
        <div class="flex flex-wrap gap-2">
          @foreach ($sexoOpts as $opt)
            <button type="button"
                    @click="sexoChoice='{{ $opt }}'"
                    :class="sexoChoice==='{{ $opt }}' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-indigo-700 border-indigo-200 hover:bg-indigo-50'"
                    class="px-3 py-1.5 rounded-full border text-sm">
              {{ $opt }}
            </button>
          @endforeach
        </div>
        <div x-show="sexoChoice==='Outro'" x-cloak>
          <input type="text" x-model="sexoOutro" placeholder="Descreva"
                 class="mt-2 w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>
      </div>

      {{-- Situação Civil --}}
      <div class="space-y-2">
        <label class="text-xs font-medium text-gray-700">Situação Civil</label>
        <input type="hidden" name="situacao_civil" :value="civilChoice==='Outro' ? (civilOutro||'Outro') : civilChoice">
        <div class="flex flex-wrap gap-2">
          @foreach ($civilOpts as $opt)
            <button type="button"
                    @click="civilChoice='{{ $opt }}'"
                    :class="civilChoice==='{{ $opt }}' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-indigo-700 border-indigo-200 hover:bg-indigo-50'"
                    class="px-3 py-1.5 rounded-full border text-sm">
              {{ $opt }}
            </button>
          @endforeach
        </div>
        <div x-show="civilChoice==='Outro'" x-cloak>
          <input type="text" x-model="civilOutro" placeholder="Descreva"
                 class="mt-2 w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>
      </div>

      {{-- Cor/Raça --}}
      <div class="space-y-2">
        <label class="text-xs font-medium text-gray-700">Cor/Raça</label>
        <input type="hidden" name="cor_raca" :value="racaChoice==='Outro' ? (racaOutro||'Outro') : racaChoice">
        <div class="flex flex-wrap gap-2">
          @foreach ($racaOpts as $opt)
            <button type="button"
                    @click="racaChoice='{{ $opt }}'"
                    :class="racaChoice==='{{ $opt }}' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-indigo-700 border-indigo-200 hover:bg-indigo-50'"
                    class="px-3 py-1.5 rounded-full border text-sm">
              {{ $opt }}
            </button>
          @endforeach
        </div>
        <div x-show="racaChoice==='Outro'" x-cloak>
          <input type="text" x-model="racaOutro" placeholder="Descreva"
                 class="mt-2 w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>
      </div>
    </div>

    <hr class="border-gray-100">

    {{-- Complementares --}}
    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-800">Dados complementares</h2>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
        <div>
          <label class="text-xs font-medium text-gray-700">Título de Eleitor</label>
          <input type="text" name="titulo_eleitor" value="{{ old('titulo_eleitor', $cidadao->titulo_eleitor ?? '') }}"
                 class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>
        <div>
          <label class="text-xs font-medium text-gray-700">Zona</label>
          <input type="text" name="zona" value="{{ old('zona', $cidadao->zona ?? '') }}"
                 class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>
        <div>
          <label class="text-xs font-medium text-gray-700">Seção</label>
          <input type="text" name="secao" value="{{ old('secao', $cidadao->secao ?? '') }}"
                 class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>
        <div>
          <label class="text-xs font-medium text-gray-700">Unidade Cadastradora</label>
          <input type="text" name="unidade_cadastradora" value="{{ old('unidade_cadastradora', $cidadao->unidade_cadastradora ?? '') }}"
                 class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="text-xs font-medium text-gray-700">Responsável Familiar</label>
          <input type="text" name="responsavel_familiar" value="{{ old('responsavel_familiar', $cidadao->responsavel_familiar ?? '') }}"
                 class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>
        <div>
          <label class="text-xs font-medium text-gray-700">Código do CadÚnico</label>
          <input type="text" name="codigo_cadunico" value="{{ old('codigo_cadunico', $cidadao->codigo_cadunico ?? '') }}"
                 class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-200">
        </div>
      </div>
    </div>

    {{-- Barra de ações (fixa no mobile) --}}
    <div class="sticky bottom-0 left-0 right-0 -mx-4 sm:mx-0 bg-white/90 backdrop-blur border-t border-gray-100 px-4 sm:px-0 py-3">
      <div class="flex items-center justify-between">
        <a href="{{ route('cidadao.perfil.moradia') }}"
           class="px-4 py-2 rounded-full border text-sm text-gray-700 hover:bg-gray-50">Pular</a>
        <button type="submit" :disabled="cpfInvalido()"
                class="px-5 py-2 rounded-full bg-indigo-600 text-white text-sm hover:bg-indigo-700 disabled:opacity-50">
          Salvar e continuar
        </button>
      </div>
    </div>
  </form>
</div>

{{-- Alpine helpers (CPF, Foto, Chips) --}}
<script>
function dadosForm(payload){
  const onlyDigits = s => (s||'').replace(/\D/g,'');
  const maskCpf = d => {
    d = d.slice(0,11);
    if (d.length <= 3) return d;
    if (d.length <= 6) return d.slice(0,3)+'.'+d.slice(3);
    if (d.length <= 9) return d.slice(0,3)+'.'+d.slice(3,6)+'.'+d.slice(6);
    return d.slice(0,3)+'.'+d.slice(3,6)+'.'+d.slice(6,9)+'-'+d.slice(9,11);
  };
  const isInvalidSequence = d => /^(\d)\1{10}$/.test(d);
  const cpfChecksum = d => {
    if (d.length !== 11 || isInvalidSequence(d)) return false;
    for (let t=9; t<11; t++){
      let soma = 0;
      for (let i=0; i<t; i++) soma += parseInt(d[i],10) * ((t+1)-i);
      let dig = (soma * 10) % 11;
      if (dig === 10) dig = 0;
      if (parseInt(d[t],10) !== dig) return false;
    }
    return true;
  };

  return {
    // cpf & foto
    cpf: maskCpf(onlyDigits(payload.cpfInicial||'')),
    cpfValido: null,
    fotoPreview: payload.fotoInicial || '',

    // chips
    sexoChoice:  payload.sexoInicial  || 'Outro',
    sexoOutro:   payload.sexoOutroInicial  || '',
    civilChoice: payload.civilInicial || 'Outro',
    civilOutro:  payload.civilOutroInicial || '',
    racaChoice:  payload.racaInicial  || 'Outro',
    racaOutro:   payload.racaOutroInicial  || '',

    init(){ this.validateCpf(); },
    // cpf
    formatCpf(){ this.cpf = maskCpf(onlyDigits(this.cpf)); this.validateCpf(); },
    onCpfPaste(e){ const t=(e.clipboardData||window.clipboardData).getData('text'); this.cpf=maskCpf(onlyDigits(t)); this.validateCpf(); },
    validateCpf(){ const d=onlyDigits(this.cpf); this.cpfValido = d.length===11 && cpfChecksum(d); },
    cpfInvalido(){ return this.cpfValido === false; },
    // foto
    onFotoChange(e){
      const f = e.target.files[0];
      if (f) {
        const r = new FileReader();
        r.onload = ev => { this.fotoPreview = ev.target.result; };
        r.readAsDataURL(f);
      }
    }
  }
}
</script>
@endsection
