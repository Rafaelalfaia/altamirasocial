@extends('layouts.app')

@section('title', 'Editar Solicitação')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow space-y-6">
    <h1 class="text-2xl font-bold text-yellow-700">✏️ Editar Solicitação</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('coordenador.solicitacoes.update', $solicitacao) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        {{-- Título --}}
        <div>
            <label for="titulo" class="block text-sm font-medium text-gray-700">Título da Solicitação</label>
            <input type="text" id="titulo" name="titulo" value="{{ old('titulo', $solicitacao->titulo) }}"
                class="w-full border rounded px-3 py-2 mt-1" required>
        </div>

        {{-- Mensagem --}}
        <div>
            <label for="mensagem" class="block text-sm font-medium text-gray-700">Mensagem</label>
            <textarea id="mensagem" name="mensagem" rows="4"
                class="w-full border rounded px-3 py-2 mt-1 resize-y" required>{{ old('mensagem', $solicitacao->mensagem) }}</textarea>
        </div>

        {{-- Tipo de Destinatário --}}
        <div>
            <label for="destinatario_tipo" class="block text-sm font-medium text-gray-700">Tipo de Destinatário</label>
            <select id="destinatario_tipo" name="destinatario_tipo"
                class="w-full border rounded px-3 py-2 mt-1" required>
                <option value="">Selecione o tipo</option>
                <option value="Assistente" {{ old('destinatario_tipo', $solicitacao->destinatario_tipo) == 'Assistente' ? 'selected' : '' }}>Assistente</option>
                <option value="Cidadao" {{ old('destinatario_tipo', $solicitacao->destinatario_tipo) == 'Cidadao' ? 'selected' : '' }}>Cidadão</option>
            </select>
        </div>

        {{-- Destinatário Individual (opcional) --}}
        <div>
            <label for="destinatario_id" class="block text-sm font-medium text-gray-700">
                Destinatário específico (opcional)
            </label>
            <input type="number" name="destinatario_id" id="destinatario_id"
                class="w-full border rounded px-3 py-2 mt-1"
                value="{{ old('destinatario_id', $solicitacao->destinatario_id) }}"
                placeholder="ID do destinatário (opcional)">
            <p class="text-xs text-gray-500 mt-1">Se vazio, será enviada para todos os do tipo selecionado.</p>
        </div>

        {{-- Botões --}}
        <div class="text-right pt-4">
            <a href="{{ route('coordenador.solicitacoes.index') }}"
               class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded mr-2">
                Cancelar
            </a>
            <button type="submit"
                class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 transition">
                💾 Salvar Alterações
            </button>
        </div>
    </form>
</div>
@endsection
