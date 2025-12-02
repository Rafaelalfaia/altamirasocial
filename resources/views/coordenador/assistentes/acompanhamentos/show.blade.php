@extends('layouts.app')

@section('title', 'Relatório do Acompanhamento')

@section('content')
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow-md">
        <h1 class="text-2xl font-bold text-green-700 mb-6">📋 Relatório do Acompanhamento</h1>

        {{-- Cabeçalho --}}
        <div class="mb-6">
            <p><strong>Cidadão:</strong> {{ $acompanhamento->cidadao->nome }}</p>
            <p><strong>Data do Atendimento:</strong> {{ $acompanhamento->data->format('d/m/Y') }}</p>
            <p><strong>Assistente:</strong> {{ $acompanhamento->assistente->name }}</p>
        </div>

        {{-- Identificação --}}
        <h2 class="text-lg font-semibold text-gray-700 mb-2">👤 Identificação</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-6">
            <p><strong>Responsável:</strong> {{ $acompanhamento->nome_responsavel }}</p>
            <p><strong>CPF:</strong> {{ $acompanhamento->cpf }}</p>
            <p><strong>Estado Civil:</strong> {{ $acompanhamento->estado_civil }}</p>
            <p><strong>WhatsApp:</strong> {{ $acompanhamento->whatsapp }}</p>
            <p class="md:col-span-2">
                <strong>Endereço:</strong> {{ $acompanhamento->endereco }}
                @if($acompanhamento->numero)
                    , Nº {{ $acompanhamento->numero }}
                @endif
            </p>
        </div>

        {{-- Socioeconômico --}}
        <h2 class="text-lg font-semibold text-gray-700 mb-2">📊 Informações Socioeconômicas</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm mb-6">
            <p><strong>Cor/Raça:</strong> {{ $acompanhamento->cor }}</p>
            <p><strong>Equipamentos:</strong>
                {{ $acompanhamento->equipamentos_comunitarios ? implode(', ', $acompanhamento->equipamentos_comunitarios) : '—' }}
            </p>
            <p><strong>Situação de moradia:</strong> {{ $acompanhamento->situacao_moradia }}</p>
            <p><strong>Tempo no imóvel:</strong> {{ $acompanhamento->tempo_residencia }}</p>
            <p><strong>Cômodos:</strong> {{ $acompanhamento->quantidade_comodos }}</p>
            <p><strong>Construção:</strong> {{ $acompanhamento->tipo_construcao }}</p>
            <p><strong>Energia:</strong> {{ $acompanhamento->energia }}</p>
            <p><strong>Água:</strong> {{ $acompanhamento->agua }}</p>
            <p><strong>Esgoto:</strong> {{ $acompanhamento->esgoto }}</p>
            <p><strong>Lixo:</strong> {{ $acompanhamento->lixo }}</p>
            <p><strong>Rua:</strong> {{ $acompanhamento->tipo_rua }}</p>
            <p><strong>Grávida:</strong> {{ $acompanhamento->possui_gravida ? 'Sim' : 'Não' }} {{ $acompanhamento->nome_gravida }}</p>
            <p><strong>Idoso:</strong> {{ $acompanhamento->possui_idoso ? 'Sim' : 'Não' }} {{ $acompanhamento->nome_idoso }}</p>
            <p><strong>Situação profissional:</strong> {{ $acompanhamento->situacao_profissional }}</p>
            <p><strong>Deficiência:</strong> {{ $acompanhamento->possui_deficiencia ? 'Sim' : 'Não' }}</p>
            <p><strong>Tipos:</strong>
                {{ $acompanhamento->tipos_deficiencia ? implode(', ', $acompanhamento->tipos_deficiencia) : '—' }}
            </p>
        </div>

        {{-- Composição Familiar --}}
        @if($acompanhamento->composicaoFamiliar->count())
            <h2 class="text-lg font-semibold text-gray-700 mb-2">👨‍👩‍👧 Composição Familiar</h2>
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="border px-3 py-2">Nome</th>
                            <th class="border px-3 py-2">Nascimento</th>
                            <th class="border px-3 py-2">Parentesco</th>
                            <th class="border px-3 py-2">Escolaridade</th>
                            <th class="border px-3 py-2">Benefício</th>
                            <th class="border px-3 py-2">Valor Benefício</th>
                            <th class="border px-3 py-2">Profissão</th>
                            <th class="border px-3 py-2">Renda Bruta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($acompanhamento->composicaoFamiliar as $membro)
                            <tr class="border-b">
                                <td class="border px-3 py-2">{{ $membro->nome }}</td>
                                <td class="border px-3 py-2">{{ optional($membro->data_nascimento)->format('d/m/Y') }}</td>
                                <td class="border px-3 py-2">{{ $membro->parentesco }}</td>
                                <td class="border px-3 py-2">{{ $membro->escolaridade }}</td>
                                <td class="border px-3 py-2">{{ $membro->beneficio }}</td>
                                <td class="border px-3 py-2">R$ {{ number_format($membro->valor_beneficio, 2, ',', '.') }}</td>
                                <td class="border px-3 py-2">{{ $membro->profissao }}</td>
                                <td class="border px-3 py-2">R$ {{ number_format($membro->renda_bruta, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Observações --}}
        <h2 class="text-lg font-semibold text-gray-700 mb-2">🗒️ Observações Finais</h2>
        <p class="text-gray-800 text-sm whitespace-pre-line">{{ $acompanhamento->observacoes }}</p>
    </div>
@endsection
