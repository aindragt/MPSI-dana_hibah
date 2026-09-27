<?php

namespace App\States\ProposalStatus;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ProposalStatusState extends State
{
    abstract public function label(): string;      // Label untuk UI (e.g., "Perlu Revisi")

    abstract public function badgeColor(): string;  // Tailwind CSS class (e.g., "bg-yellow-100 text-yellow-800")

    abstract public function isEditable(): bool;    // Apakah Pengaju bisa edit di status ini

    abstract public function isTerminal(): bool;    // Apakah status ini final (tidak bisa transisi lagi)

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, Diajukan::class)
            ->allowTransition(Diajukan::class, VerifikasiOnline::class)
            ->allowTransition(VerifikasiOnline::class, PerluRevisi::class)
            ->allowTransition(VerifikasiOnline::class, MenungguBerkasFisik::class)
            ->allowTransition(VerifikasiOnline::class, Ditolak::class)
            ->allowTransition(PerluRevisi::class, Diajukan::class)
            ->allowTransition(MenungguBerkasFisik::class, VerifikasiFinal::class)
            ->allowTransition(MenungguBerkasFisik::class, PerluRevisi::class)
            ->allowTransition(MenungguBerkasFisik::class, Ditolak::class);
    }
}
