<?php

namespace App\States\ProposalStatus;

class VerifikasiFinal extends ProposalStatusState
{
    public static $name = 'verifikasi_final';

    public function label(): string
    {
        return 'Verifikasi Final';
    }

    public function badgeColor(): string
    {
        return 'bg-green-100 text-green-800';
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
