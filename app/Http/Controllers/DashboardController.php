<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard yang menyesuaikan diri dengan peran user.
     *
     * - Mahasiswa : ringkasan proposal miliknya sendiri.
     * - Dosen/Asdos: ringkasan seluruh proposal yang masuk untuk ditinjau.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $query = $user->isMahasiswa()
            ? Proposal::query()->ownedBy($user)
            : Proposal::query();

        return view('dashboard', [
            'user' => $user,
            'proposals' => (clone $query)
                ->with('user')
                ->latest()
                ->limit(5)
                ->get(),
            'stats' => $this->buildStats($query),
        ]);
    }

    /**
     * Hitung ringkasan jumlah proposal per status.
     *
     * @param  Builder<Proposal>  $query
     * @return array<string, int>
     */
    protected function buildStats(Builder $query): array
    {
        return [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->withStatus(Proposal::STATUS_PENDING)->count(),
            'approved' => (clone $query)->withStatus(Proposal::STATUS_APPROVED)->count(),
            'rejected' => (clone $query)->withStatus(Proposal::STATUS_REJECTED)->count(),
        ];
    }
}
