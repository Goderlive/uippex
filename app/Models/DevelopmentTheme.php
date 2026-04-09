<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DevelopmentTheme extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'development_axis_id',
        'name',
    ];

    public function axis(): BelongsTo
    {
        return $this->belongsTo(DevelopmentAxis::class, 'development_axis_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(SubstantiveActivity::class);
    }
}
