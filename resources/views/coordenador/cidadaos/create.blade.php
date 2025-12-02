@extends('layouts.app')

@section('title', 'Novo Cidadão')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-green-700 mb-6">👤 Criar Novo Cidadão</h1>

    @includeFirst(['cidadao.perfil.partials._errors', 'layouts.partials._errors'])

    <form method="POST" action="{{ route('coordenador.cidadaos.store') }}" class="space-y-4">
        @csrf
        @include('coordenador.cidadaos._form')
        <div class="pt-2">
            <button class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                💾 Salvar
            </button>
            <a href="{{ route('coordenador.cidadaos.index') }}" class="ml-2 text-gray-600 hover:underline">Cancelar</a>
        </div>
    </form>
</div>
@endsection
