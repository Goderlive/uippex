<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OfficeHolder extends Model
{
    protected $fillable = [
        'academic_degree',
        'first_name',
        'last_name',
        'position_title',
    ];

    /**
     * Get the parent holdable model (Department or AdministrativeUnit).
     */
    public function holdable(): MorphTo
    {
        return $this->morphTo();
    }
}
