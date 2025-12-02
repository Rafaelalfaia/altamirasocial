@extends('layouts.app')

@section('title', 'Detalhes da Inscrição')

@section('content')
@php
    $inscrito     = $inscricao->dependente ?: $inscricao->cidadao;
    $inscritoNome = optional($inscricao->dependente)->nome ?? optional($inscricao->cidadao)->nome ?? 'Inscrito';
    $responsavel  = $inscricao->dependente ? $inscricao->cidadao : null;

    // Endereço sempre vem do CIDADÃO (domicílio do responsável/titular)
    $cid  = $inscricao->cidadao;
    // usa a RELAÇÃO para evitar conflito com possível coluna textual "bairro"
    $bRel = $cid?->getRelationValue('bairro');
    $bairroNome = optional($bRel)->nome ?? ($cid->bairro ?? null);
    $cidadeNome = optional($bRel?->cidade)->nome;
    $uf         = optional($bRel?->cidade?->estado)->sigla ?: optional($bRel?->cidade?->estado)->uf;

    // Linha única via accessor; se não houver, monta manualmente
    $linhaEndereco = $cid?->endereco_linha ?? collect([
        trim(collect([$cid->rua ?? null, $cid->numero ?? null])->filter()->implode(', '))
            . ($cid?->complemento ? ' - '.$cid->complemento : ''),
        $bairroNome,
        trim(collect([$cidadeNome, $uf])->filter()->implode(' - ')),
        $cid?->cep ? (function($v){ $d=preg_replace('/\D+/','',$v); return strlen($d)===8?('CEP '.substr($d,0,5).'-'.substr($d,5)):$v; })($cid->cep) : null,
    ])->filter()->implode(', ');
@endphp

