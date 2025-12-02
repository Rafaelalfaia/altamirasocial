@extends('layouts.app')

@section('title', 'Editar Cidadão')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-green-700 mb-6">✏️ Editar Cidadão</h1>

    {{-- Erros de validação --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded p-3 mb-4 text-sm">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        // Fallbacks: usa primeiro Cidadao, depois User (para bases antigas)
        $nomeValor  = old('nome', $cidadao->nome ?: ($cidadao->user?->name ?? ''));
        $cpfRaw     = old('cpf',  $cidadao->cpf  ?: ($cidadao->user?->cpf  ?? ''));
        $cpfDigits  = preg_replace('/\D/', '', $cpfRaw ?? ''); // somente números
        $emailValor = old('email', $cidadao->user?->email ?? '');
    @endphp

    <form method="POST"
          action="{{ route('coordenador.cidadaos.update', $cidadao->id) }}"
          class="space-y-4">
        @csrf
        @method('PUT')

        {{-- Nome --}}
        <div>
            <label class="block text-sm font-medium mb-1">Nome</label>
            <input type="text" name="nome" required
                   value="{{ $nomeValor }}"
                   class="w-full border rounded px-3 py-2">
        </div>

       {{-- CPF (mascarado visualmente; apenas dígitos são enviados) --}}
        <div>
        <label class="block text-sm font-medium mb-1">CPF</label>

        {{-- Campo visível (sem name) --}}
        <input type="text" id="cpf_mask"
                inputmode="numeric" maxlength="14" autocomplete="off"
                placeholder="Somente números"
                value="{{ $cpfDigits }}"
                class="w-full border rounded px-3 py-2">

        {{-- Campo real enviado ao backend (apenas dígitos) --}}
        <input type="hidden" name="cpf" id="cpf" value="{{ $cpfDigits }}">

        <p class="text-xs text-gray-500 mt-1">11 dígitos, apenas números.</p>
        </div>


        {{-- E-mail (opcional) --}}
        <div>
            <label class="block text-sm font-medium mb-1">E-mail (opcional)</label>
            <input type="email" name="email"
                   value="{{ $emailValor }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- Senha (opcional) + Confirmar --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">
                    Senha (deixe em branco para manter)
                </label>
                <input type="password" name="password"
                       class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Confirmar Senha</label>
                <input type="password" name="password_confirmation"
                       class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="pt-2">
            <button class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                💾 Salvar Alterações
            </button>
            <a href="{{ route('coordenador.cidadaos.index') }}" class="ml-2 text-gray-600 hover:underline">Voltar</a>
        </div>
    </form>
</div>

{{-- Máscara/Sync de CPF (visual x valor real) --}}
<script>
(function () {
  const mask = document.getElementById('cpf_mask');
  const real = document.getElementById('cpf');
  if (!mask || !real) return;

  function onlyDigits(s){ return (s || '').replace(/\D/g,'').slice(0,11); }
  function formatCPF(v){
    v = onlyDigits(v);
    if (v.length > 9) return v.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
    if (v.length > 6) return v.replace(/(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
    if (v.length > 3) return v.replace(/(\d{3})(\d{0,3})/, '$1.$2');
    return v;
  }
  function sync(){
    const digits = onlyDigits(mask.value);
    real.value = digits;           // <-- enviado ao backend (apenas números)
    mask.value = formatCPF(digits); // <-- exibição amigável
  }
  mask.addEventListener('input', sync);
  // Formata o valor inicial
  sync();
})();
</script>
@endsection
