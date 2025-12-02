{{-- resources/views/coordenador/orgaos/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Editar Órgão Público')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-green-700 mb-6">✏️ Editar Órgão Público</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('coordenador.orgaos.update', $orgao) }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="nome" class="block text-gray-700 font-medium">Nome do Órgão</label>
            <input type="text" name="nome" id="nome" value="{{ old('nome', $orgao->nome) }}"
                   class="w-full mt-1 px-4 py-2 border rounded focus:outline-none focus:ring focus:border-green-500" required>
        </div>

        
        <div class="flex justify-between">
            <a href="{{ route('coordenador.orgaos.index') }}"
               class="text-gray-600 hover:underline">← Voltar</a>

            <button type="submit"
                    class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                💾 Atualizar Órgão
            </button>
        </div>
    </form>
</div>
@endsection
