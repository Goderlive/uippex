<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class AdministrativeUnit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'fiscal_year_id',
        'department_id',
        'master_administrative_unit_id',
        'general_sector_id',
        'auxiliary_sector_id',
        'budget_project_id',
        'name',
    ];

    protected static function booted()
    {
        static::created(function ($unit) {
            if (empty($unit->master_administrative_unit_id)) {
                $dept = $unit->department;
                $masterDeptId = $dept ? $dept->master_department_id : null;

                $master = \App\Models\MasterAdministrativeUnit::create([
                    'name' => $unit->name,
                    'master_department_id' => $masterDeptId,
                ]);
                $unit->master_administrative_unit_id = $master->id;
                $unit->saveQuietly();
            }
        });
    }

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function masterAdministrativeUnit(): BelongsTo
    {
        return $this->belongsTo(MasterAdministrativeUnit::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function generalSector(): BelongsTo
    {
        return $this->belongsTo(GeneralSector::class, 'general_sector_id');
    }

    public function auxiliarySector(): BelongsTo
    {
        return $this->belongsTo(AuxiliarySector::class, 'auxiliary_sector_id');
    }

    public function budgetProject(): BelongsTo
    {
        return $this->belongsTo(BudgetProject::class, 'budget_project_id');
    }

    public function substantiveActivities()
    {
        return $this->hasMany(SubstantiveActivity::class, 'administrative_unit_id');
    }

    public function holder(): MorphOne
    {
        return $this->morphOne(OfficeHolder::class, 'holdable');
    }
}
