@extends('layouts.app')

@section('title', 'Redefinir Senha')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-green-800 mb-4">🔒 Redefinir Senha</h1>
    <p class="text-sm text-gray-600 mb-6">
        Tudo certo! Agora escolha uma nova senha para acessar o sistema.
    </p>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-sm">
            @foreach ($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('cidadao.recuperar.verificar') }}" class="space-y-4">
        @csrf
    

        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Nova Senha</label>
            <input type="password" id="password" name="password" required
                   class="mt-1 w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-600 focus:border-green-600">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                   class="mt-1 w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-600 focus:border-green-600">
        </div>

        <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded transition">
            Redefinir Senha
        </button>
    </form>
</div>
@endsection
