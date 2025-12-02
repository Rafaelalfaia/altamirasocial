@extends('layouts.app')

@section('title', 'Teste de Videoconferência')

@section('content')
    <div class="max-w-6xl mx-auto p-6 bg-white rounded shadow">
        <h1 class="text-2xl font-bold text-green-700 mb-4">🎥 Videoconferência de Teste</h1>
        <p class="text-sm text-gray-600 mb-4">
            Esta é uma sala de teste do Jitsi Meet integrada no sistema SEMAPS.
        </p>

        <div class="w-full aspect-video rounded overflow-hidden shadow">
            <iframe
            src="https://alchat.shop/semaps-teste"

                allow="camera; microphone; fullscreen; display-capture"
                style="width: 100%; height: 100%; border: none;">
            </iframe>
        </div>
    </div>
@endsection
