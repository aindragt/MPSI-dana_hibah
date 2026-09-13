<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_number',
        'user_id',
        'submission_window_id',
        'activity_title',
        'activity_description',
        'total_budget',
        'execution_start_date',
        'execution_end_date',
        'status',
        'verified_by',
        'submitted_at',
        'verified_online_at',
        'physical_docs_received_at',
        'final_verified_at',
        'rejected_at',
        'lpj_file',
        'lpj_status',
        'lpj_catatan',
    ];

    protected function casts(): array
    {
        return [
            'total_budget' => 'decimal:2',
            'execution_start_date' => 'date',
            'execution_end_date' => 'date',
            'submitted_at' => 'datetime',
            'verified_online_at' => 'datetime',
            'physical_docs_received_at' => 'datetime',
            'final_verified_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function submissionWindow(): BelongsTo
    {
        return $this->belongsTo(SubmissionWindow::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProposalDocument::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(ProposalStatusLog::class);
    }

    public function revisionNotes(): HasMany
    {
        return $this->hasMany(RevisionNote::class);
    }
}
