<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Ajukan Proposal Baru</h2>
        <p class="mt-1 text-sm text-gray-500">
            Proposal akan berstatus <span class="font-semibold text-amber-700">Menunggu Peninjauan</span>
            sampai dosen atau asisten dosen memeriksanya.
        </p>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
            <div class="rounded-md bg-blue-50 p-4 ring-1 ring-inset ring-blue-600/20">
                <p class="text-sm text-blue-800">
                    <span class="font-semibold">Batas pengiriman:</span> maksimal 3 proposal per menit
                    (rate limiter kustom <code class="rounded bg-white px-1.5 py-0.5 text-xs font-semibold">submit-proposal</code>).
                </p>
            </div>

            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <x-proposal-form
                    :action="route('proposals.store')"
                    method="POST"
                    submit-label="Kirim Proposal"
                />
            </div>
        </div>
    </div>
</x-app-layout>
