<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProposalDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'document_type_id',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'version' => 'integer',
        ];
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(DocumentVerification::class);
    }
}
