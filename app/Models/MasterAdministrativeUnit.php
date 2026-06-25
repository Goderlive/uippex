<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterAdministrativeUnit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'master_department_id',
        'name',
    ];

    public function masterDepartment(): BelongsTo
    {
        return $this->belongsTo(MasterDepartment::class);
    }

    public function administrativeUnits(): HasMany
    {
        return $this->hasMany(AdministrativeUnit::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
