<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyExecutionLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'activity_monthly_schedule_id',
        'month',
        'reported_value',
        'justification',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'reported_value' => 'float',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ActivityMonthlySchedule::class, 'activity_monthly_schedule_id');
    }
}
