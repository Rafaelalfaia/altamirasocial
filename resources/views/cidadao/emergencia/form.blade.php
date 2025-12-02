@extends('layouts.app')

@section('title', 'Emergência')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold text-red-700 mb-6">🚨 Solicitar Atendimento de Emergência</h1>

        <p class="text-gray-700 mb-4">
            Use este canal apenas em casos graves. A equipe assistente será notificada imediatamente.
        </p>

        <form action="{{ route('cidadao.emergencia.store') }}" method="POST">
            @csrf

            {{-- Motivo da Emergência --}}
            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
            <select name="motivo" required
                class="w-full mb-4 border-gray-300 rounded shadow-sm focus:ring focus:ring-red-300">
                <option value="" disabled selected>Selecione uma opção</option>
                <option value="Violência Sexual">Violência Sexual</option>
                <option value="Violência Doméstica">Violência Doméstica</option>
                <option value="Tentativa de Homicídio">Tentativa de Homicídio</option> 
                <option value="Denunciar crimes">Denunciar crimes</option>
                <option value="Pedido de alimentos">Pedido de alimentos (Pobreza extrema)</option>
            </select>

            {{-- Descrição --}}
            <label class="block text-sm font-medium text-gray-700 mb-1">Descreva a Situação</label>
            <textarea name="descricao" rows="4"
                class="w-full border-gray-300 rounded shadow-sm focus:ring focus:ring-red-300"
                placeholder="Escreva aqui o que está acontecendo..." required></textarea>

            <button type="submit"
                class="mt-6 w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded shadow">
                🎥 Iniciar Videoconferência
            </button>
        </form>
    </div>
@endsection
