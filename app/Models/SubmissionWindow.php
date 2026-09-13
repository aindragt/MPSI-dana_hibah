<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubmissionWindow extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'open_date',
        'close_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'open_date' => 'date',
            'close_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }
}
