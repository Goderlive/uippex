<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterDepartment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function masterAdministrativeUnits(): HasMany
    {
        return $this->hasMany(MasterAdministrativeUnit::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
