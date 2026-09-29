@props(['user'])

@php
    $classes = match ($user->role) {
        \App\Models\User::ROLE_DOSEN => 'bg-indigo-100 text-indigo-800 ring-indigo-600/20',
        \App\Models\User::ROLE_ASDOS => 'bg-sky-100 text-sky-800 ring-sky-600/20',
        default => 'bg-gray-100 text-gray-700 ring-gray-500/20',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {$classes}"]) }}>
    {{ $user->roleLabel() }}
</span>
