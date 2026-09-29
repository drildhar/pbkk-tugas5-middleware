<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $proposal->judul }}</h2>
                    <x-status-badge :proposal="$proposal" />
                </div>
                <p class="mt-1 text-sm text-gray-500">Peninjauan proposal — area dosen &amp; asisten dosen.</p>
            </div>

            <a href="{{ route('admin.proposals.index') }}"
               class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">
                &larr; Daftar Proposal
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto grid max-w-6xl grid-cols-1 gap-6 lg:grid-cols-3 sm:px-6 lg:px-8">
            <x-alert :status="session('status')" class="lg:col-span-3" />

            {{-- Detail proposal milik mahasiswa --}}
            <div class="space-y-6 lg:col-span-2">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <dl class="grid grid-cols-1 gap-px bg-gray-100 sm:grid-cols-2">
                        <div class="bg-white px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Mahasiswa</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-800">{{ $proposal->user->name }}</dd>
                            <dd class="text-xs text-gray-500">{{ $proposal->user->email }}</dd>
                        </div>
                        <div class="bg-white px-6 py-4">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Diajukan</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-800">
                                {{ $proposal->created_at->translatedFormat('d F Y, H:i') }}
                            </dd>
                            <dd class="text-xs text-gray-500">Diperbarui {{ $proposal->updated_at->diffForHumans() }}</dd>
                        </div>
                    </dl>

                    <div class="border-t border-gray-100 px-6 py-6">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Deskripsi</h3>
                        <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-gray-700">{{ $proposal->deskripsi }}</p>
                    </div>
                </div>

                @if ($proposal->catatan_revisi)
                    <div class="rounded-md bg-amber-50 p-4 ring-1 ring-inset ring-amber-600/20">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-amber-800">Catatan revisi terakhir</h3>
                        <p class="mt-2 whitespace-pre-line text-sm text-amber-900">{{ $proposal->catatan_revisi }}</p>
                    </div>
                @endif
            </div>

            {{-- Form penilaian: hanya untuk pemegang Gate `kelola-nilai` --}}
            <div class="lg:col-span-1">
                @can('kelola-nilai')
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-100 px-6 py-4">
                            <h3 class="font-semibold text-gray-800">Input Penilaian</h3>
                            <p class="mt-1 text-xs text-gray-500">
                                Dilindungi Gate <code class="rounded bg-gray-100 px-1 py-0.5 font-semibold">kelola-nilai</code>.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('admin.proposals.update', $proposal) }}" class="space-y-5 px-6 py-6">
                            @csrf
                            @method('PUT')

                            @if ($errors->any())
                                <div class="rounded-md bg-red-50 p-4 ring-1 ring-inset ring-red-600/20">
                                    <ul class="list-inside list-disc space-y-1 text-sm text-red-700">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div>
                                <x-input-label for="status" value="Status Peninjauan" />
                                <select id="status" name="status" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach (\App\Models\Proposal::STATUS_LABELS as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', $proposal->status) === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="nilai" value="Nilai (0-100)" />
                                <x-text-input id="nilai" name="nilai" type="number" min="0" max="100"
                                              class="mt-1 block w-full"
                                              :value="old('nilai', $proposal->nilai)" />
                                <p class="mt-1 text-xs text-gray-500">Wajib diisi bila status disetujui.</p>
                                <x-input-error :messages="$errors->get('nilai')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="catatan_revisi" value="Catatan Revisi" />
                                <textarea id="catatan_revisi" name="catatan_revisi" rows="5"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                          placeholder="Tuliskan umpan balik atau alasan penolakan.">{{ old('catatan_revisi', $proposal->catatan_revisi) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Wajib diisi bila status ditolak/perlu revisi.</p>
                                <x-input-error :messages="$errors->get('catatan_revisi')" class="mt-2" />
                            </div>

                            <x-primary-button class="w-full justify-center">Simpan Penilaian</x-primary-button>
                        </form>
                    </div>
                @else
                    <div class="rounded-md bg-gray-50 p-4 ring-1 ring-inset ring-gray-300">
                        <p class="text-sm text-gray-600">
                            Anda tidak memiliki hak untuk menginput nilai pada proposal ini.
                        </p>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
