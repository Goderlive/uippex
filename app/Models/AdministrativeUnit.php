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
        'general_sector_code',
        'auxiliary_sector_code',
        'budget_project_code',
        'name',
    ];

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function generalSector(): ?GeneralSector
    {
        return GeneralSector::where('code', $this->general_sector_code)->first();
    }

    public function auxiliarySector(): ?AuxiliarySector
    {
        return AuxiliarySector::where('code', $this->auxiliary_sector_code)->first();
    }

    public function budgetProject(): ?BudgetProject
    {
        return BudgetProject::where('code', $this->budget_project_code)->first();
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
