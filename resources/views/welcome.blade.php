<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Secure Academic Portal ITS') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        {{-- Area publik: dapat diakses guest tanpa login --}}
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-2">
                    <x-application-logo class="h-9 w-auto fill-current text-indigo-600" />
                    <span class="text-sm font-semibold uppercase tracking-widest text-gray-700">
                        Secure Academic Portal
                    </span>
                </div>

                <nav class="flex items-center gap-3 text-sm">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="rounded-md px-3 py-2 font-semibold text-gray-700 hover:text-indigo-600">
                            Dasbor
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="rounded-md px-3 py-2 font-semibold text-gray-700 hover:text-indigo-600">
                            Masuk
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="rounded-md bg-indigo-600 px-4 py-2 font-semibold text-white shadow-sm hover:bg-indigo-500">
                                Daftar
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <div>
                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-indigo-700 ring-1 ring-inset ring-indigo-600/20">
                            PBKK — Pertemuan 5
                        </span>

                        <h1 class="mt-6 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                            Secure Academic Portal
                            <span class="block text-indigo-600">Institut Teknologi Sepuluh Nopember</span>
                        </h1>

                        <p class="mt-6 text-lg leading-relaxed text-gray-600">
                            Portal pengelolaan rencana proposal proyek kelompok AI dengan autentikasi
                            modern (Laravel Breeze) serta otorisasi berlapis menggunakan
                            <span class="font-semibold text-gray-800">middleware peran</span>,
                            <span class="font-semibold text-gray-800">Gates</span>, dan
                            <span class="font-semibold text-gray-800">Policies</span>.
                        </p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            @auth
                                <a href="{{ route('dashboard') }}"
                                   class="rounded-md bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                    Buka Dasbor
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                   class="rounded-md bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                    Masuk ke Portal
                                </a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}"
                                       class="rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                                        Buat Akun Mahasiswa
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500">
                            Tiga Peran, Tiga Area Akses
                        </h2>

                        <ul class="mt-6 space-y-5">
                            <li class="flex gap-4">
                                <span class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sm font-bold text-sky-700">1</span>
                                <div>
                                    <p class="font-semibold text-gray-900">Area Publik</p>
                                    <p class="text-sm text-gray-600">Landing page dan informasi umum, terbuka untuk guest.</p>
                                </div>
                            </li>
                            <li class="flex gap-4">
                                <span class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-bold text-gray-700">2</span>
                                <div>
                                    <p class="font-semibold text-gray-900">Area Mahasiswa</p>
                                    <p class="text-sm text-gray-600">Mengajukan, mengubah, dan menghapus proposal miliknya sendiri.</p>
                                </div>
                            </li>
                            <li class="flex gap-4">
                                <span class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700">3</span>
                                <div>
                                    <p class="font-semibold text-gray-900">Area Dosen &amp; Asisten</p>
                                    <p class="text-sm text-gray-600">Meninjau seluruh proposal, memberi status, nilai, dan catatan revisi.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-gray-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-8 text-sm text-gray-500 sm:px-6 lg:px-8">
                <p class="font-semibold text-gray-700">Adriel Mahira Dharma — 5025241097 — PBKK B</p>
                <p class="mt-1">Departemen Teknik Informatika, Institut Teknologi Sepuluh Nopember.</p>
            </div>
        </footer>
    </body>
</html>
