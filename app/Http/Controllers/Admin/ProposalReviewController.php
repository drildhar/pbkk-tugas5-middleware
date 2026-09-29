<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeProposalRequest;
use App\Models\Proposal;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Area dosen & asisten dosen: meninjau proposal dan menginput nilai.
 *
 * Seluruh aksi dilindungi Gate global `kelola-nilai`, dan route-nya juga
 * diproteksi middleware `cek.peran:dosen,asdos` (lihat routes/web.php).
 */
class ProposalReviewController extends Controller
{
    /**
     * Daftar seluruh proposal dengan filter status dan pencarian judul.
     */
    public function index(Request $request): View
    {
        Gate::authorize('kelola-nilai');

        $status = $request->query('status');
        $keyword = $request->query('q');

        $proposals = Proposal::query()
            ->with('user')
            ->withStatus(is_string($status) ? $status : null)
            ->when(
                is_string($keyword) && $keyword !== '',
                fn (Builder $query): Builder => $query->where('judul', 'like', '%'.$keyword.'%'),
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.proposals.index', [
            'proposals' => $proposals,
            'filters' => [
                'status' => is_string($status) ? $status : null,
                'q' => is_string($keyword) ? $keyword : null,
            ],
            'stats' => [
                'total' => Proposal::query()->count(),
                'pending' => Proposal::query()->withStatus(Proposal::STATUS_PENDING)->count(),
                'approved' => Proposal::query()->withStatus(Proposal::STATUS_APPROVED)->count(),
                'rejected' => Proposal::query()->withStatus(Proposal::STATUS_REJECTED)->count(),
            ],
        ]);
    }

    /**
     * Detail proposal beserta form penilaian.
     */
    public function show(Proposal $proposal): View
    {
        Gate::authorize('kelola-nilai');

        return view('admin.proposals.show', [
            'proposal' => $proposal,
        ]);
    }

    /**
     * Simpan hasil peninjauan: status, nilai, dan catatan revisi.
     */
    public function update(GradeProposalRequest $request, Proposal $proposal): RedirectResponse
    {
        Gate::authorize('kelola-nilai');

        $proposal->update($request->validated());

        return redirect()
            ->route('admin.proposals.show', $proposal)
            ->with('status', 'Penilaian proposal berhasil disimpan.');
    }
}
