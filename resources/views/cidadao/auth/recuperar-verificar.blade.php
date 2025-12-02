@extends('layouts.app')

@section('title', 'Verificação de Identidade')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-green-800 mb-4">🕵️ Verifique sua Identidade</h1>
    <p class="text-sm text-gray-600 mb-6">
        Encontramos seu cadastro. Para garantir sua segurança, confirme os dados abaixo antes de redefinir sua senha.
    </p>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-sm">
            @foreach ($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('cidadao.recuperar.validar') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="cpf" value="{{ $cidadao->cpf }}">

        @if($cidadao->data_nascimento)
            <div>
                <label for="data_nascimento" class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                <input type="date" id="data_nascimento" name="data_nascimento" required
                       class="mt-1 w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-600 focus:border-green-600">
            </div>
        @endif

        @if($cidadao->rg)
            <div>
                <label for="rg" class="block text-sm font-medium text-gray-700">RG</label>
                <input type="text" id="rg" name="rg" required
                       class="mt-1 w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-600 focus:border-green-600">
            </div>
        @endif

        @if($cidadao->nis)
            <div>
                <label for="nis" class="block text-sm font-medium text-gray-700">NIS</label>
                <input type="text" id="nis" name="nis" required
                       class="mt-1 w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-600 focus:border-green-600">
            </div>
        @endif

        <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded transition">
            Confirmar e Continuar
        </button>
    </form>
</div>
@endsection
