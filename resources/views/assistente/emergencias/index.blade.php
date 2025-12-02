@extends('layouts.app')

@section('title', 'Emergências em Andamento')

@section('content')
<div class="max-w-7xl mx-auto p-6 bg-white rounded-2xl shadow-xl mt-6">
    <h1 class="text-2xl font-bold text-red-700 mb-6 flex items-center gap-2">
        🚨 Emergências em Andamento
    </h1>

    @if ($emergencias->isEmpty())
        <p class="text-gray-600">Nenhuma emergência ativa no momento.</p>
    @else
        <div class="space-y-6">
            @foreach ($emergencias as $emergencia)
                <div class="bg-gray-100 p-4 rounded-xl shadow border">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">
                                🧑 {{ $emergencia->cidadao->nome ?? 'Cidadão Desconhecido' }}
                            </h2>
                            <p class="text-sm text-gray-600"><strong>Motivo:</strong> {{ $emergencia->motivo }}</p>
                            <p class="text-sm text-gray-600"><strong>Descrição:</strong> {{ $emergencia->descricao ?? 'Não informada' }}</p>
                            <p class="text-xs text-gray-500 mt-1">Solicitada em {{ $emergencia->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div class="mt-4 md:mt-0 flex flex-col gap-2">
                            <a href="{{ route('assistente.emergencias.video', $emergencia->sala) }}"
                                class="px-4 py-2 bg-green-600 text-white rounded shadow hover:bg-green-700 transition">
                                🎥 Entrar na Sala
                            </a>

                            <form action="{{ route('assistente.emergencias.finalizar', $emergencia->id) }}" method="POST"
                                onsubmit="return confirm('Tem certeza que deseja finalizar este atendimento?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="px-4 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-600 transition">
                                    ✅ Finalizar Atendimento
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
