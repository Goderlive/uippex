<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MunicipalConfiguration extends Model
{
    protected $guarded = [];

    /**
     * Helper to retrieve or create the single row configuration.
     *
     * @return static
     */
    public static function getSettings(): static
    {
        return static::firstOrCreate([], [
            'official_name' => 'H. Ayuntamiento',
            'primary_color' => '#333333',
        ]);
    }
}
