@props([
    'titulo' => 'Gráfico',
    'src' => null,
])

<div class="bg-white border p-4 rounded shadow text-center">
    <h3 class="text-md font-semibold text-gray-700 mb-2">
        {{ $titulo }}
    </h3>

    @if ($src)
        <img src="{{ $src }}" alt="{{ $titulo }}" class="mx-auto max-w-full">
    @else
        <p class="text-gray-500 italic">Gráfico não disponível.</p>
    @endif
</div>
