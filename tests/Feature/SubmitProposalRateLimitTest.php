<?php

namespace Tests\Feature;

use App\Models\Proposal;
use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * Tantangan ekstra: rate limiting kustom `submit-proposal` (3x per menit).
 */
class SubmitProposalRateLimitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function payload(int $index): array
    {
        return [
            'judul' => "Proposal Uji Rate Limit Nomor {$index}",
            'deskripsi' => 'Deskripsi proposal untuk menguji batas pengiriman '
                .'proposal agar tidak terjadi spam dari satu akun mahasiswa.',
        ];
    }

    public function test_limiter_kustom_terdaftar_pada_rate_limiter(): void
    {
        $this->assertNotNull(
            RateLimiter::limiter(AppServiceProvider::SUBMIT_PROPOSAL_LIMITER),
        );
    }

    public function test_pengiriman_proposal_dibatasi_tiga_kali_per_menit(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $this->actingAs($mahasiswa)
                ->post(route('proposals.store'), $this->payload($attempt))
                ->assertRedirect();
        }

        $this->assertSame(3, Proposal::query()->count());

        $this->actingAs($mahasiswa)
            ->post(route('proposals.store'), $this->payload(4))
            ->assertStatus(429)
            ->assertSee('Terlalu banyak permintaan');

        $this->assertSame(3, Proposal::query()->count());
    }

    public function test_batas_dihitung_per_user_bukan_global(): void
    {
        $mahasiswaA = User::factory()->mahasiswa()->create();
        $mahasiswaB = User::factory()->mahasiswa()->create();

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $this->actingAs($mahasiswaA)
                ->post(route('proposals.store'), $this->payload($attempt))
                ->assertRedirect();
        }

        $this->actingAs($mahasiswaA)
            ->post(route('proposals.store'), $this->payload(4))
            ->assertStatus(429);

        // User lain tetap punya kuota sendiri.
        $this->actingAs($mahasiswaB)
            ->post(route('proposals.store'), $this->payload(5))
            ->assertRedirect();

        $this->assertSame(4, Proposal::query()->count());
    }
}
