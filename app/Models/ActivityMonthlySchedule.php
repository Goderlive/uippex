<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityMonthlySchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'substantive_activity_id',
        'jan_programmed', 'feb_programmed', 'mar_programmed', 'apr_programmed',
        'may_programmed', 'jun_programmed', 'jul_programmed', 'aug_programmed',
        'sep_programmed', 'oct_programmed', 'nov_programmed', 'dec_programmed',
        'jan_executed', 'feb_executed', 'mar_executed', 'apr_executed',
        'may_executed', 'jun_executed', 'jul_executed', 'aug_executed',
        'sep_executed', 'oct_executed', 'nov_executed', 'dec_executed',
    ];

    protected function casts(): array
    {
        return [
            'jan_programmed' => 'float', 'feb_programmed' => 'float', 'mar_programmed' => 'float', 'apr_programmed' => 'float',
            'may_programmed' => 'float', 'jun_programmed' => 'float', 'jul_programmed' => 'float', 'aug_programmed' => 'float',
            'sep_programmed' => 'float', 'oct_programmed' => 'float', 'nov_programmed' => 'float', 'dec_programmed' => 'float',
            'jan_executed' => 'float', 'feb_executed' => 'float', 'mar_executed' => 'float', 'apr_executed' => 'float',
            'may_executed' => 'float', 'jun_executed' => 'float', 'jul_executed' => 'float', 'aug_executed' => 'float',
            'sep_executed' => 'float', 'oct_executed' => 'float', 'nov_executed' => 'float', 'dec_executed' => 'float',
        ];
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(SubstantiveActivity::class, 'substantive_activity_id');
    }

    public function executionLogs(): HasMany
    {
        return $this->hasMany(MonthlyExecutionLog::class);
    }
}
