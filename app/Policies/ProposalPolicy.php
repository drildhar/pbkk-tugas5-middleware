<?php

namespace App\Policies;

use App\Models\Proposal;
use App\Models\User;

/**
 * Aturan otorisasi tingkat model untuk Proposal.
 *
 * Laravel 11 menemukan policy ini secara otomatis melalui konvensi
 * `App\Models\{Model}` -> `App\Policies\{Model}Policy`.
 */
class ProposalPolicy
{
    /**
     * Bolehkah user melihat daftar proposal?
     *
     * Semua user yang sudah login boleh membuka daftar; isi daftarnya sendiri
     * sudah dibatasi di controller (mahasiswa hanya melihat proposal miliknya).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Bolehkah user melihat detail proposal ini?
     *
     * Pemilik, dosen, dan asisten dosen (peninjau) diizinkan.
     */
    public function view(User $user, Proposal $proposal): bool
    {
        return $user->getKey() === $proposal->user_id || $user->isPengelola();
    }

    /**
     * Bolehkah user membuat proposal baru?
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Bolehkah user mengubah proposal ini?
     *
     * Pemilik proposal boleh mengedit miliknya sendiri. Pengecualian: dosen
     * berhak mengedit proposal siapa pun (misalnya untuk menetapkan status
     * revisi), sedangkan asdos tidak.
     */
    public function update(User $user, Proposal $proposal): bool
    {
        return $user->isDosen() || $user->getKey() === $proposal->user_id;
    }

    /**
     * Bolehkah user menghapus proposal ini?
     *
     * Hanya mahasiswa yang merupakan pemilik proposal.
     */
    public function delete(User $user, Proposal $proposal): bool
    {
        return $user->getKey() === $proposal->user_id;
    }
}
