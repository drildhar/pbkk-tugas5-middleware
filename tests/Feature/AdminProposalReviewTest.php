<?php

namespace Tests\Feature;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProposalReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_tidak_dapat_membuka_area_admin(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();
        $proposal = Proposal::factory()->for($mahasiswa)->create();

        $this->actingAs($mahasiswa)->get(route('admin.proposals.index'))->assertForbidden();
        $this->actingAs($mahasiswa)->get(route('admin.proposals.show', $proposal))->assertForbidden();
    }

    public function test_dosen_dan_asdos_dapat_membuka_area_admin(): void
    {
        $dosen = User::factory()->dosen()->create();
        $asdos = User::factory()->asdos()->create();

        $this->actingAs($dosen)->get(route('admin.proposals.index'))->assertOk();
        $this->actingAs($asdos)->get(route('admin.proposals.index'))->assertOk();
    }

    public function test_dosen_dapat_menyetujui_proposal_dan_memberi_nilai(): void
    {
        $dosen = User::factory()->dosen()->create();
        $proposal = Proposal::factory()->create();

        $this->actingAs($dosen)
            ->put(route('admin.proposals.update', $proposal), [
                'status' => Proposal::STATUS_APPROVED,
                'nilai' => 92,
                'catatan_revisi' => null,
            ])
            ->assertRedirect(route('admin.proposals.show', $proposal));

        $proposal->refresh();

        $this->assertSame(Proposal::STATUS_APPROVED, $proposal->status);
        $this->assertSame(92, $proposal->nilai);
    }

    public function test_asdos_dapat_menolak_proposal_dengan_catatan_revisi(): void
    {
        $asdos = User::factory()->asdos()->create();
        $proposal = Proposal::factory()->create();

        $this->actingAs($asdos)
            ->put(route('admin.proposals.update', $proposal), [
                'status' => Proposal::STATUS_REJECTED,
                'catatan_revisi' => 'Perjelas sumber dataset dan persempit batasan masalah.',
            ])
            ->assertRedirect(route('admin.proposals.show', $proposal));

        $proposal->refresh();

        $this->assertSame(Proposal::STATUS_REJECTED, $proposal->status);
        $this->assertNotNull($proposal->catatan_revisi);
    }

    public function test_status_disetujui_wajib_menyertakan_nilai(): void
    {
        $dosen = User::factory()->dosen()->create();
        $proposal = Proposal::factory()->create();

        $this->actingAs($dosen)
            ->put(route('admin.proposals.update', $proposal), [
                'status' => Proposal::STATUS_APPROVED,
                'nilai' => null,
            ])
            ->assertSessionHasErrors('nilai');
    }

    public function test_status_ditolak_wajib_menyertakan_catatan_revisi(): void
    {
        $dosen = User::factory()->dosen()->create();
        $proposal = Proposal::factory()->create();

        $this->actingAs($dosen)
            ->put(route('admin.proposals.update', $proposal), [
                'status' => Proposal::STATUS_REJECTED,
            ])
            ->assertSessionHasErrors('catatan_revisi');
    }

    public function test_nilai_harus_berada_dalam_rentang_0_sampai_100(): void
    {
        $dosen = User::factory()->dosen()->create();
        $proposal = Proposal::factory()->create();

        $this->actingAs($dosen)
            ->put(route('admin.proposals.update', $proposal), [
                'status' => Proposal::STATUS_APPROVED,
                'nilai' => 150,
            ])
            ->assertSessionHasErrors('nilai');
    }

    public function test_mahasiswa_tidak_dapat_mengirim_penilaian(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();
        $proposal = Proposal::factory()->for($mahasiswa)->create();

        $this->actingAs($mahasiswa)
            ->put(route('admin.proposals.update', $proposal), [
                'status' => Proposal::STATUS_APPROVED,
                'nilai' => 100,
            ])
            ->assertForbidden();

        $this->assertSame(Proposal::STATUS_PENDING, $proposal->fresh()->status);
    }

    public function test_filter_status_dan_pencarian_judul_bekerja(): void
    {
        $dosen = User::factory()->dosen()->create();

        Proposal::factory()->approved()->create(['judul' => 'Proposal Alpha Disetujui']);
        Proposal::factory()->create(['judul' => 'Proposal Beta Menunggu']);

        $this->actingAs($dosen)
            ->get(route('admin.proposals.index', ['status' => Proposal::STATUS_APPROVED]))
            ->assertOk()
            ->assertSee('Proposal Alpha Disetujui')
            ->assertDontSee('Proposal Beta Menunggu');

        $this->actingAs($dosen)
            ->get(route('admin.proposals.index', ['q' => 'Beta']))
            ->assertOk()
            ->assertSee('Proposal Beta Menunggu')
            ->assertDontSee('Proposal Alpha Disetujui');
    }
}
