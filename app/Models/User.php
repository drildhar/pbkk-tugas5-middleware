<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** Mahasiswa: pemilik dan pengelola proposal miliknya sendiri. */
    public const ROLE_MAHASISWA = 'mahasiswa';

    /** Dosen: pengampu mata kuliah sekaligus pengelola nilai. */
    public const ROLE_DOSEN = 'dosen';

    /** Asisten dosen: membantu peninjauan proposal dan pengelola nilai. */
    public const ROLE_ASDOS = 'asdos';

    /**
     * Seluruh peran yang valid.
     *
     * @var list<string>
     */
    public const ROLES = [
        self::ROLE_MAHASISWA,
        self::ROLE_DOSEN,
        self::ROLE_ASDOS,
    ];

    /**
     * Peran yang diizinkan masuk ke area `/admin`.
     *
     * @var list<string>
     */
    public const ROLES_PENGELOLA = [
        self::ROLE_DOSEN,
        self::ROLE_ASDOS,
    ];

    /**
     * Label peran yang ramah untuk ditampilkan di UI.
     *
     * @var array<string, string>
     */
    public const ROLE_LABELS = [
        self::ROLE_MAHASISWA => 'Mahasiswa',
        self::ROLE_DOSEN => 'Dosen',
        self::ROLE_ASDOS => 'Asisten Dosen',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * Catatan: `role` sengaja boleh diisi massal agar seeder/factory ringkas.
     * Pendaftaran publik (Breeze) tidak pernah meneruskan input request mentah,
     * sehingga user tidak bisa menaikkan perannya sendiri menjadi dosen.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Proposal yang dimiliki user ini.
     *
     * @return HasMany<Proposal, $this>
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * Apakah user memiliki peran yang diberikan.
     */
    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isMahasiswa(): bool
    {
        return $this->hasRole(self::ROLE_MAHASISWA);
    }

    public function isDosen(): bool
    {
        return $this->hasRole(self::ROLE_DOSEN);
    }

    public function isAsdos(): bool
    {
        return $this->hasRole(self::ROLE_ASDOS);
    }

    /**
     * Dosen dan asisten dosen berbagi akses ke area `/admin`.
     */
    public function isPengelola(): bool
    {
        return $this->hasRole(...self::ROLES_PENGELOLA);
    }

    /**
     * Label peran untuk ditampilkan di UI.
     */
    public function roleLabel(): string
    {
        return self::ROLE_LABELS[$this->role] ?? ucfirst((string) $this->role);
    }
}
