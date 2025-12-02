@extends('layouts.app')

@section('title', 'Programas Sociais')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-indigo-700">📋 Programas Sociais</h1>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 font-semibold">
                <tr>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3 text-center">Vagas</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($programas as $programa)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-indigo-700">
                            {{ $programa->nome }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            {{ $programa->vagas }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($programa->status === 'ativado')
                                <span class="inline-block px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                    Ativado
                                </span>
                            @else
                                <span class="inline-block px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                    Desativado
                                </span>
                            @endif
                        </td>
                        <<td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('assistente.programas.indicar', $programa->id) }}"
                                class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded text-xs hover:bg-green-200 transition">
                                ✅ Indicar
                            </a>
                            <a href="{{ route('assistente.programas.denunciar', $programa->id) }}"
                                class="inline-block bg-red-100 text-red-700 px-3 py-1 rounded text-xs hover:bg-red-200 transition">
                                ⚠️ Denunciar
                            </a>
                            <a href="{{ route('assistente.programas.denunciar.historico') }}"
                                class="inline-block bg-indigo-100 text-indigo-700 px-3 py-1 rounded text-xs hover:bg-indigo-200 transition">
                                📁 Histórico
                            </a>

                        </td>
                        
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">Nenhum programa cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $programas->links() }}
    </div>
@endsection
