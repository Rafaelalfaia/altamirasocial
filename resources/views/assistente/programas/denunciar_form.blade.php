@extends('layouts.app')

@section('title', 'Denunciar Cidadão')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">
        <h1 class="text-2xl font-bold text-red-700 mb-6">
            ⚠️ Denunciar – {{ $cidadao->nome }}
        </h1>

        <p class="mb-4 text-gray-700">
            Você está denunciando um possível uso indevido do benefício no programa:
            <strong class="text-indigo-700">{{ $programa->nome }}</strong>.
        </p>

        <form action="{{ route('assistente.programas.denunciar.store', [$programa->id, $cidadao->id]) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="motivo" class="block text-sm font-medium text-gray-700 mb-1">
                    Motivo da Denúncia
                </label>
                <textarea name="motivo" id="motivo" rows="5"
                    class="w-full border px-4 py-2 rounded shadow-sm focus:ring focus:border-red-400"
                    placeholder="Descreva o motivo da denúncia..." required></textarea>
            </div>

            <div class="text-right">
                <button type="submit"
                    class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700 transition">
                    📩 Enviar Denúncia
                </button>
            </div>
        </form>
    </div>
@endsection
