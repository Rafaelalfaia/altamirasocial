@extends('layouts.app')

@section('title', 'Inscritos Aprovados')

@section('content')
    <h1 class="text-2xl font-bold mb-6 text-green-800">✅ Aprovados – {{ $programa->nome }}</h1>

    <div class="mb-6">
        <a href="{{ route('coordenador.programas.inscritos.pdf', ['programa' => $programa->id, 'status' => 'aprovado']) }}"
            class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded shadow inline-block">
            ⬇️ Baixar Lista
        </a>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        @if($inscricoes->isEmpty())
            <p class="text-gray-600">Nenhum inscrito aprovado neste programa.</p>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b font-semibold text-left text-gray-700">
                        <th class="py-2">Cidadão</th>
                        <th>Status</th>
                        <th>Dependentes</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inscricoes as $inscricao)
                        <tr class="border-b">
                            <td class="py-2 font-medium text-gray-800">
                                {{ $inscricao->cidadao->nome }}
                            </td>
                            <td>
                                <span class="text-green-700 bg-green-100 px-2 py-1 rounded text-xs">
                                    Aprovado
                                </span>
                            </td>
                            <td>
                                @if($inscricao->dependentes && $inscricao->dependentes->count())
                                    <ul class="list-disc list-inside space-y-1 text-gray-700">
                                        @foreach($inscricao->dependentes as $dep)
                                            <li>
                                                <strong>{{ $dep->nome }}</strong>
                                                @if($dep->cpf)
                                                    – CPF: {{ $dep->cpf }}
                                                @endif
                                                <br>
                                                <small class="text-gray-600">({{ $dep->grau_parentesco }})</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-gray-400 italic">Sem dependentes</span>
                                @endif
                            </td>
                            <td class="space-x-2">
                                {{-- Mover para pendente --}}
                                <form method="POST"
                                    action="{{ route('coordenador.programas.atualizar-inscricao', [$programa->id, $inscricao->id]) }}"
                                    class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="pendente">
                                    <button class="text-yellow-600 text-sm hover:underline"
                                        onclick="return confirm('Mover para pendente?')">
                                        Pendente
                                    </button>
                                </form>

                                {{-- Reprovar --}}
                                <form method="POST"
                                    action="{{ route('coordenador.programas.reprovar', [$programa->id, $inscricao->id]) }}"
                                    class="inline">
                                    @csrf
                                    <button class="text-red-600 text-sm hover:underline ml-2"
                                        onclick="return confirm('Tem certeza que deseja reprovar?')">
                                        Reprovar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
