<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProposalRequest;
use App\Http\Requests\UpdateProposalRequest;
use App\Models\Proposal;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Area mahasiswa: CRUD proposal proyek kelompok AI milik sendiri.
 *
 * Setiap aksi sensitif dilindungi oleh ProposalPolicy melalui `Gate::authorize()`.
 */
class ProposalController extends Controller
{
    /**
     * Tampilkan daftar proposal milik user yang sedang login.
     */
    public function index(Request $request): View
    {
        $proposals = Proposal::query()
            ->ownedBy($request->user())
            ->latest()
            ->paginate(10);

        return view('proposals.index', [
            'proposals' => $proposals,
        ]);
    }

    /**
     * Tampilkan form pengajuan proposal baru.
     */
    public function create(): View
    {
        Gate::authorize('create', Proposal::class);

        return view('proposals.create');
    }

    /**
     * Simpan proposal baru.
     *
     * Route POST ini diproteksi rate limiter `submit-proposal` (3x per menit).
     */
    public function store(StoreProposalRequest $request): RedirectResponse
    {
        Gate::authorize('create', Proposal::class);

        $proposal = $request->user()->proposals()->create([
            ...$request->validated(),
            'status' => Proposal::STATUS_PENDING,
        ]);

        return redirect()
            ->route('proposals.show', $proposal)
            ->with('status', 'Proposal berhasil dikirim dan sedang menunggu peninjauan dosen.');
    }

    /**
     * Tampilkan detail satu proposal.
     */
    public function show(Proposal $proposal): View
    {
        Gate::authorize('view', $proposal);

        return view('proposals.show', [
            'proposal' => $proposal,
        ]);
    }

    /**
     * Tampilkan form edit proposal.
     */
    public function edit(Proposal $proposal): View
    {
        Gate::authorize('update', $proposal);

        return view('proposals.edit', [
            'proposal' => $proposal,
        ]);
    }

    /**
     * Simpan perubahan proposal.
     */
    public function update(UpdateProposalRequest $request, Proposal $proposal): RedirectResponse
    {
        Gate::authorize('update', $proposal);

        $proposal->update($request->validated());

        return redirect()
            ->route('proposals.show', $proposal)
            ->with('status', 'Perubahan proposal berhasil disimpan.');
    }

    /**
     * Hapus proposal.
     */
    public function destroy(Proposal $proposal): RedirectResponse
    {
        Gate::authorize('delete', $proposal);

        $proposal->delete();

        return redirect()
            ->route('proposals.index')
            ->with('status', 'Proposal berhasil dihapus.');
    }
}
