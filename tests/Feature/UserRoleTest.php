<?php

namespace Tests\Feature;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_default_adalah_mahasiswa(): void
    {
        $user = User::query()->create([
            'name' => 'Pengguna Baru',
            'email' => 'pengguna.baru@its.ac.id',
            'password' => 'password',
        ]);

        $this->assertSame(User::ROLE_MAHASISWA, $user->fresh()->role);
    }

    public function test_registrasi_publik_tidak_bisa_menaikkan_peran_menjadi_dosen(): void
    {
        $this->post(route('register'), [
            'name' => 'Pendaftar Nakal',
            'email' => 'pendaftar@its.ac.id',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::ROLE_DOSEN,
        ])->assertRedirect(route('dashboard'));

        $user = User::query()->where('email', 'pendaftar@its.ac.id')->firstOrFail();

        $this->assertSame(User::ROLE_MAHASISWA, $user->role);
    }

    public function test_helper_peran_bekerja_sesuai_nilainya(): void
    {
        $dosen = User::factory()->dosen()->create();
        $asdos = User::factory()->asdos()->create();
        $mahasiswa = User::factory()->mahasiswa()->create();

        $this->assertTrue($dosen->isDosen());
        $this->assertTrue($dosen->isPengelola());
        $this->assertFalse($dosen->isAsdos());

        $this->assertTrue($asdos->isAsdos());
        $this->assertTrue($asdos->isPengelola());

        $this->assertTrue($mahasiswa->isMahasiswa());
        $this->assertFalse($mahasiswa->isPengelola());
    }

    public function test_relasi_user_memiliki_banyak_proposal(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();
        Proposal::factory()->count(2)->for($mahasiswa)->create();

        $this->assertCount(2, $mahasiswa->proposals);
        $this->assertTrue($mahasiswa->proposals->first()->user->is($mahasiswa));
    }

    public function test_menghapus_user_ikut_menghapus_proposalnya(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();
        Proposal::factory()->for($mahasiswa)->create();

        $mahasiswa->delete();

        $this->assertDatabaseCount('proposals', 0);
    }
}
