<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class ReconductionItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'programmatic_reconduction_id',
        'substantive_activity_id',
        'modification_type',
        'previous_annual_goal',
        'new_annual_goal',
        'achieved_so_far',
        'previous_schedule',
        'new_schedule',
        'justification',
    ];

    protected $casts = [
        'previous_annual_goal' => 'decimal:2',
        'new_annual_goal' => 'decimal:2',
        'achieved_so_far' => 'decimal:2',
        // Utilizando AsArrayObject para interactuar directamente desde JS/Vue/React.
        'previous_schedule' => AsArrayObject::class,
        'new_schedule' => AsArrayObject::class,
    ];

    public function reconduction(): BelongsTo
    {
        return $this->belongsTo(ProgrammaticReconduction::class, 'programmatic_reconduction_id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(SubstantiveActivity::class, 'substantive_activity_id');
    }
}
