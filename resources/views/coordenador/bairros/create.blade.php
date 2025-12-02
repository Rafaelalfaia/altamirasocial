@extends('layouts.app')

@section('title', 'Cadastro Hierárquico de Localidades')

@section('content')
    <div class="max-w-6xl mx-auto p-6 bg-white shadow rounded">
        <h1 class="text-2xl font-bold text-indigo-700 mb-6">🌍 Cadastro Hierárquico: Estado > Cidade > Bairro</h1>

        {{-- Mensagem de sucesso --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- ESTADOS --}}
            <div class="bg-gray-50 p-4 rounded border">
                <h2 class="text-lg font-semibold text-indigo-700 mb-3">🗺️ Estados</h2>
                <form method="POST" action="{{ route('coordenador.moradia.estado.salvar') }}" class="flex gap-2 mb-4">
                    @csrf
                    <input name="nome" type="text" placeholder="Nome do Estado" required
                        class="flex-1 border rounded px-3 py-2">
                    <button type="submit"
                        class="bg-indigo-600 text-white px-3 py-2 rounded hover:bg-indigo-700">Salvar</button>
                </form>
                <ul class="text-sm space-y-1 max-h-48 overflow-y-auto">
                    @foreach($estados as $estado)
                        <li class="flex justify-between items-center bg-white px-2 py-1 border rounded">
                            {{ $estado->nome }}
                            <form method="POST" action="{{ route('coordenador.moradia.estado.deletar', $estado) }}"
                                onsubmit="return confirm('Excluir estado?');">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-600 hover:underline">Excluir</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- CIDADES --}}
            <div class="bg-gray-50 p-4 rounded border">
                <h2 class="text-lg font-semibold text-blue-700 mb-3">🏙️ Cidades</h2>
                <form method="POST" action="{{ route('coordenador.moradia.cidade.salvar') }}" class="space-y-2 mb-4">
                    @csrf
                    <input name="nome" type="text" placeholder="Nome da Cidade" required
                        class="w-full border rounded px-3 py-2">
                    <select name="estado_id" required class="w-full border rounded px-3 py-2">
                        <option value="">Selecione um Estado</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->nome }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700">
                        Salvar Cidade
                    </button>
                </form>
                <ul class="text-sm space-y-1 max-h-48 overflow-y-auto">
                    @foreach($cidades as $cidade)
                        <li class="flex justify-between items-center bg-white px-2 py-1 border rounded">
                            <span>{{ $cidade->nome }} <span class="text-gray-400">({{ $cidade->estado->nome ?? '-' }})</span></span>
                            <form method="POST" action="{{ route('coordenador.moradia.cidade.deletar', $cidade) }}"
                                onsubmit="return confirm('Excluir cidade?');">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-600 hover:underline">Excluir</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- BAIRROS --}}
            <div class="bg-gray-50 p-4 rounded border">
                <h2 class="text-lg font-semibold text-green-700 mb-3">🏡 Bairros</h2>
                <form method="POST" action="{{ route('coordenador.moradia.bairro.salvar') }}" class="space-y-2 mb-4">
                    @csrf
                    <input name="nome" type="text" placeholder="Nome do Bairro" required
                        class="w-full border rounded px-3 py-2">

                    <select name="cidade_id" required class="w-full border rounded px-3 py-2">
                        <option value="">Selecione uma Cidade</option>
                        @foreach($cidades as $cidade)
                            <option value="{{ $cidade->id }}">
                                {{ $cidade->nome }} ({{ $cidade->estado->nome ?? '-' }})
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="w-full bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">
                        Salvar Bairro
                    </button>
                </form>

                <ul class="text-sm space-y-1 max-h-48 overflow-y-auto">
                    @foreach($bairros as $bairro)
                        <li class="flex justify-between items-center bg-white px-2 py-1 border rounded">
                            <span>
                                {{ $bairro->nome }}
                                <span class="text-gray-400">
                                    (
                                        {{ $bairro->cidade->nome ?? '-' }},
                                        {{ $bairro->cidade?->estado?->nome ?? '-' }}
                                    )
                                </span>
                            </span>
                            <form method="POST" action="{{ route('coordenador.moradia.bairro.deletar', $bairro) }}"
                                onsubmit="return confirm('Excluir bairro?');">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-600 hover:underline">Excluir</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
