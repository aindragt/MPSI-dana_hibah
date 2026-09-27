<?php

namespace App\Observers;

use App\Models\Proposal;
use App\Models\ProposalStatusLog;
use Illuminate\Support\Facades\Auth;

class ProposalObserver
{
    /**
     * Handle the Proposal "updated" event.
     */
    public function updated(Proposal $proposal): void
    {
        if ($proposal->isDirty('status')) {
            ProposalStatusLog::create([
                'proposal_id' => $proposal->id,
                'changed_by' => Auth::id(),
                'from_status' => (string) $proposal->getOriginal('status'),
                'to_status' => (string) $proposal->status,
            ]);
        }
    }
}
