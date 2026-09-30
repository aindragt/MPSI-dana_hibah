<?php

namespace App\Services;

use App\Models\Proposal;
use App\Models\SubmissionWindow;
use App\Models\User;

class ProposalService
{
    /**
     * Generate proposal number in format: HIBAH-{YEAR}-{UNIX_TIMESTAMP}
     */
    public function generateProposalNumber(int $year): string
    {
        return "HIBAH-{$year}-".time();
    }

    /**
     * Get current active submission window
     */
    public function getActiveWindow(): ?SubmissionWindow
    {
        $today = now()->toDateString();

        return SubmissionWindow::where('is_active', true)
            ->where('open_date', '<=', $today)
            ->where('close_date', '>=', $today)
            ->first();
    }

    /**
     * Check whether the user has an active (non-terminal) proposal in the given year
     */
    public function hasActiveProposalThisYear(User $user, int $year): bool
    {
        return Proposal::where('user_id', $user->id)
            ->whereHas('submissionWindow', function ($query) use ($year) {
                $query->where('year', $year);
            })
            ->whereNotIn('status', ['verifikasi_final', 'ditolak'])
            ->exists();
    }
}
