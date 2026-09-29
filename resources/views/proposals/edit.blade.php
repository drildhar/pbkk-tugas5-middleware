<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Proposal</h2>
        <p class="mt-1 text-sm text-gray-500">
            Hanya judul dan deskripsi yang dapat diubah. Status dan nilai hanya boleh diubah oleh dosen/asdos.
        </p>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
            @if (! Auth::user()->isMahasiswa())
                <div class="rounded-md bg-indigo-50 p-4 ring-1 ring-inset ring-indigo-600/20">
                    <p class="text-sm text-indigo-800">
                        Anda mengedit proposal ini sebagai <span class="font-semibold">{{ Auth::user()->roleLabel() }}</span>,
                        bukan sebagai pemiliknya. Hak ini diberikan oleh <code class="rounded bg-white px-1.5 py-0.5 text-xs font-semibold">ProposalPolicy@update</code>.
                    </p>
                </div>
            @endif

            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <x-proposal-form
                    :proposal="$proposal"
                    :action="route('proposals.update', $proposal)"
                    method="PUT"
                    submit-label="Simpan Perubahan"
                />
            </div>
        </div>
    </div>
</x-app-layout>
