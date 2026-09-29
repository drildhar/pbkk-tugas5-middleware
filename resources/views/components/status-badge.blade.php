@props(['proposal'])

{{--
    Kelas Tailwind sengaja didefinisikan di komponen view (bukan di model) agar
    tetap terbaca oleh pemindai konten Tailwind saat `npm run build`.
--}}
@php
    $classes = match ($proposal->status) {
        \App\Models\Proposal::STATUS_APPROVED => 'bg-green-100 text-green-800 ring-green-600/20',
        \App\Models\Proposal::STATUS_REJECTED => 'bg-red-100 text-red-800 ring-red-600/20',
        default => 'bg-amber-100 text-amber-800 ring-amber-600/20',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {$classes}"]) }}>
    {{ $proposal->statusLabel() }}
</span>
