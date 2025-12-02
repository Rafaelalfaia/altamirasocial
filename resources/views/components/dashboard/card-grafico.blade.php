@props(['id', 'title'])

<div class="bg-white p-4 rounded-xl shadow-md">
    <h3 class="text-lg font-semibold mb-2 text-gray-800">{{ $title }}</h3>
    <div class="relative h-72">
        <canvas id="{{ $id }}" class="w-full h-full"></canvas>
    </div>
</div>
