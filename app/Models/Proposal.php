<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proposal extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /**
     * Seluruh status proposal yang valid.
     *
     * @var list<string>
     */
    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
    ];

    /**
     * Label status yang ramah untuk ditampilkan di UI.
     *
     * @var array<string, string>
     */
    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Menunggu Peninjauan',
        self::STATUS_APPROVED => 'Disetujui',
        self::STATUS_REJECTED => 'Ditolak / Perlu Revisi',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * Catatan keamanan: `status`, `nilai`, dan `catatan_revisi` hanya boleh
     * diubah oleh dosen/asdos. Controller mahasiswa memakai Form Request yang
     * hanya mengembalikan `judul` dan `deskripsi`, sehingga mahasiswa tidak bisa
     * menaikkan nilainya sendiri lewat mass assignment.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'status',
        'nilai',
        'catatan_revisi',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
        ];
    }

    /**
     * Mahasiswa pemilik proposal.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Label status untuk ditampilkan di UI.
     */
    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst((string) $this->status);
    }

    /**
     * Batasi query ke proposal milik user tertentu.
     *
     * @param  Builder<Proposal>  $query
     * @return Builder<Proposal>
     */
    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->getKey());
    }

    /**
     * Filter query berdasarkan status (abaikan nilai yang tidak valid).
     *
     * @param  Builder<Proposal>  $query
     * @return Builder<Proposal>
     */
    public function scopeWithStatus(Builder $query, ?string $status): Builder
    {
        return $query->when(
            $status !== null && in_array($status, self::STATUSES, true),
            fn (Builder $builder): Builder => $builder->where('status', $status),
        );
    }
}
