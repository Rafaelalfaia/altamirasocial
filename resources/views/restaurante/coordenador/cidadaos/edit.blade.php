@extends('layouts.app')

@section('title', 'Editar Cidadão')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-green-700 mb-6">✏️ Editar Cidadão</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('restaurante.coordenador.cidadaos.update', $cidadao->id) }}" method="POST" onsubmit="return validarCPF()">
        @csrf
        @method('PUT')

        {{-- Nome --}}
        <div class="mb-4">
            <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo</label>
            <input type="text" name="nome" id="nome" value="{{ old('nome', $cidadao->nome) }}" required
                   class="w-full border rounded px-3 py-2 mt-1">
        </div>

        {{-- CPF (apenas leitura) --}}
        <input type="text" name="cpf" id="cpf"
       value="{{ old('cpf', \Illuminate\Support\Str::of($cidadao->cpf)->padLeft(11, '0')->replaceMatches('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4')) }}"
       class="w-full border rounded px-3 py-2 mt-1"
       maxlength="14" required oninput="mascararCPF(this)">



        {{-- Email (opcional) --}}
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">E-mail (opcional)</label>
            <input type="email" name="email" id="email" value="{{ old('email', $cidadao->user->email ?? '') }}"
                   class="w-full border rounded px-3 py-2 mt-1">
        </div>

        {{-- Nova Senha (opcional) --}}
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Nova Senha (opcional)</label>
            <input type="password" name="password" id="password"
                class="w-full border rounded px-3 py-2 mt-1"
                placeholder="Deixe em branco para manter a atual">
        </div>

        {{-- Confirmar Nova Senha --}}
        <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                class="w-full border rounded px-3 py-2 mt-1">
        </div>


        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                💾 Atualizar
            </button>
        </div>
    </form>
</div>
@endsection
