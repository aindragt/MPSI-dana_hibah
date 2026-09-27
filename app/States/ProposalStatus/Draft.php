<?php

namespace App\States\ProposalStatus;

class Draft extends ProposalStatusState
{
    public static $name = 'draft';

    public function label(): string
    {
        return 'Draft';
    }

    public function badgeColor(): string
    {
        return 'bg-gray-100 text-gray-700';
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
