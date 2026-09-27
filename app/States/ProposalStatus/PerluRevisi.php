<?php

namespace App\States\ProposalStatus;

class PerluRevisi extends ProposalStatusState
{
    public static $name = 'perlu_revisi';

    public function label(): string
    {
        return 'Perlu Revisi';
    }

    public function badgeColor(): string
    {
        return 'bg-yellow-100 text-yellow-800';
    }

    public function isEditable(): bool
    {
        return true;
    }

    public function isTerminal(): bool
    {
        return false;
    }
}
