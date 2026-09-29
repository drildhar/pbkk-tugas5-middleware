<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="mt-1 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="mt-4 block">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="mt-4 flex items-center justify-end">
            @if (Route::has('password.request'))
                <a class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    {{--
        Autentikasi bypass lokal: hanya tampil saat APP_ENV=local agar penguji
        bisa masuk sekali klik tanpa mengetik kredensial.
    --}}
    @if (app()->environment('local') && Route::has('login.as'))
        <div class="mt-6 border-t border-gray-200 pt-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                Quick Login — mode lokal
            </p>

            <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-3">
                <a href="{{ route('login.as', 'dosen') }}"
                   class="inline-flex items-center justify-center rounded-md border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">
                    Dosen
                </a>
                <a href="{{ route('login.as', 'asdos') }}"
                   class="inline-flex items-center justify-center rounded-md border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 transition hover:bg-sky-100">
                    Asdos
                </a>
                <a href="{{ route('login.as', 'mahasiswa') }}"
                   class="inline-flex items-center justify-center rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-100">
                    Mahasiswa
                </a>
            </div>

            <p class="mt-2 text-xs text-gray-400">
                Tombol ini hanya muncul saat <code>APP_ENV=local</code>. Login mahasiswa memakai akun
                <span class="font-semibold">mhs1@its.ac.id</span> (pemilik contoh proposal).
            </p>
        </div>
    @endif
</x-guest-layout>
