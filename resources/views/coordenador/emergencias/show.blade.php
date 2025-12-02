@extends('layouts.app')
@section('title', 'Detalhes da Ocorrência')
@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-2xl shadow-xl mt-6 space-y-6">

    <h1 class="text-2xl font-bold text-red-700 flex items-center gap-2">
        📋 Detalhes da Ocorrência
    </h1>

    {{-- Seção do Assistente --}}
    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow-sm">
        <h2 class="text-base font-semibold text-red-800 mb-2">👨‍⚕️ Atendimento</h2>
        <p><strong>Atendido por:</strong> {{ $emergencia->user->name ?? 'Não informado' }}</p>
        <p><strong>Data e Hora:</strong> {{ $emergencia->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Motivo da Ocorrência:</strong> {{ $emergencia->motivo }}</p>
        <p><strong>Descrição da Situação:</strong> {{ $emergencia->descricao ?? 'Não informado' }}</p>
        <p><strong>Conclusão / Providência:</strong> {{ $emergencia->conclusao ?? 'Não informado' }}</p>
    </div>

    {{-- Seção do Cidadão --}}
    <div class="bg-gray-50 border border-gray-200 p-4 rounded-lg shadow-sm">
        <h2 class="text-base font-semibold text-gray-700 mb-2">🙍‍♂️ Dados do Cidadão</h2>
        <p><strong>Nome:</strong> {{ $emergencia->cidadao->nome ?? 'Desconhecido' }}</p>
        <p><strong>CPF:</strong> {{ $emergencia->cidadao->cpf ?? 'Não informado' }}</p>
        <p><strong>Telefone:</strong> {{ $emergencia->cidadao->telefone ?? 'Não informado' }}</p>
        <p><strong>Endereço:</strong> {{ $emergencia->cidadao->endereco_completo ?? 'Não informado' }}</p>
    </div>

    {{-- Botão de Voltar --}}
    <div class="pt-4">
        <a href="{{ route('coordenador.emergencias.index') }}"
            class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded shadow text-sm">
            🔙 Voltar para Lista
        </a>
    </div>

</div>
@endsection
