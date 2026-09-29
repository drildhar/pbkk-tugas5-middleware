<?php

namespace Tests\Feature;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Payload valid untuk membuat/mengubah proposal.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'judul' => 'Deteksi Objek Sampah Laut Menggunakan YOLOv8',
            'deskripsi' => 'Proyek ini membangun model deteksi objek untuk sampah laut '
                .'memakai arsitektur YOLOv8 dengan dataset citra bawah air yang '
                .'dikumpulkan dan dianotasi secara mandiri oleh tim.',
        ], $overrides);
    }

    public function test_dashboard_menampilkan_ringkasan_proposal_milik_mahasiswa(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();
        Proposal::factory()->for($mahasiswa)->create(['judul' => 'Proposal Dashboard Saya']);

        $this->actingAs($mahasiswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Proposal Dashboard Saya');
    }

    public function test_mahasiswa_dapat_mengajukan_proposal_baru(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();

        $this->actingAs($mahasiswa)
            ->post(route('proposals.store'), $this->validPayload())
            ->assertRedirect();

        $proposal = Proposal::query()->firstOrFail();

        $this->assertSame($mahasiswa->getKey(), $proposal->user_id);
        $this->assertSame('Deteksi Objek Sampah Laut Menggunakan YOLOv8', $proposal->judul);
        $this->assertSame(Proposal::STATUS_PENDING, $proposal->status);
    }

    public function test_mahasiswa_tidak_bisa_menaikkan_status_dan_nilai_sendiri(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();

        $this->actingAs($mahasiswa)->post(route('proposals.store'), $this->validPayload([
            'status' => Proposal::STATUS_APPROVED,
            'nilai' => 100,
            'catatan_revisi' => 'Dinaikkan sendiri.',
        ]));

        $proposal = Proposal::query()->firstOrFail();

        $this->assertSame(Proposal::STATUS_PENDING, $proposal->status);
        $this->assertNull($proposal->nilai);
        $this->assertNull($proposal->catatan_revisi);
    }

    public function test_daftar_proposal_hanya_menampilkan_milik_user_sendiri(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();
        $lain = User::factory()->mahasiswa()->create();

        Proposal::factory()->for($mahasiswa)->create(['judul' => 'Proposal Milik Saya Sendiri']);
        Proposal::factory()->for($lain)->create(['judul' => 'Proposal Milik Orang Lain']);

        $this->actingAs($mahasiswa)
            ->get(route('proposals.index'))
            ->assertOk()
            ->assertSee('Proposal Milik Saya Sendiri')
            ->assertDontSee('Proposal Milik Orang Lain');
    }

    public function test_mahasiswa_lain_tidak_boleh_melihat_detail_proposal(): void
    {
        $pemilik = User::factory()->mahasiswa()->create();
        $lain = User::factory()->mahasiswa()->create();
        $proposal = Proposal::factory()->for($pemilik)->create();

        $this->actingAs($lain)
            ->get(route('proposals.show', $proposal))
            ->assertForbidden();
    }

    public function test_mahasiswa_lain_tidak_boleh_mengubah_atau_menghapus_proposal(): void
    {
        $pemilik = User::factory()->mahasiswa()->create();
        $lain = User::factory()->mahasiswa()->create();
        $proposal = Proposal::factory()->for($pemilik)->create();

        $this->actingAs($lain)->get(route('proposals.edit', $proposal))->assertForbidden();
        $this->actingAs($lain)->put(route('proposals.update', $proposal), $this->validPayload())->assertForbidden();
        $this->actingAs($lain)->delete(route('proposals.destroy', $proposal))->assertForbidden();

        $this->assertDatabaseHas('proposals', ['id' => $proposal->getKey()]);
    }

    public function test_pemilik_dapat_mengubah_dan_menghapus_proposal(): void
    {
        $pemilik = User::factory()->mahasiswa()->create();
        $proposal = Proposal::factory()->for($pemilik)->create();

        $this->actingAs($pemilik)
            ->put(route('proposals.update', $proposal), $this->validPayload([
                'judul' => 'Judul Setelah Diubah',
            ]))
            ->assertRedirect(route('proposals.show', $proposal));

        $this->assertSame('Judul Setelah Diubah', $proposal->fresh()->judul);

        $this->actingAs($pemilik)
            ->delete(route('proposals.destroy', $proposal))
            ->assertRedirect(route('proposals.index'));

        $this->assertDatabaseMissing('proposals', ['id' => $proposal->getKey()]);
    }

    public function test_dosen_dapat_mengedit_proposal_mahasiswa(): void
    {
        $dosen = User::factory()->dosen()->create();
        $proposal = Proposal::factory()->create();

        $this->actingAs($dosen)->get(route('proposals.edit', $proposal))->assertOk();

        $this->actingAs($dosen)
            ->put(route('proposals.update', $proposal), $this->validPayload([
                'judul' => 'Diedit oleh Dosen',
            ]))
            ->assertRedirect(route('proposals.show', $proposal));

        $this->assertSame('Diedit oleh Dosen', $proposal->fresh()->judul);
    }

    public function test_asdos_tidak_dapat_mengedit_proposal_mahasiswa(): void
    {
        $asdos = User::factory()->asdos()->create();
        $proposal = Proposal::factory()->create();

        $this->actingAs($asdos)->get(route('proposals.edit', $proposal))->assertForbidden();
        $this->actingAs($asdos)->put(route('proposals.update', $proposal), $this->validPayload())->assertForbidden();
    }

    public function test_dosen_tidak_dapat_menghapus_proposal_mahasiswa(): void
    {
        $dosen = User::factory()->dosen()->create();
        $proposal = Proposal::factory()->create();

        $this->actingAs($dosen)->delete(route('proposals.destroy', $proposal))->assertForbidden();

        $this->assertDatabaseHas('proposals', ['id' => $proposal->getKey()]);
    }

    public function test_judul_dan_deskripsi_divalidasi(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();

        $this->actingAs($mahasiswa)
            ->post(route('proposals.store'), ['judul' => 'AI', 'deskripsi' => 'terlalu pendek'])
            ->assertSessionHasErrors(['judul', 'deskripsi']);

        $this->assertDatabaseCount('proposals', 0);
    }

    public function test_tamu_tidak_dapat_mengakses_area_mahasiswa(): void
    {
        $this->get(route('proposals.index'))->assertRedirect(route('login'));
        $this->get(route('proposals.create'))->assertRedirect(route('login'));
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_halaman_publik_dapat_diakses_tanpa_login(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Secure Academic Portal');
    }
}
