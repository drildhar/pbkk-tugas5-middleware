<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Terlalu Banyak Permintaan — {{ config('app.name', 'Secure Academic Portal ITS') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="flex min-h-screen items-center justify-center bg-gray-100 px-4">
            <div class="w-full max-w-lg rounded-lg bg-white p-8 text-center shadow-md">
                <p class="text-sm font-semibold uppercase tracking-widest text-amber-600">HTTP 429</p>

                <h1 class="mt-4 text-2xl font-bold text-gray-900">Terlalu banyak permintaan</h1>

                <p class="mt-3 text-sm leading-relaxed text-gray-600">
                    Anda mengirim proposal lebih dari <span class="font-semibold">3 kali dalam satu menit</span>.
                    Rate limiter kustom <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs font-semibold">submit-proposal</code>
                    menahan permintaan berlebih demi mencegah spam.
                </p>

                <p class="mt-2 text-sm text-gray-600">
                    Silakan tunggu sekitar satu menit, lalu coba kirim kembali.
                </p>

                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="{{ url()->previous() }}"
                       class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        Kembali
                    </a>
                    <a href="{{ route('home') }}"
                       class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                        Halaman Utama
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
