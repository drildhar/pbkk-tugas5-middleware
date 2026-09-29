@props([
    'proposal' => null,
    'action',
    'method' => 'POST',
    'submitLabel' => 'Simpan Proposal',
])

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf

    @if (! in_array(strtoupper($method), ['GET', 'POST'], true))
        @method($method)
    @endif

    {{-- Ringkasan error validasi --}}
    @if ($errors->any())
        <div class="rounded-md bg-red-50 p-4 ring-1 ring-inset ring-red-600/20">
            <p class="text-sm font-semibold text-red-800">Periksa kembali isian berikut:</p>
            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <x-input-label for="judul" value="Judul Proposal" />
        <x-text-input
            id="judul"
            name="judul"
            type="text"
            class="mt-1 block w-full"
            :value="old('judul', $proposal?->judul)"
            placeholder="Contoh: Deteksi Dini Penyakit Daun Padi Menggunakan CNN"
            required
            autofocus
        />
        <x-input-error :messages="$errors->get('judul')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="deskripsi" value="Deskripsi Proposal" />
        <textarea
            id="deskripsi"
            name="deskripsi"
            rows="8"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="Jelaskan latar belakang masalah, metode/arsitektur AI yang dipakai, dataset, dan luaran yang diharapkan."
            required
        >{{ old('deskripsi', $proposal?->deskripsi) }}</textarea>
        <p class="mt-1 text-xs text-gray-500">Minimal 20 karakter, maksimal 5000 karakter.</p>
        <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <x-primary-button>{{ $submitLabel }}</x-primary-button>

        <a href="{{ $proposal ? route('proposals.show', $proposal) : route('proposals.index') }}"
           class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Batal
        </a>
    </div>
</form>
