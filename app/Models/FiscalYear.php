<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FiscalYear extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'year',
        'is_active',
    ];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }
}
