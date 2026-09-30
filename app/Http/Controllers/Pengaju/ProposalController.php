<?php

namespace App\Http\Controllers\Pengaju;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengaju\StoreProposalRequest;
use App\Http\Requests\Pengaju\UpdateProposalRequest;
use App\Models\DocumentType;
use App\Models\Proposal;
use App\Services\ProposalService;
use App\States\ProposalStatus\Draft;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProposalController extends Controller
{
    public function __construct(
        protected ProposalService $proposalService
    ) {}

    /**
     * Display a listing of proposals owned by the logged-in user.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Proposal::class);

        $proposals = Proposal::with(['submissionWindow', 'documents.documentType'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return Inertia::render('Pengaju/Proposals/Index', [
            'proposals' => $proposals,
        ]);
    }

    /**
     * Show the form for creating a new proposal.
     */
    public function create(): Response
    {
        $this->authorize('create', Proposal::class);

        $activeWindow = $this->proposalService->getActiveWindow();

        return Inertia::render('Pengaju/Proposals/Create', [
            'activeWindow' => $activeWindow,
        ]);
    }

    /**
     * Store a newly created proposal in storage.
     */
    public function store(StoreProposalRequest $request): RedirectResponse
    {
        $this->authorize('create', Proposal::class);

        $activeWindow = $this->proposalService->getActiveWindow();

        if (! $activeWindow) {
            return redirect()->back()->withErrors([
                'error' => 'Tidak ada jendela pengajuan aktif saat ini.',
            ]);
        }

        $validated = $request->validated();

        $proposalNumber = $this->proposalService->generateProposalNumber($activeWindow->year);

        $proposal = Proposal::create([
            'proposal_number' => $proposalNumber,
            'user_id' => $request->user()->id,
            'submission_window_id' => $activeWindow->id,
            'activity_title' => $validated['activity_title'],
            'activity_description' => $validated['activity_description'] ?? null,
            'total_budget' => $validated['total_budget'],
            'execution_start_date' => $validated['execution_start_date'] ?? null,
            'execution_end_date' => $validated['execution_end_date'] ?? null,
            'status' => Draft::class,
        ]);

        return redirect()->route('pengaju.proposals.index')
            ->with('success', 'Proposal berhasil dibuat dengan status Draft.');
    }

    /**
     * Display the specified proposal along with documents checklist.
     */
    public function show(Proposal $proposal): Response
    {
        $this->authorize('view', $proposal);

        $proposal->load([
            'submissionWindow',
            'documents.documentType',
            'documents.verifications',
            'revisionNotes.creator',
            'statusLogs.user',
        ]);

        $documentTypes = DocumentType::orderBy('sort_order')->get();

        return Inertia::render('Pengaju/Proposals/Show', [
            'proposal' => $proposal,
            'documentTypes' => $documentTypes,
        ]);
    }

    /**
     * Show the form for editing the specified proposal.
     */
    public function edit(Proposal $proposal): Response
    {
        $this->authorize('update', $proposal);

        return Inertia::render('Pengaju/Proposals/Edit', [
            'proposal' => $proposal,
        ]);
    }

    /**
     * Update the specified proposal in storage.
     */
    public function update(UpdateProposalRequest $request, Proposal $proposal): RedirectResponse
    {
        $this->authorize('update', $proposal);

        $validated = $request->validated();

        $proposal->update([
            'activity_title' => $validated['activity_title'],
            'activity_description' => $validated['activity_description'] ?? null,
            'total_budget' => $validated['total_budget'],
            'execution_start_date' => $validated['execution_start_date'] ?? null,
            'execution_end_date' => $validated['execution_end_date'] ?? null,
        ]);

        return redirect()->route('pengaju.proposals.show', $proposal->id)
            ->with('success', 'Proposal berhasil diperbarui.');
    }
}
