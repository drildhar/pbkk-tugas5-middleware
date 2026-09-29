<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Tests\TestCase;

/**
 * Menguji custom middleware App\Http\Middleware\CekPeran yang dipakai pada
 * route group /admin melalui alias 'cek.peran:dosen,asdos'.
 */
class CekPeranMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get(route('admin.proposals.index'))
            ->assertRedirect(route('login'));
    }

    public function test_mahasiswa_ditolak_masuk_area_admin(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();

        $this->actingAs($mahasiswa)
            ->get(route('admin.proposals.index'))
            ->assertForbidden();
    }

    public function test_dosen_diizinkan_masuk_area_admin(): void
    {
        $dosen = User::factory()->dosen()->create();

        $this->actingAs($dosen)
            ->get(route('admin.proposals.index'))
            ->assertOk();
    }

    public function test_asdos_diizinkan_masuk_area_admin(): void
    {
        $asdos = User::factory()->asdos()->create();

        $this->actingAs($asdos)
            ->get(route('admin.proposals.index'))
            ->assertOk();
    }

    public function test_middleware_mengembalikan_pesan_403_yang_sesuai(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();

        $this->actingAs($mahasiswa);

        try {
            $this->withoutExceptionHandling()->get(route('admin.proposals.index'));

            $this->fail('Sebuah HttpException 403 seharusnya dilempar.');
        } catch (HttpExceptionInterface $exception) {
            $this->assertSame(403, $exception->getStatusCode());
            $this->assertSame('Anda tidak memiliki hak akses!', $exception->getMessage());
        }
    }
}
