<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Department extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'fiscal_year_id',
        'master_department_id',
        'name',
    ];

    protected static function booted()
    {
        static::created(function ($department) {
            if (empty($department->master_department_id)) {
                $master = \App\Models\MasterDepartment::create([
                    'name' => $department->name
                ]);
                $department->master_department_id = $master->id;
                $department->saveQuietly();
            }
        });
    }

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function masterDepartment(): BelongsTo
    {
        return $this->belongsTo(MasterDepartment::class);
    }

    public function administrativeUnits(): HasMany
    {
        return $this->hasMany(AdministrativeUnit::class);
    }

    public function holder(): MorphOne
    {
        return $this->morphOne(OfficeHolder::class, 'holdable');
    }
}
