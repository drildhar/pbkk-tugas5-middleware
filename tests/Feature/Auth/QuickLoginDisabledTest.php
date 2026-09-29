<?php

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Memastikan route bypass autentikasi TIDAK tersedia di luar lingkungan lokal,
 * sehingga tidak menjadi celah keamanan saat aplikasi dipasang di production.
 */
class QuickLoginDisabledTest extends TestCase
{
    public function test_route_quick_login_tidak_terdaftar_di_luar_lokal(): void
    {
        $this->assertFalse($this->app->environment('local'));
        $this->assertFalse(Route::has('login.as'));
    }

    public function test_endpoint_quick_login_mengembalikan_404_di_luar_lokal(): void
    {
        $this->get('/login/as/dosen')->assertNotFound();
    }
}
