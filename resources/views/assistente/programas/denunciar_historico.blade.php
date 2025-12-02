@extends('layouts.app')

@section('title', 'Histórico de Denúncias')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-red-700">📂 Histórico de Denúncias</h1>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if ($denuncias->isEmpty())
        <p class="text-gray-500">Nenhuma denúncia registrada ainda.</p>
    @else
        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700 font-semibold">
                    <tr>
                        <th class="px-4 py-3">Programa</th>
                        <th class="px-4 py-3">Cidadão</th>
                        <th class="px-4 py-3">Motivo</th>
                        <th class="px-4 py-3">Data</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($denuncias as $denuncia)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2 text-indigo-700 font-medium">{{ $denuncia->programa->nome }}</td>
                            <td class="px-4 py-2">{{ $denuncia->cidadao->nome }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ $denuncia->motivo }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $denuncia->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
