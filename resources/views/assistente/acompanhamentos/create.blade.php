@extends('layouts.app')

@section('title', 'Novo Acompanhamento')

@section('content')
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow-md">
        <h1 class="text-2xl font-bold text-green-700 mb-6">
            📝 Novo Acompanhamento – {{ $cidadao->nome }}
        </h1>

        <form method="POST" action="{{ route('assistente.acompanhamentos.store', $cidadao->id) }}">
            @csrf

            {{-- Seção 1: Identificação do Responsável --}}
            <h2 class="text-xl font-semibold text-gray-700 mb-4">👤 Identificação do Responsável Familiar</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-600">Nome do Responsável</label>
                    <input type="text" name="nome_responsavel" class="w-full border rounded px-3 py-2" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600">CPF</label>
                    <input type="text" name="cpf" class="w-full border rounded px-3 py-2" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600">Estado Civil</label>
                    <input type="text" name="estado_civil" class="w-full border rounded px-3 py-2" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600">WhatsApp</label>
                    <input type="text" name="whatsapp" class="w-full border rounded px-3 py-2" />
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-600">Endereço Completo</label>
                    <input type="text" name="endereco" class="w-full border rounded px-3 py-2" />
                </div>
            </div>

            <hr class="my-8">

            <h2 class="text-xl font-semibold text-gray-700 mb-4">📊 Perfil Socioeconômico</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                {{-- Cor/Raça --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600">Você se considera:</label>
                    <select name="cor" class="w-full border rounded px-3 py-2">
                        <option value="">Selecione</option>
                        <option>Branco</option>
                        <option>Pardo</option>
                        <option>Negro</option>
                        <option>Amarelo</option>
                        <option>Indígena</option>
                        <option>Outros</option>
                    </select>
                </div>

                {{-- Equipamentos comunitários --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600">Equipamentos comunitários próximos:</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['Quadra de Esportes', 'Escola', 'Posto de Saúde', 'Praça', 'Igreja', 'Creche', 'Centro Comunitário', 'Outros'] as $equip)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="equipamentos_comunitarios[]" value="{{ $equip }}" class="mr-2">
                                {{ $equip }}
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Situação de moradia --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600">Situação de Moradia</label>
                    <select name="situacao_moradia" class="w-full border rounded px-3 py-2">
                        <option value="">Selecione</option>
                        <option>Própria</option>
                        <option>Alugada</option>
                        <option>Cedida/Emprestada</option>
                        <option>Invasão</option>
                        <option>Outras</option>
                    </select>
                </div>

                {{-- Tempo de residência --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600">Tempo de residência</label>
                    <select name="tempo_residencia" class="w-full border rounded px-3 py-2">
                        <option value="">Selecione</option>
                        <option>01 ano</option>
                        <option>02 à 03 anos</option>
                        <option>04 à 06 anos</option>
                        <option>Mais de 07 anos</option>
                    </select>
                </div>

                {{-- Comodos --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600">Nº de cômodos</label>
                    <select name="quantidade_comodos" class="w-full border rounded px-3 py-2">
                        <option>01</option>
                        <option>02</option>
                        <option>03</option>
                        <option>04</option>
                        <option>Mais de 05</option>
                    </select>
                </div>

                {{-- Tipo de construção --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600">Tipo de construção</label>
                    <select name="tipo_construcao" class="w-full border rounded px-3 py-2">
                        <option>Madeira</option>
                        <option>Alvenaria</option>
                        <option>Barro / Rudimentar</option>
                        <option>Outros</option>
                    </select>
                </div>

                {{-- Energia --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600">Energia Elétrica</label>
                    <select name="energia" class="w-full border rounded px-3 py-2">
                        <option>Com medidor próprio</option>
                        <option>Sem padrão</option>
                        <option>Não possui</option>
                    </select>
                </div>

                {{-- Água --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600">Abastecimento de água</label>
                    <select name="agua" class="w-full border rounded px-3 py-2">
                        <option>Rede geral</option>
                        <option>Poço</option>
                        <option>Fonte</option>
                        <option>Carro pipa</option>
                        <option>Outros</option>
                    </select>
                </div>

                {{-- Esgoto --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600">Rede de esgoto</label>
                    <select name="esgoto" class="w-full border rounded px-3 py-2">
                        <option>Sim</option>
                        <option>Não</option>
                    </select>
                </div>

                {{-- Lixo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600">Coleta de lixo</label>
                    <select name="lixo" class="w-full border rounded px-3 py-2">
                        <option>Sim</option>
                        <option>Não</option>
                    </select>
                </div>

                {{-- Tipo de rua --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600">Tipo de rua</label>
                    <select name="tipo_rua" class="w-full border rounded px-3 py-2">
                        <option>Asfalto</option>
                        <option>Bloquete</option>
                        <option>Piçarra</option>
                        <option>Outros</option>
                    </select>
                </div>

                {{-- Gravidez --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600">Possui grávida na família?</label>
                    <select name="possui_gravida" class="w-full border rounded px-3 py-2">
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                    <input type="text" name="nome_gravida" class="mt-2 w-full border rounded px-3 py-2"
                        placeholder="Nome (se sim)">
                </div>

                {{-- Idoso --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600">Possui idoso na família?</label>
                    <select name="possui_idoso" class="w-full border rounded px-3 py-2">
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                    <input type="text" name="nome_idoso" class="mt-2 w-full border rounded px-3 py-2"
                        placeholder="Nome (se sim)">
                </div>

                {{-- Situação profissional --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-600">Situação profissional atual</label>
                    <input type="text" name="situacao_profissional" class="w-full border rounded px-3 py-2">
                </div>

                {{-- Deficiência --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600">Possui pessoa com deficiência?</label>
                    <select name="possui_deficiencia" class="w-full border rounded px-3 py-2">
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600">Tipos de deficiência</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['Visual', 'Auditiva', 'Mental/Intelectual', 'Síndrome de Down', 'Física', 'Múltiplas', 'Transtorno Mental', 'Outros'] as $def)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="tipos_deficiencia[]" value="{{ $def }}" class="mr-2">
                                {{ $def }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <hr class="my-8">

            <h2 class="text-xl font-semibold text-gray-700 mb-4">👨‍👩‍👧 Composição Familiar</h2>

            <div id="familia-wrapper" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-lg border" id="membro-0">
                    <input type="text" name="composicao[0][nome]" class="border px-3 py-2 rounded"
                        placeholder="Nome completo">
                    <input type="date" name="composicao[0][data_nascimento]" class="border px-3 py-2 rounded"
                        placeholder="Data de nascimento">
                    <input type="text" name="composicao[0][parentesco]" class="border px-3 py-2 rounded"
                        placeholder="Parentesco">
                    <input type="text" name="composicao[0][escolaridade]" class="border px-3 py-2 rounded"
                        placeholder="Escolaridade">

                    <input type="text" name="composicao[0][beneficio]" class="border px-3 py-2 rounded"
                        placeholder="Benefício">
                    <input type="number" step="0.01" name="composicao[0][valor_beneficio]" class="border px-3 py-2 rounded"
                        placeholder="R$ Benefício">
                    <input type="text" name="composicao[0][profissao]" class="border px-3 py-2 rounded"
                        placeholder="Profissão">
                    <input type="number" step="0.01" name="composicao[0][renda_bruta]" class="border px-3 py-2 rounded"
                        placeholder="R$ Renda Bruta">
                </div>
            </div>

            <div class="mt-4">
                <button type="button" onclick="adicionarMembro()" class="text-sm text-green-700 hover:underline">
                    ➕ Adicionar mais um membro
                </button>
            </div>

            <script>
                let membroIndex = 1;
                function adicionarMembro() {
                    const wrapper = document.getElementById('familia-wrapper');
                    const div = document.createElement('div');
                    div.className = 'grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-lg border';
                    div.innerHTML = `
                        <input type="text" name="composicao[${membroIndex}][nome]" class="border px-3 py-2 rounded" placeholder="Nome completo">
                        <input type="date" name="composicao[${membroIndex}][data_nascimento]" class="border px-3 py-2 rounded" placeholder="Data de nascimento">
                        <input type="text" name="composicao[${membroIndex}][parentesco]" class="border px-3 py-2 rounded" placeholder="Parentesco">
                        <input type="text" name="composicao[${membroIndex}][escolaridade]" class="border px-3 py-2 rounded" placeholder="Escolaridade">

                        <input type="text" name="composicao[${membroIndex}][beneficio]" class="border px-3 py-2 rounded" placeholder="Benefício">
                        <input type="number" step="0.01" name="composicao[${membroIndex}][valor_beneficio]" class="border px-3 py-2 rounded" placeholder="R$ Benefício">
                        <input type="text" name="composicao[${membroIndex}][profissao]" class="border px-3 py-2 rounded" placeholder="Profissão">
                        <input type="number" step="0.01" name="composicao[${membroIndex}][renda_bruta]" class="border px-3 py-2 rounded" placeholder="R$ Renda Bruta">
                    `;
                    wrapper.appendChild(div);
                    membroIndex++;
                }
            </script>

            <hr class="my-8">

            <h2 class="text-xl font-semibold text-gray-700 mb-4">🗒️ Observações Finais</h2>
            <div class="mb-6">
                <textarea name="observacoes" rows="5" class="w-full border px-4 py-3 rounded"
                    placeholder="Descreva qualquer observação adicional..."></textarea>
            </div>

            <div class="text-right">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2 bg-green-700 text-white rounded hover:bg-green-800 transition">
                    💾 Salvar Acompanhamento
                </button>
            </div>


        </form>
    </div>


@endsection