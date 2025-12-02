@extends('layouts.app')

@section('title', 'Acompanhamentos')

@section('content')
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow-md">
        {{-- Cabeçalho --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">
                📋 Acompanhamentos Realizados
            </h1>
            <a href="{{ route('assistente.acompanhamentos.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-green-600 text-green-700 hover:bg-green-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="text-sm font-medium">Novo Acompanhamento</span>
            </a>
        </div>

        {{-- Alerta de sucesso --}}
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-2 rounded mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Formulário de busca --}}
        <form method="GET" class="mb-6">
            <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                <input type="text" name="busca" placeholder="🔍 Buscar por nome ou CPF" value="{{ request('busca') }}"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-sm shadow-sm focus:ring-green-500 focus:border-green-500">
                <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 transition">
                    Buscar
                </button>
            </div>
        </form>

        {{-- Tabela de Acompanhamentos --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-gray-600">
                        <th class="px-4 py-3 font-medium">Cidadão</th>
                        <th class="px-4 py-3 font-medium">CPF</th>
                        <th class="px-4 py-3 font-medium">Data</th>
                        <th class="px-4 py-3 font-medium">Assistente</th>
                        <th class="px-4 py-3 font-medium text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($acompanhamentos as $acomp)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-800">{{ $acomp->cidadao->nome }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $acomp->cidadao->cpf }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $acomp->data->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $acomp->assistente->name }}</td>
                            <td class="px-4 py-3 text-center">
                                <div
                                    class="flex flex-col space-y-2 md:flex-row md:space-y-0 md:space-x-2 text-sm justify-center">

                                    {{-- Ver 1º Atendimento --}}
                                    <a href="{{ route('cidadao.ficha', $acomp->cidadao->id) }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition">
                                        📖 Ficha do Cidadão
                                     </a>
                                     
                                    

                                    {{-- Nova Evolução --}}
                                    @php $total = $acomp->cidadao->acompanhamentos->count(); @endphp
                                    @if ($total >= 1)
                                        <a href="{{ route('assistente.evolucoes.index', $acomp->id) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 rounded hover:bg-green-200 transition">
                                            ➕ Evolução
                                        </a>
                                    @endif

                                    {{-- Excluir --}}
                                    <form action="{{ route('assistente.acompanhamentos.destroy', $acomp->id) }}" method="POST"
                                        onsubmit="return confirm('Deseja realmente excluir este acompanhamento?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 rounded hover:bg-red-200 transition">
                                            🗑️ Excluir
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500 italic">
                                Nenhum acompanhamento registrado ainda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>



        {{-- Paginação --}}
        @if ($acompanhamentos->hasPages())
            <div class="mt-6">
                {{ $acompanhamentos->links() }}
            </div>
        @endif
    </div>
@endsection