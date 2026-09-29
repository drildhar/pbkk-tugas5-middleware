<?php

namespace Tests\Feature\Authorization;

use App\Models\Proposal;
use App\Models\User;
use App\Policies\ProposalPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * Menguji aturan bisnis ProposalPolicy.
 *
 * - update : pemilik proposal ATAU dosen (asdos tidak).
 * - delete : hanya pemilik proposal.
 * - view   : pemilik, dosen, dan asdos (peninjau).
 */
class ProposalPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_policy_ditemukan_otomatis_oleh_laravel(): void
    {
        $this->assertInstanceOf(
            ProposalPolicy::class,
            Gate::getPolicyFor(Proposal::class),
        );
    }

    public function test_update_diizinkan_untuk_pemilik_dan_dosen(): void
    {
        $pemilik = User::factory()->mahasiswa()->create();
        $dosen = User::factory()->dosen()->create();
        $proposal = Proposal::factory()->for($pemilik)->create();

        $this->assertTrue(Gate::forUser($pemilik)->allows('update', $proposal));
        $this->assertTrue(Gate::forUser($dosen)->allows('update', $proposal));
    }

    public function test_update_ditolak_untuk_mahasiswa_lain_dan_asdos(): void
    {
        $pemilik = User::factory()->mahasiswa()->create();
        $mahasiswaLain = User::factory()->mahasiswa()->create();
        $asdos = User::factory()->asdos()->create();
        $proposal = Proposal::factory()->for($pemilik)->create();

        $this->assertFalse(Gate::forUser($mahasiswaLain)->allows('update', $proposal));
        $this->assertFalse(Gate::forUser($asdos)->allows('update', $proposal));
    }

    public function test_delete_hanya_diizinkan_untuk_pemilik(): void
    {
        $pemilik = User::factory()->mahasiswa()->create();
        $mahasiswaLain = User::factory()->mahasiswa()->create();
        $dosen = User::factory()->dosen()->create();
        $asdos = User::factory()->asdos()->create();
        $proposal = Proposal::factory()->for($pemilik)->create();

        $this->assertTrue(Gate::forUser($pemilik)->allows('delete', $proposal));
        $this->assertFalse(Gate::forUser($mahasiswaLain)->allows('delete', $proposal));
        $this->assertFalse(Gate::forUser($dosen)->allows('delete', $proposal));
        $this->assertFalse(Gate::forUser($asdos)->allows('delete', $proposal));
    }

    public function test_view_diizinkan_untuk_pemilik_dan_peninjau(): void
    {
        $pemilik = User::factory()->mahasiswa()->create();
        $dosen = User::factory()->dosen()->create();
        $asdos = User::factory()->asdos()->create();
        $proposal = Proposal::factory()->for($pemilik)->create();

        $this->assertTrue(Gate::forUser($pemilik)->allows('view', $proposal));
        $this->assertTrue(Gate::forUser($dosen)->allows('view', $proposal));
        $this->assertTrue(Gate::forUser($asdos)->allows('view', $proposal));
    }

    public function test_view_ditolak_untuk_mahasiswa_lain(): void
    {
        $proposal = Proposal::factory()->create();
        $mahasiswaLain = User::factory()->mahasiswa()->create();

        $this->assertFalse(Gate::forUser($mahasiswaLain)->allows('view', $proposal));
    }

    public function test_semua_user_terautentikasi_boleh_membuat_proposal(): void
    {
        $this->assertTrue(Gate::forUser(User::factory()->mahasiswa()->create())->allows('create', Proposal::class));
        $this->assertTrue(Gate::forUser(User::factory()->dosen()->create())->allows('create', Proposal::class));
    }
}
