<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Tantangan ekstra: autentikasi bypass lokal (/login/as/{role}).
 *
 * Route ini hanya didaftarkan saat APP_ENV=local, sementara phpunit.xml
 * memaksa APP_ENV=testing. Kelas test ini menaikkan env menjadi `local`
 * sebelum aplikasi di-boot agar jalur lokal tersebut benar-benar teruji.
 */
class QuickLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        $this->forceEnvironment('local');

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->forceEnvironment('testing');
    }

    private function forceEnvironment(string $environment): void
    {
        putenv("APP_ENV={$environment}");
        $_ENV['APP_ENV'] = $environment;
        $_SERVER['APP_ENV'] = $environment;
    }

    public function test_route_quick_login_terdaftar_di_lingkungan_lokal(): void
    {
        $this->assertTrue($this->app->environment('local'));
        $this->assertTrue(Route::has('login.as'));
    }

    public function test_quick_login_sebagai_dosen(): void
    {
        $dosen = User::factory()->dosen()->create();

        $this->get(route('login.as', 'dosen'))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($dosen);
    }

    public function test_quick_login_sebagai_asdos(): void
    {
        $asdos = User::factory()->asdos()->create();

        $this->get(route('login.as', 'asdos'))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($asdos);
    }

    public function test_quick_login_memilih_akun_pertama_dari_peran_yang_diminta(): void
    {
        $mahasiswaPertama = User::factory()->mahasiswa()->create();
        User::factory()->mahasiswa()->create();

        $this->get(route('login.as', 'mahasiswa'))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($mahasiswaPertama);
    }

    public function test_quick_login_menolak_peran_yang_tidak_dikenal(): void
    {
        $this->get('/login/as/operator')->assertNotFound();
    }

    public function test_quick_login_memberi_pesan_bila_belum_ada_akun_untuk_peran_tersebut(): void
    {
        $this->get(route('login.as', 'dosen'))
            ->assertOk()
            ->assertSee('Akun uji coba untuk role ini tidak ditemukan!');
    }
}
