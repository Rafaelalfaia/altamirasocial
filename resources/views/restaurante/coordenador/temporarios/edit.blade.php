@extends('layouts.app')

@section('title', 'Editar Cidadão Temporário')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded shadow p-6 space-y-6">

    <h1 class="text-2xl font-bold text-blue-800">✏️ Editar Cidadão Temporário</h1>

    {{-- Mensagens de erro --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulário --}}
    <form action="{{ route('restaurante.coordenador.temporarios.update', $cidadao->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Nome --}}
        <div class="mb-4">
            <label for="nome" class="block text-sm font-medium text-gray-700">Nome completo <span class="text-red-600">*</span></label>
            <input type="text" name="nome" id="nome" value="{{ old('nome', $cidadao->nome) }}" required
                class="mt-1 block w-full border-gray-300 rounded shadow-sm text-sm">
        </div>

        {{-- Motivo --}}
        <div class="mb-4">
            <label for="motivo" class="block text-sm font-medium text-gray-700">Motivo do cadastro</label>
            <input type="text" name="motivo" id="motivo" value="{{ old('motivo', $cidadao->motivo) }}"
                class="mt-1 block w-full border-gray-300 rounded shadow-sm text-sm">
        </div>

        {{-- Botões --}}
        <div class="flex justify-between mt-6">
            <a href="{{ route('restaurante.coordenador.temporarios.index') }}"
               class="text-sm text-gray-600 hover:underline">← Voltar</a>

            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded text-sm">
                Atualizar
            </button>
        </div>
    </form>

</div>
@endsection
