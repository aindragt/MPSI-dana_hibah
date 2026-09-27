<?php

namespace App\States\ProposalStatus;

class MenungguBerkasFisik extends ProposalStatusState
{
    public static $name = 'menunggu_berkas_fisik';

    public function label(): string
    {
        return 'Menunggu Berkas Fisik';
    }

    public function badgeColor(): string
    {
        return 'bg-indigo-100 text-indigo-800';
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
