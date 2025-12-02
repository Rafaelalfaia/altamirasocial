@extends('layouts.app')

@section('title', 'Recuperar Senha')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-green-800 mb-4">🔐 Recuperar Senha</h1>
    <p class="text-sm text-gray-600 mb-6">
        Para recuperar sua senha, informe seu <strong>nome completo</strong> e <strong>CPF</strong>. Se encontrarmos seu cadastro, você passará por uma verificação adicional.
    </p>

    @if ($errors->has('mensagem'))
        <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded mb-4 text-sm">
            {{ $errors->first('mensagem') }}
        </div>
    @endif


    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-sm">
            @foreach ($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('cidadao.recuperar.verificar') }}" class="space-y-4">
        @csrf
    

        <div>
            <label for="nome" class="block text-sm font-medium text-gray-700">Nome completo</label>
            <input type="text" id="nome" name="nome" required
                   value="{{ old('nome') }}"
                   class="mt-1 w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-600 focus:border-green-600">
        </div>

        <div>
            <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
            <input type="text" id="cpf" name="cpf" required
                   value="{{ old('cpf') }}"
                   class="mt-1 w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-600 focus:border-green-600"
                   placeholder="000.000.000-00">
        </div>

        <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded transition">
            Continuar
        </button>
    </form>
</div>
@endsection
