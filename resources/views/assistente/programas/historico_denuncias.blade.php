@extends('layouts.app')

@section('title', 'Histórico de Denúncias')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold text-red-700 mb-6">📜 Histórico de Denúncias Realizadas</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 border border-green-300 rounded px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if ($denuncias->isEmpty())
        <div class="text-gray-600 text-center py-10">
            Nenhuma denúncia registrada ainda.
        </div>
    @else
        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700 font-semibold">
                    <tr>
                        <th class="px-4 py-3">Cidadão</th>
                        <th class="px-4 py-3">Programa</th>
                        <th class="px-4 py-3">Motivo</th>
                        <th class="px-4 py-3">Data</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($denuncias as $denuncia)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3 text-indigo-700 font-medium">
                                {{ $denuncia->cidadao->nome ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $denuncia->programa->nome ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $denuncia->motivo }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $denuncia->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
