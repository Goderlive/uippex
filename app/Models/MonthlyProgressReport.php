<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonthlyProgressReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'substantive_activity_id',
        'month',
        'reported_value',
        'evidence_path',
        'status',
        'validated_by',
        'observations',
    ];

    /**
     * Relationship with the user who validated the report.
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Relationship with the activity.
     */
    public function substantiveActivity(): BelongsTo
    {
        return $this->belongsTo(SubstantiveActivity::class);
    }
}
