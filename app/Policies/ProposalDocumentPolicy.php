<?php

namespace App\Policies;

use App\Models\ProposalDocument;
use App\Models\User;

class ProposalDocumentPolicy
{
    /**
     * Determine whether the user can download the proposal document.
     */
    public function download(User $user, ProposalDocument $document): bool
    {
        return $user->role->slug === 'admin-kesra'
            || $user->id === $document->proposal->user_id;
    }

    /**
     * Determine whether the user can upload a new version of the proposal document.
     */
    public function upload(User $user, ProposalDocument $document): bool
    {
        return $user->id === $document->proposal->user_id
            && $document->proposal->status->isEditable();
    }
}
