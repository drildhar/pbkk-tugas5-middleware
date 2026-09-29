<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Dasbor</h2>
                <p class="mt-1 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                    <span>Selamat datang, {{ $user->name }}.</span>
                    <x-role-badge :user="$user" />
                </p>
            </div>

            @if ($user->isMahasiswa())
                <a href="{{ route('proposals.create') }}"
                   class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Ajukan Proposal
                </a>
            @else
                <a href="{{ route('admin.proposals.index') }}"
                   class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Tinjau Semua Proposal
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
            <x-alert :status="session('status')" />

            {{-- Ringkasan angka --}}
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-stat-card
                    label="{{ $user->isMahasiswa() ? 'Proposal Saya' : 'Total Proposal' }}"
                    :value="$stats['total']"
                    accent="indigo"
                />
                <x-stat-card label="Menunggu Peninjauan" :value="$stats['pending']" accent="amber" />
                <x-stat-card label="Disetujui" :value="$stats['approved']" accent="green" />
                <x-stat-card label="Ditolak / Revisi" :value="$stats['rejected']" accent="red" />
            </dl>

            {{-- Daftar proposal terbaru --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h3 class="font-semibold text-gray-800">
                        {{ $user->isMahasiswa() ? 'Proposal Terbaru Saya' : 'Proposal Terbaru yang Masuk' }}
                    </h3>

                    <a href="{{ $user->isMahasiswa() ? route('proposals.index') : route('admin.proposals.index') }}"
                       class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">
                        Lihat semua
                    </a>
                </div>

                @if ($proposals->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <p class="text-sm text-gray-500">
                            {{ $user->isMahasiswa()
                                ? 'Anda belum mengajukan proposal proyek AI.'
                                : 'Belum ada proposal yang masuk untuk ditinjau.' }}
                        </p>

                        @if ($user->isMahasiswa())
                            <a href="{{ route('proposals.create') }}"
                               class="mt-4 inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm hover:bg-indigo-500">
                                Buat Proposal Pertama
                            </a>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Judul</th>
                                    @unless ($user->isMahasiswa())
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Mahasiswa</th>
                                    @endunless
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nilai</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Diperbarui</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($proposals as $proposal)
                                    <tr class="hover:bg-gray-50">
                                        <td class="max-w-xs px-6 py-4">
                                            <a href="{{ $user->isMahasiswa() ? route('proposals.show', $proposal) : route('admin.proposals.show', $proposal) }}"
                                               class="font-medium text-gray-900 hover:text-indigo-600">
                                                {{ $proposal->judul }}
                                            </a>
                                        </td>
                                        @unless ($user->isMahasiswa())
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $proposal->user->name }}</td>
                                        @endunless
                                        <td class="px-6 py-4"><x-status-badge :proposal="$proposal" /></td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $proposal->nilai !== null ? $proposal->nilai : '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $proposal->updated_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            @can('kelola-nilai')
                <div class="rounded-lg border border-indigo-100 bg-indigo-50 p-6">
                    <h3 class="font-semibold text-indigo-900">Hak akses Anda: pengelola nilai</h3>
                    <p class="mt-1 text-sm text-indigo-800">
                        Gate <code class="rounded bg-white px-1.5 py-0.5 text-xs font-semibold text-indigo-700">kelola-nilai</code>
                        mengizinkan Anda membuka area <span class="font-semibold">/admin</span>, mengubah status
                        proposal, serta menginput nilai dan catatan revisi.
                    </p>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
