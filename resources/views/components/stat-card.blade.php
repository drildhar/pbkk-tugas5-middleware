@props(['label', 'value', 'accent' => 'gray', 'hint' => null])

@php
    $accents = [
        'gray' => 'text-gray-900',
        'indigo' => 'text-indigo-600',
        'amber' => 'text-amber-600',
        'green' => 'text-green-600',
        'red' => 'text-red-600',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'overflow-hidden bg-white p-5 shadow-sm sm:rounded-lg']) }}>
    <dt class="truncate text-sm font-medium text-gray-500">{{ $label }}</dt>
    <dd class="mt-1 text-3xl font-semibold tracking-tight {{ $accents[$accent] ?? $accents['gray'] }}">
        {{ $value }}
    </dd>

    @if ($hint)
        <p class="mt-1 text-xs text-gray-400">{{ $hint }}</p>
    @endif
</div>
