@extends('layouts.app')

@section('title', 'Histórico de Acompanhamentos')

@section('content')
    <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold text-green-700 mb-4">
            📚 Histórico de Acompanhamentos – {{ $cidadao->nome }}
        </h1>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @forelse ($acompanhamentos as $acomp)
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                {{-- Link para relatório --}}
                <a href="{{ route('assistente.acompanhamentos.relatorio', $acomp->id) }}"
                    class="text-sm text-green-700 font-medium hover:underline">
                    📅 {{ $acomp->data->format('d/m/Y') }} — {{ $acomp->assistente->name }}
                </a>

                <a href="{{ route('assistente.acompanhamentos.edit', $acomp->id) }}"
                    class="text-sm text-blue-600 font-medium hover:underline ml-2">
                    ✏️ Editar
                </a>


            </div>

        @empty
            <p class="text-gray-600">Nenhum acompanhamento registrado ainda.</p>
        @endforelse
    </div>
@endsection