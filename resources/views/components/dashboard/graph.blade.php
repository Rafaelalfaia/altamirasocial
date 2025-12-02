@props(['title', 'id'])

<div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-md hover:shadow-lg transition">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
        {{ $title }}
    </h2>
    <div class="relative h-64">
        <canvas id="{{ $id }}"></canvas>
    </div>
</div>
