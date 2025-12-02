@extends('layouts.app')

@section('title', 'Ficha Pública do Cidadão')

@section('content')
@php
    use Illuminate\Support\Carbon;

    // CPF mascarado
    $cpfDigits = preg_replace('/\D/', '', (string) $cidadao->cpf);
    $cpfMasked = strlen($cpfDigits) === 11
        ? substr($cpfDigits,0,3).'.'.substr($cpfDigits,3,3).'.'.substr($cpfDigits,6,3).'-'.substr($cpfDigits,9,2)
        : ($cidadao->cpf ?: '—');

    // Datas com fallback
    $fmtDate = function($v) {
        if (!$v) return null;
        if ($v instanceof \Carbon\CarbonInterface) return $v->format('d/m/Y');
        try { return Carbon::parse((string)$v)->format('d/m/Y'); } catch (\Throwable $e) { return null; }
    };

    $nasc    = $fmtDate($cidadao->data_nascimento) ?: '—';
    $emissao = $fmtDate($cidadao->data_emissao_rg) ?: '—';
    $decl    = $fmtDate($cidadao->data_declaracao) ?: '—';

    // Renda
    $rendaFmt = is_numeric($cidadao->renda_total_familiar)
        ? 'R$ '.number_format($cidadao->renda_total_familiar, 2, ',', '.')
        : '—';

    // Tipos de deficiência (array/JSON/CSV)
    $tipos = $cidadao->tipos_deficiencia ?? [];
    if (is_string($tipos)) {
        $dec = json_decode($tipos, true);
        $tipos = is_array($dec) ? $dec : array_filter(array_map('trim', explode(',', $tipos)));
    }
    if (!is_array($tipos)) $tipos = [];
    $tipos = array_values(array_filter($tipos, fn($t) => $t !== ''));

    // Foto
    $fotoUrl = $cidadao?->foto
        ? asset('storage/fotos/'.$cidadao->foto).'?v='.optional($cidadao->updated_at)->timestamp
        : asset('imagens/avatar-padrao.png');

    // Helpers de UI
    $yesNo = fn($b) => ($b ? 'Sim' : 'Não');
    $vv    = fn($v) => ($v !== null && $v !== '' ? $v : '—');
@endphp

