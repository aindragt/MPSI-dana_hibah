<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_document_id',
        'verified_by',
        'verification_type',
        'status',
        'notes',
    ];

    public function proposalDocument(): BelongsTo
    {
        return $this->belongsTo(ProposalDocument::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
