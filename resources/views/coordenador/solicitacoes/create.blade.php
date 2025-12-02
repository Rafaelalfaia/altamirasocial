@extends('layouts.app')

@section('title', 'Nova Solicitação')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 md:p-8 rounded-xl shadow space-y-6">
    <h1 class="text-3xl font-bold text-green-800 flex items-center gap-2">
        📩 Criar Nova Solicitação
    </h1>

    {{-- Mensagens de sucesso --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded shadow">{{ session('success') }}</div>
    @endif

    {{-- Mensagens de erro --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded shadow">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('coordenador.solicitacoes.store') }}" class="space-y-6">
        @csrf

        {{-- Título --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Título da Solicitação</label>
            <input type="text" name="titulo" value="{{ old('titulo') }}" required
                   class="w-full rounded-lg border-gray-300 focus:ring-green-600 focus:border-green-600 shadow-sm">
        </div>

        {{-- Mensagem --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mensagem</label>
            <textarea name="mensagem" rows="5" required
                      class="w-full rounded-lg border-gray-300 focus:ring-green-600 focus:border-green-600 shadow-sm resize-none">{{ old('mensagem') }}</textarea>
        </div>

        {{-- Assistentes --}}
        <div class="bg-gray-100 p-4 rounded-lg border border-gray-200">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-green-700 font-semibold text-lg">👨‍💼 Assistentes</h2>
                <button type="button" onclick="toggleCheckboxes('assistente')" class="text-sm text-green-700 hover:underline">
                    Marcar/Desmarcar Todos
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($assistentes as $usuario)
                    <label class="flex items-center gap-2 text-sm bg-white px-3 py-2 rounded shadow-sm border">
                        <input type="checkbox" name="destinatarios_assistentes[]" value="{{ $usuario->id }}"
                               class="checkbox-assistente text-green-600">
                        <span class="flex-1">
                            {{ $usuario->name }} —
                            <span class="text-gray-500">{{ $usuario->telefone ?? 'Sem telefone' }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Cidadãos --}}
        <div class="bg-gray-100 p-4 rounded-lg border border-gray-200">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-green-700 font-semibold text-lg">👥 Cidadãos</h2>
                <button type="button" onclick="toggleCheckboxes('cidadao')" class="text-sm text-green-700 hover:underline">
                    Marcar/Desmarcar Todos
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($cidadaos as $usuario)
                    <label class="flex items-center gap-2 text-sm bg-white px-3 py-2 rounded shadow-sm border">
                        <input type="checkbox" name="destinatarios_cidadaos[]" value="{{ $usuario->id }}"
                               class="checkbox-cidadao text-green-600">
                        <span class="flex-1">
                            {{ $usuario->name }} —
                            <span class="text-gray-500">{{ $usuario->telefone ?? 'Sem telefone' }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Botão de envio --}}
        <div class="text-right">
            <button type="submit"
                    class="bg-green-700 hover:bg-green-800 text-white font-semibold px-6 py-2 rounded-lg shadow transition">
                ✅ Enviar Solicitação
            </button>
        </div>
    </form>
</div>

{{-- Script --}}
<script>
    function toggleCheckboxes(tipo) {
        const checkboxes = document.querySelectorAll('.checkbox-' + tipo);
        const todosMarcados = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !todosMarcados);
    }
</script>
@endsection
