@extends('layouts.app')

@section('title', 'Videoconferência de Emergência')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white p-4 sm:p-6 lg:p-8 rounded-2xl shadow-xl space-y-6">
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-red-700 flex items-center gap-2">
            🚨 Atendimento de Emergência
        </h1>

        {{-- Videoconferência responsiva com proporção vertical em telas pequenas --}}
        <div class="video-wrapper relative w-full overflow-hidden rounded-xl border border-gray-200 shadow-md">
            <iframe
                src="https://meet.jit.si/{{ $sala }}"
                allow="camera; microphone; fullscreen; display-capture"
                class="absolute top-0 left-0 w-full h-full rounded-xl"
                allowfullscreen>
            </iframe>
        </div>

        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
            Aguarde o(a) Assistente Social. <br>
            Use este espaço apenas em situações emergenciais. Após 5 minutos, recarregue a página.
        </p>
    </div>
</div>

{{-- CSS para alterar a proporção da div do vídeo --}}
<style>
    .video-wrapper {
        aspect-ratio: 16 / 9;
    }

    @media (max-width: 640px) {
        .video-wrapper {
            aspect-ratio: 3 / 4; /* mais vertical em mobile */
        }
    }
</style>
@endsection
