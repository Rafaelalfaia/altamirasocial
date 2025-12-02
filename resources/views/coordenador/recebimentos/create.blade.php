@extends('layouts.app')

@section('title', $isEdit ? 'Editar Registro' : 'Novo Recebimento ou Encaminhamento')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-green-700 mb-6">
        {{ $isEdit ? '✏️ Editar Registro' : '➕ Novo Recebimento ou Encaminhamento' }}
    </h1>

    <form method="POST" action="{{ $isEdit ? route('coordenador.recebimentos.update', $registro->id) : route('coordenador.recebimentos.store') }}">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- Órgão Público + botão de adicionar --}}
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Selecionar Órgão Público</label>
            <div class="flex gap-2">
                <select name="orgao_publico_id" id="orgaoSelect" required class="w-full border rounded px-3 py-2">
                    <option value="">-- selecione --</option>
                    @foreach ($orgaos as $orgao)
                        <option value="{{ $orgao->id }}" {{ (old('orgao_publico_id', $registro->orgao_publico_id ?? '') == $orgao->id) ? 'selected' : '' }}>{{ $orgao->nome }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="abrirModalOrgao()"
                    class="bg-blue-600 text-white px-3 rounded hover:bg-blue-700">➕</button>
            </div>
        </div>

        {{-- Tipo --}}
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Tipo</label>
            <select name="tipo" required class="w-full border rounded px-3 py-2">
                <option value="">-- selecione --</option>
                <option value="recebimento" {{ old('tipo', $registro->tipo ?? '') === 'recebimento' ? 'selected' : '' }}>Recebimento</option>
                <option value="encaminhamento" {{ old('tipo', $registro->tipo ?? '') === 'encaminhamento' ? 'selected' : '' }}>Encaminhamento</option>
            </select>
        </div>

        {{-- Nome do cidadão --}}
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Nome do Cidadão</label>
            <input type="text" name="nome_cidadao" value="{{ old('nome_cidadao', $registro->nome_cidadao ?? '') }}"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        {{-- Selecionar cidadão opcional --}}
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Selecionar Cidadão (opcional)</label>
            <select name="cidadao_id" class="w-full border rounded px-3 py-2">
                <option value="">-- opcional --</option>
                @foreach ($cidadaos as $c)
                    <option value="{{ $c->id }}" {{ old('cidadao_id', $registro->cidadao_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->nome }}</option>
                @endforeach
            </select>
        </div>

        {{-- Programa social --}}
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Programa Social (opcional)</label>
            <select name="programa_social_id" class="w-full border rounded px-3 py-2">
                <option value="">-- opcional --</option>
                @foreach ($programas as $p)
                    <option value="{{ $p->id }}" {{ old('programa_social_id', $registro->programa_social_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->nome }}</option>
                @endforeach
            </select>
        </div>

        {{-- Descrição --}}
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Descrição</label>
            <textarea name="descricao" class="w-full border rounded px-3 py-2" rows="4">{{ old('descricao', $registro->descricao ?? '') }}</textarea>
        </div>

        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            {{ $isEdit ? 'Atualizar' : 'Salvar' }}
        </button>
        <a href="{{ route('coordenador.recebimentos.index') }}" class="ml-4 text-gray-600 hover:underline">Cancelar</a>
    </form>
</div>

{{-- Modal e script para adicionar órgão --}}
<div id="modalOrgao" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg w-96 p-6">
        <h2 class="text-lg font-bold mb-4">Novo Órgão</h2>
        <input type="text" id="novoOrgaoNome" placeholder="Nome do órgão" class="w-full border px-3 py-2 rounded mb-4">
        <div class="text-right">
            <button onclick="fecharModalOrgao()" class="text-gray-600 mr-4">Cancelar</button>
            <button onclick="salvarOrgao()" class="bg-green-600 text-white px-4 py-1 rounded">Salvar</button>
        </div>
    </div>
</div>

<script>
    function abrirModalOrgao() {
        document.getElementById('modalOrgao').classList.remove('hidden');
    }

    function fecharModalOrgao() {
        document.getElementById('modalOrgao').classList.add('hidden');
        document.getElementById('novoOrgaoNome').value = '';
    }

    function salvarOrgao() {
        const nome = document.getElementById('novoOrgaoNome').value;
        if (!nome) return alert('Informe o nome do órgão.');

        fetch("{{ route('coordenador.orgaos.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ nome })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('orgaoSelect');
                const option = document.createElement('option');
                option.value = data.id;
                option.text = data.nome;
                select.add(option);
                select.value = data.id;
                fecharModalOrgao();
            } else {
                alert('Erro ao salvar.');
            }
        });
    }
</script>
@endsection
