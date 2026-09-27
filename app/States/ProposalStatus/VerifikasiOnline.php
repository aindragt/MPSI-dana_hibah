<?php

namespace App\States\ProposalStatus;

class VerifikasiOnline extends ProposalStatusState
{
    public static $name = 'verifikasi_online';

    public function label(): string
    {
        return 'Verifikasi Berkas Online';
    }

    public function badgeColor(): string
    {
        return 'bg-purple-100 text-purple-800';
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
