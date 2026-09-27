<?php

namespace App\States\ProposalStatus;

class Diajukan extends ProposalStatusState
{
    public static $name = 'diajukan';

    public function label(): string
    {
        return 'Diajukan';
    }

    public function badgeColor(): string
    {
        return 'bg-blue-100 text-blue-800';
    }

    public function isEditable(): bool
    {
        return false;
    }

    public function isTerminal(): bool
    {
        return false;
    }
}
