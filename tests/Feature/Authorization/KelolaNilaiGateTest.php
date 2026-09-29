<?php

namespace Tests\Feature\Authorization;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * Menguji Gate global `kelola-nilai` yang didaftarkan pada AppServiceProvider.
 */
class KelolaNilaiGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_dan_asdos_diizinkan_mengelola_nilai(): void
    {
        $dosen = User::factory()->dosen()->create();
        $asdos = User::factory()->asdos()->create();

        $this->assertTrue(Gate::forUser($dosen)->allows('kelola-nilai'));
        $this->assertTrue(Gate::forUser($asdos)->allows('kelola-nilai'));
    }

    public function test_mahasiswa_tidak_diizinkan_mengelola_nilai(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();

        $this->assertFalse(Gate::forUser($mahasiswa)->allows('kelola-nilai'));
    }

    public function test_tamu_tidak_diizinkan_mengelola_nilai(): void
    {
        $this->assertFalse(Gate::allows('kelola-nilai'));
    }
}
