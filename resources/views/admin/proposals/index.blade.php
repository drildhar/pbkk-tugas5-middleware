<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Tinjau Proposal</h2>
                <p class="mt-1 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                    <span>Area khusus dosen &amp; asisten dosen.</span>
                    <x-role-badge :user="Auth::user()" />
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <x-alert :status="session('status')" />

            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-stat-card label="Total Proposal" :value="$stats['total']" accent="indigo" />
                <x-stat-card label="Menunggu Peninjauan" :value="$stats['pending']" accent="amber" />
                <x-stat-card label="Disetujui" :value="$stats['approved']" accent="green" />
                <x-stat-card label="Ditolak / Revisi" :value="$stats['rejected']" accent="red" />
            </dl>

            {{-- Filter & pencarian --}}
            <form method="GET" action="{{ route('admin.proposals.index') }}"
                  class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <x-input-label for="filter-status" value="Status" />
                        <select id="filter-status" name="status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua status</option>
                            @foreach (\App\Models\Proposal::STATUS_LABELS as $value => $label)
                                <option value="{{ $value }}" @selected($filters['status'] === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="filter-q" value="Cari Judul" />
                        <x-text-input id="filter-q" name="q" type="search" class="mt-1 block w-full"
                                      :value="$filters['q']" placeholder="Kata kunci judul..." />
                    </div>

                    <div class="flex items-end gap-3">
                        <x-primary-button>Terapkan</x-primary-button>
                        <a href="{{ route('admin.proposals.index') }}"
                           class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                @if ($proposals->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <h3 class="text-base font-semibold text-gray-800">Tidak ada proposal yang cocok</h3>
                        <p class="mt-2 text-sm text-gray-500">Coba ubah filter status atau kata kunci pencarian.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Judul</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Mahasiswa</th>
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
                                            <a href="{{ route('admin.proposals.show', $proposal) }}"
                                               class="font-medium text-gray-900 hover:text-indigo-600">
                                                {{ $proposal->judul }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $proposal->user->name }}</td>
                                        <td class="px-6 py-4"><x-status-badge :proposal="$proposal" /></td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-700">
                                            {{ $proposal->nilai !== null ? $proposal->nilai : '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $proposal->updated_at->diffForHumans() }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('admin.proposals.show', $proposal) }}"
                                               class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">
                                                Tinjau
                                            </a>
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
