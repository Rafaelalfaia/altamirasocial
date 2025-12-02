@php
    $isEdit = isset($acompanhamento);
@endphp

@extends('layouts.app')

@section('title', 'Editar Acompanhamento')

@section('content')
<div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow-md">
    <h1 class="text-2xl font-bold text-green-700 mb-6">
        ✏️ Editar Acompanhamento – {{ $cidadao->nome }}
    </h1>

    <form method="POST" action="{{ route('assistente.acompanhamentos.update', $acompanhamento->id) }}">
        @csrf
        @method('PATCH')

        {{-- Seção 1: Identificação do Responsável --}}
        <h2 class="text-xl font-semibold text-gray-700 mb-4">👤 Identificação do Responsável Familiar</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-600">Nome do Responsável</label>
                <input type="text" name="nome_responsavel" class="w-full border rounded px-3 py-2"
                    value="{{ old('nome_responsavel', $acompanhamento->nome_responsavel) }}" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">CPF</label>
                <input type="text" name="cpf" class="w-full border rounded px-3 py-2"
                    value="{{ old('cpf', $acompanhamento->cpf) }}" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Estado Civil</label>
                <input type="text" name="estado_civil" class="w-full border rounded px-3 py-2"
                    value="{{ old('estado_civil', $acompanhamento->estado_civil) }}" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">WhatsApp</label>
                <input type="text" name="whatsapp" class="w-full border rounded px-3 py-2"
                    value="{{ old('whatsapp', $acompanhamento->whatsapp) }}" />
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-600">Endereço Completo</label>
                <input type="text" name="endereco" class="w-full border rounded px-3 py-2"
                    value="{{ old('endereco', $acompanhamento->endereco) }}" />
            </div>
        </div>
        <hr class="my-8">
        {{-- Seção 2: Perfil Socioeconômico --}}
        <h2 class="text-xl font-semibold text-gray-700 mb-4">📊 Perfil Socioeconômico</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-600">Você se considera:</label>
                @php $corSelecionada = old('cor', $acompanhamento->cor); @endphp
                <select name="cor" class="w-full border rounded px-3 py-2">
                    <option value="">Selecione</option>
                    @foreach(['Branco', 'Pardo', 'Negro', 'Amarelo', 'Indígena', 'Outros'] as $opcao)
                        <option value="{{ $opcao }}" {{ $corSelecionada === $opcao ? 'selected' : '' }}>
                            {{ $opcao }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Equipamentos comunitários próximos:</label>
                @php $equipamentosSelecionados = old('equipamentos_comunitarios', $acompanhamento->equipamentos_comunitarios ?? []); @endphp
                <div class="grid grid-cols-2 gap-2">
                    @foreach(['Quadra de Esportes', 'Escola', 'Posto de Saúde', 'Praça', 'Igreja', 'Creche', 'Centro Comunitário', 'Outros'] as $equip)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="equipamentos_comunitarios[]" value="{{ $equip }}"
                                {{ in_array($equip, $equipamentosSelecionados) ? 'checked' : '' }} class="mr-2">
                            {{ $equip }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Situação de Moradia</label>
                @php $sit = old('situacao_moradia', $acompanhamento->situacao_moradia); @endphp
                <select name="situacao_moradia" class="w-full border rounded px-3 py-2">
                    <option value="">Selecione</option>
                    @foreach(['Própria', 'Alugada', 'Cedida/Emprestada', 'Invasão', 'Outras'] as $opt)
                        <option value="{{ $opt }}" {{ $sit == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Tempo de residência</label>
                @php $tempo = old('tempo_residencia', $acompanhamento->tempo_residencia); @endphp
                <select name="tempo_residencia" class="w-full border rounded px-3 py-2">
                    <option value="">Selecione</option>
                    @foreach(['01 ano', '02 à 03 anos', '04 à 06 anos', 'Mais de 07 anos'] as $opt)
                        <option value="{{ $opt }}" {{ $tempo == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Nº de cômodos</label>
                @php $comodos = old('quantidade_comodos', $acompanhamento->quantidade_comodos); @endphp
                <select name="quantidade_comodos" class="w-full border rounded px-3 py-2">
                    @foreach(['01', '02', '03', '04', 'Mais de 05'] as $opt)
                        <option value="{{ $opt }}" {{ $comodos == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Tipo de construção</label>
                @php $tipo = old('tipo_construcao', $acompanhamento->tipo_construcao); @endphp
                <select name="tipo_construcao" class="w-full border rounded px-3 py-2">
                    @foreach(['Madeira', 'Alvenaria', 'Barro / Rudimentar', 'Outros'] as $opt)
                        <option value="{{ $opt }}" {{ $tipo == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Energia Elétrica</label>
                @php $energia = old('energia', $acompanhamento->energia); @endphp
                <select name="energia" class="w-full border rounded px-3 py-2">
                    @foreach(['Com medidor próprio', 'Sem padrão', 'Não possui'] as $opt)
                        <option value="{{ $opt }}" {{ $energia == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Abastecimento de água</label>
                @php $agua = old('agua', $acompanhamento->agua); @endphp
                <select name="agua" class="w-full border rounded px-3 py-2">
                    @foreach(['Rede geral', 'Poço', 'Fonte', 'Carro pipa', 'Outros'] as $opt)
                        <option value="{{ $opt }}" {{ $agua == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Rede de esgoto</label>
                @php $esgoto = old('esgoto', $acompanhamento->esgoto); @endphp
                <select name="esgoto" class="w-full border rounded px-3 py-2">
                    <option value="Sim" {{ $esgoto == 'Sim' ? 'selected' : '' }}>Sim</option>
                    <option value="Não" {{ $esgoto == 'Não' ? 'selected' : '' }}>Não</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Coleta de lixo</label>
                @php $lixo = old('lixo', $acompanhamento->lixo); @endphp
                <select name="lixo" class="w-full border rounded px-3 py-2">
                    <option value="Sim" {{ $lixo == 'Sim' ? 'selected' : '' }}>Sim</option>
                    <option value="Não" {{ $lixo == 'Não' ? 'selected' : '' }}>Não</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Tipo de rua</label>
                @php $rua = old('tipo_rua', $acompanhamento->tipo_rua); @endphp
                <select name="tipo_rua" class="w-full border rounded px-3 py-2">
                    @foreach(['Asfalto', 'Bloquete', 'Piçarra', 'Outros'] as $opt)
                        <option value="{{ $opt }}" {{ $rua == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Possui grávida na família?</label>
                @php $gravida = old('possui_gravida', $acompanhamento->possui_gravida); @endphp
                <select name="possui_gravida" class="w-full border rounded px-3 py-2">
                    <option value="0" {{ $gravida == 0 ? 'selected' : '' }}>Não</option>
                    <option value="1" {{ $gravida == 1 ? 'selected' : '' }}>Sim</option>
                </select>
                <input type="text" name="nome_gravida" class="mt-2 w-full border rounded px-3 py-2"
                    placeholder="Nome (se sim)" value="{{ old('nome_gravida', $acompanhamento->nome_gravida) }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Possui idoso na família?</label>
                @php $idoso = old('possui_idoso', $acompanhamento->possui_idoso); @endphp
                <select name="possui_idoso" class="w-full border rounded px-3 py-2">
                    <option value="0" {{ $idoso == 0 ? 'selected' : '' }}>Não</option>
                    <option value="1" {{ $idoso == 1 ? 'selected' : '' }}>Sim</option>
                </select>
                <input type="text" name="nome_idoso" class="mt-2 w-full border rounded px-3 py-2"
                    placeholder="Nome (se sim)" value="{{ old('nome_idoso', $acompanhamento->nome_idoso) }}">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-600">Situação profissional atual</label>
                <input type="text" name="situacao_profissional" class="w-full border rounded px-3 py-2"
                    value="{{ old('situacao_profissional', $acompanhamento->situacao_profissional ?? '') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Possui pessoa com deficiência?</label>
                @php $possuiDef = old('possui_deficiencia', $acompanhamento->possui_deficiencia ?? 0); @endphp
                <select name="possui_deficiencia" class="w-full border rounded px-3 py-2">
                    <option value="0" {{ $possuiDef == 0 ? 'selected' : '' }}>Não</option>
                    <option value="1" {{ $possuiDef == 1 ? 'selected' : '' }}>Sim</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Tipos de deficiência</label>
                <div class="grid grid-cols-2 gap-2">
                    @php
                        $tiposSelecionados = old('tipos_deficiencia', $acompanhamento->tipos_deficiencia ?? []);
                        if (is_string($tiposSelecionados)) {
                            $tiposSelecionados = json_decode($tiposSelecionados, true) ?? [];
                        }
                    @endphp
                    @foreach(['Visual', 'Auditiva', 'Mental/Intelectual', 'Síndrome de Down', 'Física', 'Múltiplas', 'Transtorno Mental', 'Outros'] as $def)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="tipos_deficiencia[]" value="{{ $def }}" class="mr-2"
                                {{ in_array($def, $tiposSelecionados) ? 'checked' : '' }}>
                            {{ $def }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="mb-6 mt-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">🗒️ Observações Finais</h2>
                <textarea name="observacoes" rows="5" class="w-full border px-4 py-3 rounded"
                    placeholder="Descreva qualquer observação adicional...">{{ old('observacoes', $acompanhamento->observacoes ?? '') }}</textarea>
            </div>
            <div class="text-right">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2 bg-green-700 text-white rounded hover:bg-green-800 transition">
                    💾 Atualizar Acompanhamento
                </button>
            </div>
        </form>
    </div>
@endsection
