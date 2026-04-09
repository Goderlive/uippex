<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetProject extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';

    protected $fillable = [
        'budget_program_id',
        'code',
        'name',
    ];

    public function budgetProgram(): BelongsTo
    {
        return $this->belongsTo(BudgetProgram::class);
    }
}
