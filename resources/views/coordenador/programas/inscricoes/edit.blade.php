@extends('layouts.app')

@section('title', 'Editar Inscrição')

@section('content')
@php
    $REGIOES = config('programas.regioes', ['Altamira', 'Castelo dos Sonhos e Cachoeira da Serra']);
    $inscritoNome = optional($inscricao->dependente)->nome ?? optional($inscricao->cidadao)->nome ?? 'Inscrito';
    $responsavel = $inscricao->dependente ? $inscricao->cidadao : null;
@endphp

<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-green-800">
            Editar Inscrição — <span class="text-indigo-700">{{ $inscritoNome }}</span>
        </h1>

        <a href="{{ route('coordenador.programas.inscritos', ['programa' => $programa->id, 'status' => request('status')]) }}"
           class="text-sm text-gray-700 hover:underline">← Voltar</a>
    </div>

    {{-- Feedback de validação --}}
    @if ($errors->any())
        <div class="mb-4 rounded border border-red-200 bg-red-50 text-red-800 p-3 text-sm">
            <div class="font-semibold mb-1">Corrija os erros abaixo:</div>
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Resumo do contexto --}}
    <div class="mb-5 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
        <div class="p-3 rounded bg-gray-50">
            <div class="text-gray-500">Programa</div>
            <div class="font-semibold">{{ $programa->nome }}</div>
        </div>
        <div class="p-3 rounded bg-gray-50">
            <div class="text-gray-500">Status atual</div>
            <div>
                <span class="inline-block px-2 py-1 text-xs rounded-full font-semibold
                    @if($inscricao->status === 'aprovado') bg-green-100 text-green-700
                    @elseif($inscricao->status === 'pendente') bg-yellow-100 text-yellow-700
                    @else bg-red-100 text-red-700 @endif">
                    {{ ucfirst($inscricao->status) }}
                </span>
            </div>
        </div>
        <div class="p-3 rounded bg-gray-50">
            <div class="text-gray-500">Inscrito</div>
            <div class="font-semibold">{{ $inscritoNome }}</div>
        </div>
        <div class="p-3 rounded bg-gray-50">
            <div class="text-gray-500">Região (atual)</div>
            <div class="font-semibold">{{ $inscricao->regiao ?: '—' }}</div>
        </div>

        @if($responsavel)
            <div class="md:col-span-2 p-3 rounded bg-gray-50">
                <div class="text-gray-500">Responsável</div>
                <div class="font-semibold">
                    {{ $responsavel->nome }}
                    @if($responsavel->cpf)
                        <span class="text-gray-400 font-normal"> · CPF: {{ $responsavel->cpf }}</span>
                    @endif
                    @if($responsavel->telefone)
                        <span class="text-gray-400 font-normal"> · Tel: {{ $responsavel->telefone }}</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Formulário de edição --}}
    <form method="POST" action="{{ route('coordenador.programas.inscricoes.update', [$programa->id, $inscricao->id]) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Status</label>
                <select name="status" class="w-full border rounded px-3 py-2">
                    @foreach(['aprovado' => 'Aprovado', 'pendente' => 'Pendente', 'reprovado' => 'Reprovado'] as $k => $rotulo)
                        <option value="{{ $k }}" @selected(old('status', $inscricao->status) === $k)>{{ $rotulo }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium">Região</label>
                <select name="regiao" class="w-full border rounded px-3 py-2">
                    <option value="">—</option>
                    @foreach($REGIOES as $r)
                        <option value="{{ $r }}" @selected(old('regiao', $inscricao->regiao) === $r)>{{ $r }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($programa->aceita_menores)
            <div class="border-t pt-4">
                <h2 class="text-lg font-semibold text-indigo-700 mb-2">Dados do Dependente</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Nome</label>
                        <input type="text" name="dependente[nome]"
                               value="{{ old('dependente.nome', optional($inscricao->dependente)->nome) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">CPF</label>
                        <input type="text" name="dependente[cpf]"
                               value="{{ old('dependente.cpf', optional($inscricao->dependente)->cpf) }}"
                               maxlength="11"
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Parentesco</label>
                        <input type="text" name="dependente[grau_parentesco]"
                               value="{{ old('dependente.grau_parentesco', optional($inscricao->dependente)->grau_parentesco) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                </div>
            </div>
        @endif

        <div class="flex items-center gap-3 pt-2">
            <button class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">Salvar</button>
            <a href="{{ route('coordenador.programas.inscritos', ['programa' => $programa->id, 'status' => request('status')]) }}"
               class="text-gray-700 hover:underline">Cancelar</a>
        </div>
    </form>
</div>
@endsection
