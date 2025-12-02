@props(['title', 'value', 'color' => 'green'])

@php
    $colors = [
        'green' => 'bg-green-100 text-green-800 ring-green-200',
        'blue' => 'bg-blue-100 text-blue-800 ring-blue-200',
        'purple' => 'bg-purple-100 text-purple-800 ring-purple-200',
        'indigo' => 'bg-indigo-100 text-indigo-800 ring-indigo-200',
        'orange' => 'bg-orange-100 text-orange-800 ring-orange-200',
        'red' => 'bg-red-100 text-red-800 ring-red-200',
        'gray' => 'bg-gray-100 text-gray-800 ring-gray-200',
    ];

    $style = $colors[$color] ?? $colors['gray'];
@endphp

<div class="flex flex-col items-center justify-center rounded-xl shadow-sm ring-1 {{ $style }} px-3 py-3 text-center hover:shadow transition duration-150 text-xs sm:text-sm">
    <div class="text-[0.65rem] font-semibold uppercase tracking-tight text-gray-600 leading-tight">
        {{ $title }}
    </div>
    <div class="mt-1 text-lg sm:text-xl font-bold text-gray-900">
        {{ $value }}
    </div>
</div>
