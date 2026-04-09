<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralSector extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    
    protected $fillable = [
        'code',
        'name',
    ];
}
