<?php

namespace App\Policies;

use App\Models\Proposal;
use App\Models\SubmissionWindow;
use App\Models\User;

class ProposalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->slug, ['pengaju', 'admin-kesra']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Proposal $proposal): bool
    {
        return $user->role->slug === 'admin-kesra'
            || $user->id === $proposal->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->role->slug !== 'pengaju') {
            return false;
        }

        $today = now()->toDateString();
        $activeWindow = SubmissionWindow::where('is_active', true)
            ->where('open_date', '<=', $today)
            ->where('close_date', '>=', $today)
            ->first();

        if (! $activeWindow) {
            return false;
        }

        $hasActiveProposal = Proposal::where('user_id', $user->id)
            ->where('submission_window_id', $activeWindow->id)
            ->whereNotIn('status', ['verifikasi_final', 'ditolak'])
            ->exists();

        return ! $hasActiveProposal;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Proposal $proposal): bool
    {
        return $user->id === $proposal->user_id
            && $proposal->status->isEditable();
    }

    /**
     * Determine whether the user can submit the model.
     */
    public function submit(User $user, Proposal $proposal): bool
    {
        if ($user->id !== $proposal->user_id) {
            return false;
        }

        if (! $proposal->status->isEditable()) {
            return false;
        }

        // Must have all 11 document types uploaded (latest versions)
        $uploadedTypeIds = $proposal->documents()
            ->select('document_type_id')
            ->distinct()
            ->pluck('document_type_id');

        return $uploadedTypeIds->count() >= 11;
    }
}
