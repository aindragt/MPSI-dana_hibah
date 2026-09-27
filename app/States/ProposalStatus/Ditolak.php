<?php

namespace App\States\ProposalStatus;

class Ditolak extends ProposalStatusState
{
    public static $name = 'ditolak';

    public function label(): string
    {
        return 'Ditolak';
    }

    public function badgeColor(): string
    {
        return 'bg-red-100 text-red-800';
    }

    public function isEditable(): bool
    {
        return false;
    }

    public function isTerminal(): bool
    {
        return true;
    }
}
