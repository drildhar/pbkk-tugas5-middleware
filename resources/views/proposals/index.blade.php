<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Proposal Saya</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola rencana proposal proyek kelompok AI Anda.
                </p>
            </div>

            <a href="{{ route('proposals.create') }}"
               class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Ajukan Proposal
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <x-alert :status="session('status')" />

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                @if ($proposals->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <h3 class="text-base font-semibold text-gray-800">Belum ada proposal</h3>
                        <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">
                            Anda belum mengajukan proposal proyek AI. Mulai dengan menuliskan judul dan
                            deskripsi rencana proyek kelompok Anda.
                        </p>
                        <a href="{{ route('proposals.create') }}"
                           class="mt-6 inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm hover:bg-indigo-500">
                            Buat Proposal Pertama
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Judul</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nilai</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Diperbarui</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($proposals as $proposal)
                                    <tr class="hover:bg-gray-50">
                                        <td class="max-w-md px-6 py-4">
                                            <a href="{{ route('proposals.show', $proposal) }}"
                                               class="font-medium text-gray-900 hover:text-indigo-600">
                                                {{ $proposal->judul }}
                                            </a>
                                            <p class="mt-1 line-clamp-2 text-xs text-gray-500">
                                                {{ \Illuminate\Support\Str::limit($proposal->deskripsi, 120) }}
                                            </p>
                                        </td>
                                        <td class="px-6 py-4"><x-status-badge :proposal="$proposal" /></td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-700">
                                            {{ $proposal->nilai !== null ? $proposal->nilai : '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $proposal->updated_at->diffForHumans() }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-end gap-3 text-sm">
                                                <a href="{{ route('proposals.show', $proposal) }}"
                                                   class="font-semibold text-gray-600 hover:text-indigo-600">
                                                    Detail
                                                </a>

                                                {{-- Tombol edit hanya dirender bila Policy mengizinkan. --}}
                                                @can('update', $proposal)
                                                    <a href="{{ route('proposals.edit', $proposal) }}"
                                                       class="font-semibold text-indigo-600 hover:text-indigo-500">
                                                        Edit
                                                    </a>
                                                @endcan

                                                @can('delete', $proposal)
                                                    <form method="POST" action="{{ route('proposals.destroy', $proposal) }}"
                                                          onsubmit="return confirm('Hapus proposal ini secara permanen?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="font-semibold text-red-600 hover:text-red-500">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($proposals->hasPages())
                        <div class="border-t border-gray-100 px-6 py-4">
                            {{ $proposals->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
