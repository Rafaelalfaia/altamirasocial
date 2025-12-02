@extends('layouts.app')

@section('title', 'Detalhes do Temporário')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <h1 class="text-2xl font-bold text-green-800">🧾 Detalhes do Cidadão Temporário</h1>

    <div class="bg-white p-6 shadow rounded space-y-2">
        <p><strong>Nome:</strong> {{ $cidadao->nome }}</p>
        <p><strong>CPF:</strong> {{ $cidadao->cpf }}</p>
        <p><strong>Motivo:</strong> {{ $cidadao->motivo }}</p>
        <p><strong>Validez até:</strong> {{ \Carbon\Carbon::parse($cidadao->fim_validez)->format('d/m/Y') }}</p>
        <p><strong>Cadastrado em:</strong> {{ $cidadao->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Cadastrado por:</strong> {{ $cidadao->user->name ?? 'Desconhecido' }}</p>
        @if($cidadao->user?->restaurantes)
            <p><strong>Restaurante:</strong> {{ $cidadao->user->restaurantes->first()->nome ?? '-' }}</p>
        @endif
    </div>
</div>
@endsection
