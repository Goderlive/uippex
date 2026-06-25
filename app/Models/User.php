<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'master_administrative_unit_id',
        'master_department_id',
    ];

    /**
     * Get the master department.
     */
    public function masterDepartment(): BelongsTo
    {
        return $this->belongsTo(MasterDepartment::class);
    }

    /**
     * Get the master administrative unit.
     */
    public function masterAdministrativeUnit(): BelongsTo
    {
        return $this->belongsTo(MasterAdministrativeUnit::class);
    }

    /**
     * Alias for frontend compatibility
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(MasterDepartment::class, 'master_department_id');
    }

    /**
     * Alias for frontend compatibility
     */
    public function administrativeUnit(): BelongsTo
    {
        return $this->belongsTo(MasterAdministrativeUnit::class, 'master_administrative_unit_id');
    }

    /**
     * Helper to get the transactional department for the current active context (latest).
     */
    public function getCurrentDepartment()
    {
        return $this->master_department_id 
            ? \App\Models\Department::where('master_department_id', $this->master_department_id)->latest('id')->first() 
            : null;
    }

    /**
     * Helper to get the transactional administrative unit for the current active context (latest).
     */
    public function getCurrentAdministrativeUnit()
    {
        return $this->master_administrative_unit_id 
            ? \App\Models\AdministrativeUnit::where('master_administrative_unit_id', $this->master_administrative_unit_id)->latest('id')->first() 
            : null;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
