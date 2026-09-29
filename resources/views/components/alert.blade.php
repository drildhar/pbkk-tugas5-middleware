@props(['status' => null])

@if ($status)
    <div
        role="status"
        {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-md bg-green-50 p-4 ring-1 ring-inset ring-green-600/20']) }}
    >
        <svg class="h-5 w-5 shrink-0 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>

        <p class="text-sm font-medium text-green-800">{{ $status }}</p>
    </div>
@endif