<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <div class="flex items-start justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-green-800">Inscrição — {{ $inscritoNome }}</h1>
            <div class="text-sm text-gray-600">
                Programa: <span class="font-medium">{{ $programa->nome }}</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('coordenador.programas.inscritos', ['programa' => $programa->id, 'status' => request('status')]) }}"
               class="text-sm text-gray-700 hover:underline">← Voltar</a>

            <a href="{{ route('coordenador.programas.inscricoes.edit', [$programa->id, $inscricao->id]) }}"
               class="text-sm px-3 py-1 rounded bg-indigo-600 text-white hover:bg-indigo-700">Editar</a>

            <form method="POST"
                  action="{{ route('coordenador.programas.inscricoes.destroy', [$programa->id, $inscricao->id]) }}"
                  onsubmit="return confirm('Deseja EXCLUIR esta inscrição? Esta ação não pode ser desfeita.');">
                @csrf
                @method('DELETE')
                <button class="text-sm px-3 py-1 rounded bg-red-600 text-white hover:bg-red-700">Excluir</button>
            </form>
        </div>
    </div>

    {{-- Linha 1: Status e Região --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-4 bg-gray-50 rounded">
            <div class="text-gray-500 text-sm">Status</div>
            <div class="mt-1">
                <span class="inline-block px-2 py-1 text-xs rounded-full font-semibold
                    @if($inscricao->status === 'aprovado') bg-green-100 text-green-700
                    @elseif($inscricao->status === 'pendente') bg-yellow-100 text-yellow-700
                    @else bg-red-100 text-red-700 @endif">
                    {{ ucfirst($inscricao->status) }}
                </span>
            </div>
        </div>
        <div class="p-4 bg-gray-50 rounded">
            <div class="text-gray-500 text-sm">Região</div>
            <div class="mt-1 font-medium">{{ $inscricao->regiao ?: '—' }}</div>
        </div>
        <div class="p-4 bg-gray-50 rounded">
            <div class="text-gray-500 text-sm">Registrado em</div>
            <div class="mt-1 font-medium">
                {{ optional($inscricao->created_at)->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    {{-- Linha 2: Inscrito e Responsável --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="p-4 bg-gray-50 rounded">
            <div class="text-gray-500 text-sm">Inscrito</div>
            <div class="mt-1 font-semibold text-indigo-700">{{ $inscritoNome }}</div>

            <div class="mt-2 text-sm text-gray-700 space-y-1">
                @if($inscricao->dependente)
                    {{-- Dados do dependente --}}
                    @if($inscricao->dependente->cpf)
                        <div>CPF: <span class="font-mono">{{ $inscricao->dependente->cpf }}</span></div>
                    @endif
                    @if($inscricao->dependente->grau_parentesco)
                        <div>Parentesco: {{ $inscricao->dependente->grau_parentesco }}</div>
                    @endif
                @else
                    {{-- Dados do cidadão-inscrito --}}
                    @if(optional($inscricao->cidadao)->cpf_formatado ?? optional($inscricao->cidadao)->cpf)
                        <div>CPF: <span class="font-mono">{{ $inscricao->cidadao->cpf_formatado ?? $inscricao->cidadao->cpf }}</span></div>
                    @endif
                    @if(optional($inscricao->cidadao)->telefone)
                        <div>Telefone: {{ $inscricao->cidadao->telefone }}</div>
                    @endif
                    @if(optional($inscricao->cidadao)->email)
                        <div>E-mail: {{ $inscricao->cidadao->email }}</div>
                    @endif
                @endif
            </div>
        </div>

        <div class="p-4 bg-gray-50 rounded">
            <div class="text-gray-500 text-sm">Responsável</div>
            <div class="mt-1">
                @if($responsavel)
                    <div class="font-semibold">{{ $responsavel->nome }}</div>
                    <div class="text-sm text-gray-700 space-y-1 mt-1">
                        @if($responsavel->cpf_formatado ?? $responsavel->cpf)
                            <div>CPF: <span class="font-mono">{{ $responsavel->cpf_formatado ?? $responsavel->cpf }}</span></div>
                        @endif
                        @if($responsavel->telefone)
                            <div>Telefone: {{ $responsavel->telefone }}</div>
                        @endif
                        @if($responsavel->email)
                            <div>E-mail: {{ $responsavel->email }}</div>
                        @endif
                    </div>
                @else
                    <div class="text-gray-400">—</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Endereço (sempre mostra; do domicílio do cidadão/responsável) --}}
    <div class="p-4 bg-gray-50 rounded mb-6">
        <div class="text-gray-500 text-sm">
            Endereço {{ $inscricao->dependente ? '(domicílio do responsável)' : '' }}
        </div>

        <div class="mt-1 font-medium">
            {{ $linhaEndereco !== '' ? $linhaEndereco : 'Não informado' }}
        </div>

        {{-- Detalhamento linha a linha (útil p/ conferência) --}}
        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-700">
            <div><span class="font-medium">Logradouro:</span> {{ $cid->rua ?? '—' }}</div>
            <div><span class="font-medium">Número:</span> {{ $cid->numero ?? '—' }}</div>
            <div class="sm:col-span-2"><span class="font-medium">Complemento:</span> {{ $cid->complemento ?? '—' }}</div>
            <div><span class="font-medium">Bairro:</span> {{ $bairroNome ?? '—' }}</div>
            <div><span class="font-medium">Cidade/UF:</span>
                {{ $cidadeNome ?: '—' }}@if($cidadeNome && $uf) - {{ $uf }} @endif
            </div>
            <div class="sm:col-span-2">
                <span class="font-medium">CEP:</span>
                @php
                    $cep = $cid?->cep;
                    $cepFmt = $cep ? preg_replace('/\D+/','',$cep) : '';
                    $cepFmt = (strlen($cepFmt)===8) ? (substr($cepFmt,0,5).'-'.substr($cepFmt,5)) : ($cep ?: null);
                @endphp
                {{ $cepFmt ?? '—' }}
            </div>
        </div>
    </div>

    {{-- Ações rápidas de status --}}
    <div class="flex flex-wrap items-center gap-3 border-t pt-4">
        @if($inscricao->status !== 'aprovado')
            <form method="POST" action="{{ route('coordenador.programas.aprovar', [$programa->id, $inscricao->id]) }}"
                  onsubmit="return confirm('Deseja APROVAR este inscrito?');">
                @csrf
                <button class="px-3 py-1 rounded bg-green-700 text-white text-sm hover:bg-green-800">Aprovar</button>
            </form>
        @endif

        @if($inscricao->status !== 'pendente')
            <form method="POST" action="{{ route('coordenador.programas.atualizar-inscricao', [$programa->id, $inscricao->id]) }}"
                  onsubmit="return confirm('Deseja marcar como PENDENTE?');">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="pendente">
                <button class="px-3 py-1 rounded bg-yellow-500 text-white text-sm hover:bg-yellow-600">Pendente</button>
            </form>
        @endif

        @if($inscricao->status !== 'reprovado')
            <form method="POST" action="{{ route('coordenador.programas.reprovar', [$programa->id, $inscricao->id]) }}"
                  onsubmit="return confirm('Deseja REPROVAR este inscrito?');">
                @csrf
                <button class="px-3 py-1 rounded bg-red-600 text-white text-sm hover:bg-red-700">Reprovar</button>
            </form>
        @endif
    </div>
</div>
@endsection
