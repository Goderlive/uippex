<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgrammaticReconduction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'administrative_unit_id',
        'document_number',
        'requested_date',
        'quarter',
        'requested_by',
        'validated_by',
        'validated_at',
        'status',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'quarter' => 'integer',
        'status' => 'integer',
        'validated_at' => 'datetime',
    ];

    public function administrativeUnit(): BelongsTo
    {
        return $this->belongsTo(AdministrativeUnit::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReconductionItem::class);
    }
}
