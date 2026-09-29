<?php

namespace Database\Factories;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Proposal>
 */
class ProposalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Proposal>
     */
    protected $model = Proposal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->mahasiswa(),
            'judul' => 'Proyek AI: '.fake()->unique()->sentence(4),
            'deskripsi' => fake()->paragraphs(2, true),
            'status' => Proposal::STATUS_PENDING,
            'nilai' => null,
            'catatan_revisi' => null,
        ];
    }

    /**
     * Proposal yang sudah disetujui dan diberi nilai.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Proposal::STATUS_APPROVED,
            'nilai' => fake()->numberBetween(75, 100),
            'catatan_revisi' => null,
        ]);
    }

    /**
     * Proposal yang ditolak dan perlu revisi.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Proposal::STATUS_REJECTED,
            'nilai' => null,
            'catatan_revisi' => fake()->sentence(12),
        ]);
    }
}
