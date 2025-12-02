@props(['id', 'title', 'tipo' => 'bar'])

<div class="bg-white p-4 rounded-xl shadow-md">
    <h3 class="text-lg font-semibold mb-2 text-gray-800">{{ $title }}</h3>

    {{-- Loading --}}
    <div id="loading-{{ $id }}" class="text-gray-500 text-sm">Carregando gráfico...</div>

    {{-- Canvas --}}
    <div class="relative h-72">
        <canvas id="{{ $id }}" class="w-full h-full hidden"></canvas>
    </div>
</div>
