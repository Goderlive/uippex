<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RamtCertificate extends Model
{
    protected $fillable = [
        'department_id',
        'quarter',
        'certificate_folio',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->certificate_folio)) {
                $model->certificate_folio = (string) Str::uuid();
            }
            if (empty($model->issued_at)) {
                $model->issued_at = now();
            }
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
