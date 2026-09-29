<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Nama rate limiter kustom untuk pengiriman proposal.
     */
    public const SUBMIT_PROPOSAL_LIMITER = 'submit-proposal';

    /**
     * Jumlah maksimal pengiriman proposal per menit (per user).
     */
    public const SUBMIT_PROPOSAL_MAX_ATTEMPTS = 3;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerGates();
        $this->registerRateLimiters();
    }

    /**
     * Gate global: hanya dosen dan asisten dosen yang boleh mengelola nilai.
     */
    protected function registerGates(): void
    {
        Gate::define('kelola-nilai', static function (User $user): bool {
            return $user->isPengelola();
        });
    }

    /**
     * Rate limiter kustom (anti-spam submit proposal).
     *
     * Ekuivalen dengan `throttle:3,1`, namun dihitung per user yang login
     * dan diberi nama agar mudah dipakai ulang pada route.
     */
    protected function registerRateLimiters(): void
    {
        RateLimiter::for(self::SUBMIT_PROPOSAL_LIMITER, static function (Request $request): Limit {
            $key = $request->user()?->getAuthIdentifier() ?? $request->ip();

            return Limit::perMinute(self::SUBMIT_PROPOSAL_MAX_ATTEMPTS)
                ->by((string) $key);
        });
    }
}
