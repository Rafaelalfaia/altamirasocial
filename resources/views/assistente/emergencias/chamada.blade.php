@extends('layouts.app')

@section('title', 'Videoconferência de Emergência')

@section('content')
<div class="max-w-7xl mx-auto p-6 bg-white rounded-2xl shadow-xl mt-6">
    <h1 class="text-2xl font-bold text-red-700 mb-6 flex items-center gap-2">
        🆘 Atendimento de Emergência – Assistente Social
    </h1>

    <div class="video-container w-full overflow-hidden rounded-xl border shadow relative">
        <iframe
            src="https://meet.jit.si/{{ $sala }}"
            allow="camera; microphone; fullscreen; display-capture"
            class="video-frame absolute top-0 left-0 w-full h-full"
            allowfullscreen>
        </iframe>
    </div>

    <p class="text-gray-600 text-sm mt-6">
        Você está conectado à sala de emergência. Prossiga com o atendimento ao cidadão. Após 5 minutos recarregue a página.
    </p>
</div>

{{-- CSS customizado para responsividade do vídeo --}}
<style>
    .video-container {
        aspect-ratio: 16 / 9;
    }

    @media (max-width: 640px) {
        .video-container {
            aspect-ratio: 3 / 4; /* formato retrato no celular */
        }
    }
</style>
@endsection
