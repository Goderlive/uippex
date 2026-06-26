<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetProgram extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';

    protected $fillable = [
        'code',
        'year',
        'name',
        'description',
    ];

    public function budgetProjects(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BudgetProject::class);
    }
}
