<?php

use App\Http\Controllers\Admin\ProposalReviewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProposalController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Area Publik
|--------------------------------------------------------------------------
| Landing page "Secure Academic Portal ITS" — dapat diakses guest.
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Area Mahasiswa (hanya user terautentikasi)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/proposals', [ProposalController::class, 'index'])->name('proposals.index');
    Route::get('/proposals/create', [ProposalController::class, 'create'])->name('proposals.create');

    // Rate limiter kustom: maksimal 3x submit per menit per user.
    Route::post('/proposals', [ProposalController::class, 'store'])
        ->middleware('throttle:submit-proposal')
        ->name('proposals.store');

    Route::get('/proposals/{proposal}', [ProposalController::class, 'show'])->name('proposals.show');
    Route::get('/proposals/{proposal}/edit', [ProposalController::class, 'edit'])->name('proposals.edit');
    Route::put('/proposals/{proposal}', [ProposalController::class, 'update'])->name('proposals.update');
    Route::delete('/proposals/{proposal}', [ProposalController::class, 'destroy'])->name('proposals.destroy');

    // Profil bawaan Breeze.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Area Dosen & Asisten Dosen
|--------------------------------------------------------------------------
| Diproteksi dua lapis: `auth` (harus login) + `cek.peran:dosen,asdos`
| (custom middleware CekPeran yang mengembalikan 403 bila peran tidak cocok).
*/
Route::middleware(['auth', 'cek.peran:dosen,asdos'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [ProposalReviewController::class, 'index'])->name('dashboard');

        Route::get('/proposals', [ProposalReviewController::class, 'index'])->name('proposals.index');
        Route::get('/proposals/{proposal}', [ProposalReviewController::class, 'show'])->name('proposals.show');
        Route::put('/proposals/{proposal}', [ProposalReviewController::class, 'update'])->name('proposals.update');
    });

/*
|--------------------------------------------------------------------------
| Autentikasi Bypass Lokal (Quick Login untuk pengujian)
|--------------------------------------------------------------------------
| Hanya aktif saat APP_ENV=local. Memudahkan penguji masuk sebagai dosen,
| asdos, atau mahasiswa tanpa mengetik kredensial.
*/
if (app()->environment('local')) {
    Route::get('/login/as/{role}', function (string $role) {
        if (! in_array($role, User::ROLES, true)) {
            abort(404);
        }

        $user = User::query()->where('role', $role)->orderBy('id')->first();

        if ($user === null) {
            return 'Akun uji coba untuk role ini tidak ditemukan!';
        }

        Auth::login($user);
        request()->session()->regenerate();

        return redirect()->route('dashboard');
    })->name('login.as');
}

require __DIR__.'/auth.php';
