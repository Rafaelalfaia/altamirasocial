@extends('layouts.app')

@section('title', 'Editar Atendente')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">

    <h1 class="text-2xl font-bold mb-6 text-green-800">✏️ Editar Atendente</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-sm">
            @foreach ($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('restaurante.coordenador.atendentes.update', $atendente->id) }}">
        @csrf
        @method('PUT')

        {{-- Nome --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">Nome Completo</label>
            <input type="text" name="name" value="{{ old('name', $atendente->name) }}" required class="w-full border-gray-300 rounded px-3 py-2 shadow-sm">
        </div>

        {{-- CPF --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">CPF</label>
            <input type="text" name="cpf" id="cpf" value="{{ old('cpf', $atendente->cpf) }}" required
                   class="w-full border-gray-300 rounded px-3 py-2 shadow-sm" maxlength="14"
                   placeholder="000.000.000-00">
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">E-mail (opcional)</label>
            <input type="email" name="email" value="{{ old('email', $atendente->email) }}" class="w-full border-gray-300 rounded px-3 py-2 shadow-sm">
        </div>

        {{-- Senha (opcional) --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">Nova Senha (opcional)</label>
            <input type="password" name="password" class="w-full border-gray-300 rounded px-3 py-2 shadow-sm">
        </div>

        {{-- Confirmação de Senha --}}
        <div class="mb-4">
            <label class="block text-sm font-medium">Confirmar Nova Senha</label>
            <input type="password" name="password_confirmation" class="w-full border-gray-300 rounded px-3 py-2 shadow-sm">
        </div>

        {{-- Restaurantes --}}
        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">Restaurantes Vinculados</label>
            <select name="restaurantes[]" multiple required class="w-full border-gray-300 rounded px-3 py-2 shadow-sm">
                @foreach ($restaurantes as $restaurante)
                    <option value="{{ $restaurante->id }}" {{ in_array($restaurante->id, $atendente->restaurantes->pluck('id')->toArray()) ? 'selected' : '' }}>
                        {{ $restaurante->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white font-medium px-6 py-2 rounded">
                Atualizar Atendente
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('cpf').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = value;
    });
</script>
@endsection
