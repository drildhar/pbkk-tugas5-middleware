<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ $proposal->judul }}
                    </h2>
                    <x-status-badge :proposal="$proposal" />
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Diajukan oleh {{ $proposal->user->name }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @can('kelola-nilai')
                    <a href="{{ route('admin.proposals.show', $proposal) }}"
                       class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm hover:bg-indigo-500">
                        Nilai di Area Admin
                    </a>
                @endcan

                {{-- Tombol aksi dirender sesuai hasil ProposalPolicy. --}}
                @can('update', $proposal)
                    <a href="{{ route('proposals.edit', $proposal) }}"
                       class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">
                        Edit
                    </a>
                @endcan

                @can('delete', $proposal)
                    <form method="POST" action="{{ route('proposals.destroy', $proposal) }}"
                          onsubmit="return confirm('Hapus proposal ini secara permanen?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm hover:bg-red-500">
                            Hapus
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
            <x-alert :status="session('status')" />

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <dl class="grid grid-cols-1 gap-px bg-gray-100 sm:grid-cols-3">
                    <div class="bg-white px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Pemilik</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-800">{{ $proposal->user->name }}</dd>
                    </div>
                    <div class="bg-white px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Diajukan</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-800">
                            {{ $proposal->created_at->translatedFormat('d F Y, H:i') }}
                        </dd>
                    </div>
                    <div class="bg-white px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Terakhir diperbarui</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-800">
                            {{ $proposal->updated_at->diffForHumans() }}
                        </dd>
                    </div>
                </dl>

                <div class="border-t border-gray-100 px-6 py-6">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Deskripsi</h3>
                    <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-gray-700">{{ $proposal->deskripsi }}</p>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h3 class="font-semibold text-gray-800">Hasil Peninjauan</h3>
                </div>

                <div class="grid grid-cols-1 gap-px bg-gray-100 sm:grid-cols-3">
                    <div class="bg-white px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status</dt>
                        <dd class="mt-2"><x-status-badge :proposal="$proposal" /></dd>
                    </div>
                    <div class="bg-white px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nilai</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900">
                            {{ $proposal->nilai !== null ? $proposal->nilai : '—' }}
                        </dd>
                    </div>
                    <div class="bg-white px-6 py-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Perlu tindakan?</dt>
                        <dd class="mt-1 text-sm font-medium {{ $proposal->isRejected() ? 'text-red-700' : 'text-gray-800' }}">
                            {{ $proposal->isRejected() ? 'Ya, revisi deskripsi proposal.' : 'Tidak ada.' }}
                        </dd>
                    </div>
                </div>

                <div class="border-t border-gray-100 px-6 py-6">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Catatan Revisi</h4>

                    @if ($proposal->catatan_revisi)
                        <p class="mt-3 whitespace-pre-line rounded-md bg-amber-50 p-4 text-sm leading-relaxed text-amber-900 ring-1 ring-inset ring-amber-600/20">
                            {{ $proposal->catatan_revisi }}
                        </p>
                    @else
                        <p class="mt-3 text-sm text-gray-500">Belum ada catatan revisi dari dosen/asdos.</p>
                    @endif
                </div>
            </div>

            <div>
                <a href="{{ route('proposals.index') }}"
                   class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">
                    &larr; Kembali ke daftar proposal
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