<div class="max-w-4xl mx-auto bg-white p-5 md:p-6 rounded-lg shadow">
    {{-- Cabeçalho --}}
    <div class="flex items-center gap-4 mb-6">
        <img src="{{ $fotoUrl }}" alt="Foto do cidadão" class="h-14 w-14 md:h-16 md:w-16 rounded-full object-cover ring-2 ring-gray-100">
        <div class="min-w-0">
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 leading-tight truncate">
                {{ $cidadao->nome ?? '—' }}
            </h1>
            <p class="text-sm text-gray-500">
                CPF: <span class="font-medium text-gray-700">{{ $cpfMasked }}</span>
            </p>
        </div>
    </div>

    <div class="space-y-7">
        {{-- 1. Dados Pessoais --}}
        <section>
            <h2 class="text-base md:text-lg font-semibold text-gray-800 border-b pb-2 mb-3">1. Dados Pessoais</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                <div>
                    <p class="text-[12px] uppercase tracking-wide text-gray-500">Nome</p>
                    <p class="mt-1 text-gray-900 font-medium break-words">{{ $vv($cidadao->nome) }}</p>
                </div>
                <div>
                    <p class="text-[12px] uppercase tracking-wide text-gray-500">CPF</p>
                    <p class="mt-1 text-gray-900 font-medium">{{ $cpfMasked }}</p>
                </div>
                <div>
                    <p class="text-[12px] uppercase tracking-wide text-gray-500">Telefone</p>
                    <p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->telefone) }}</p>
                </div>
                <div>
                    <p class="text-[12px] uppercase tracking-wide text-gray-500">Data de Nascimento</p>
                    <p class="mt-1 text-gray-900 font-medium">{{ $nasc }}</p>
                </div>
                <div>
                    <p class="text-[12px] uppercase tracking-wide text-gray-500">Sexo</p>
                    <p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->sexo) }}</p>
                </div>
                <div>
                    <p class="text-[12px] uppercase tracking-wide text-gray-500">Cor/Raça</p>
                    <p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->cor_raca) }}</p>
                </div>
                <div>
                    <p class="text-[12px] uppercase tracking-wide text-gray-500">NIS</p>
                    <p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->nis) }}</p>
                </div>
                <div>
                    <p class="text-[12px] uppercase tracking-wide text-gray-500">RG</p>
                    <p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->rg) }}</p>
                </div>
                <div>
                    <p class="text-[12px] uppercase tracking-wide text-gray-500">Órgão Emissor</p>
                    <p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->orgao_emissor) }}</p>
                </div>
                <div>
                    <p class="text-[12px] uppercase tracking-wide text-gray-500">Emissão do RG</p>
                    <p class="mt-1 text-gray-900 font-medium">{{ $emissao }}</p>
                </div>
                <div>
                    <p class="text-[12px] uppercase tracking-wide text-gray-500">Título de Eleitor</p>
                    <p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->titulo_eleitor) }}</p>
                </div>
                <div>
                    <p class="text-[12px] uppercase tracking-wide text-gray-500">Zona / Seção</p>
                    <p class="mt-1 text-gray-900 font-medium">
                        {{ $vv($cidadao->zona) }}{{ $cidadao->secao ? ' / '.$cidadao->secao : '' }}
                    </p>
                </div>
                <div class="lg:col-span-3">
                    <p class="text-[12px] uppercase tracking-wide text-gray-500">CadÚnico</p>
                    <p class="mt-1 text-gray-900 font-medium break-words">{{ $vv($cidadao->codigo_cadunico) }}</p>
                </div>
            </div>
        </section>

        {{-- 2. Moradia --}}
        <section>
            <h2 class="text-base md:text-lg font-semibold text-gray-800 border-b pb-2 mb-3">2. Moradia</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">CEP</p><p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->cep) }}</p></div>
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Rua</p><p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->rua) }}</p></div>
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Número</p><p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->numero) }}</p></div>
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Complemento</p><p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->complemento) }}</p></div>
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Bairro</p><p class="mt-1 text-gray-900 font-medium">{{ $vv(optional($cidadao->bairro)->nome) }}</p></div>
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Cidade</p><p class="mt-1 text-gray-900 font-medium">{{ $vv(optional(optional($cidadao->bairro)->cidade)->nome) }}</p></div>
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Estado</p><p class="mt-1 text-gray-900 font-medium">{{ $vv(optional(optional(optional($cidadao->bairro)->cidade)->estado)->nome) }}</p></div>
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Tipo de Moradia</p><p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->tipo_moradia) }}</p></div>
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Possui Animais</p><p class="mt-1 text-gray-900 font-medium">{{ $yesNo($cidadao->possui_animais) }}</p></div>
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Quantidade de Animais</p><p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->numero_animais) }}</p></div>
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Água Encanada</p><p class="mt-1 text-gray-900 font-medium">{{ $yesNo($cidadao->tem_agua_encanada) }}</p></div>
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Esgoto</p><p class="mt-1 text-gray-900 font-medium">{{ $yesNo($cidadao->tem_esgoto) }}</p></div>
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Coleta de Lixo</p><p class="mt-1 text-gray-900 font-medium">{{ $yesNo($cidadao->tem_coleta_lixo) }}</p></div>
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Energia Elétrica</p><p class="mt-1 text-gray-900 font-medium">{{ $yesNo($cidadao->tem_energia) }}</p></div>
            </div>
        </section>

        {{-- 3. Trabalho e Renda --}}
        <section>
            <h2 class="text-base md:text-lg font-semibold text-gray-800 border-b pb-2 mb-3">3. Trabalho e Renda</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Ocupação</p><p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->ocupacao) }}</p></div>
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Renda Familiar Total</p><p class="mt-1 text-gray-900 font-medium">{{ $rendaFmt }}</p></div>
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Pessoas na Residência</p><p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->pessoas_na_residencia) }}</p></div>
                <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Grau de Parentesco</p><p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->grau_parentesco) }}</p></div>
                <div class="lg:col-span-2"><p class="text-[12px] uppercase tracking-wide text-gray-500">Escolaridade</p><p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->escolaridade) }}</p></div>
            </div>
        </section>

        {{-- 4. Acessibilidade e Observações --}}
        <section>
            <h2 class="text-base md:text-lg font-semibold text-gray-800 border-b pb-2 mb-3">4. Acessibilidade e Observações</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-[12px] uppercase tracking-wide text-gray-500">Possui Deficiência</p>
                    <p class="mt-1 text-gray-900 font-medium">{{ $yesNo($cidadao->pcd) }}</p>
                </div>

                {{-- Tipos como chips --}}
                <div>
                    <p class="text-[12px] uppercase tracking-wide text-gray-500">Tipos de Deficiência</p>
                    @if(count($tipos))
                        <div class="mt-1.5 flex flex-wrap gap-1.5">
                            @foreach($tipos as $t)
                                <span class="px-2 py-1 rounded-full text-xs bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $t }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-1 text-gray-900 font-medium">—</p>
                    @endif
                </div>

                <div class="md:col-span-2">
                    <p class="text-[12px] uppercase tracking-wide text-gray-500">Observações</p>
                    <p class="mt-1 text-gray-900 leading-relaxed">
                        {{ $cidadao->observacoes ?: '—' }}
                    </p>
                </div>

                {{-- Se quiser tornar público: --}}
                {{-- <div><p class="text-[12px] uppercase tracking-wide text-gray-500">Data da Declaração</p><p class="mt-1 text-gray-900 font-medium">{{ $decl }}</p></div> --}}
                {{-- <div><p class="text-[12px] uppercase tracking-wide text-gray-500">CID</p><p class="mt-1 text-gray-900 font-medium">{{ $vv($cidadao->cid) }}</p></div> --}}
            </div>
        </section>
    </div>
</div>
@endsection
