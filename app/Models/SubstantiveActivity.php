<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SubstantiveActivity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'administrative_unit_id',
        'development_theme_id',
        'name',
        'measurement_unit',
    ];

    protected $appends = ['annual_target'];

    public function administrativeUnit(): BelongsTo
    {
        return $this->belongsTo(AdministrativeUnit::class);
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(DevelopmentTheme::class, 'development_theme_id');
    }

    public function monthlySchedule(): HasOne
    {
        return $this->hasOne(ActivityMonthlySchedule::class);
    }

    public function progressReports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MonthlyProgressReport::class);
    }

    /**
     * Calculate sum of all programmed months.
     */
    public function getAnnualTargetAttribute(): float
    {
        if (!$this->monthlySchedule) {
            return 0;
        }

        $s = $this->monthlySchedule;
        return (float) (
            $s->jan_programmed + $s->feb_programmed + $s->mar_programmed +
            $s->apr_programmed + $s->may_programmed + $s->jun_programmed +
            $s->jul_programmed + $s->aug_programmed + $s->sep_programmed +
            $s->oct_programmed + $s->nov_programmed + $s->dec_programmed
        );
    }
}
