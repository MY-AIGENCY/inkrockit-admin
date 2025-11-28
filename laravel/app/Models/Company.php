<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'users_company';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company',
        'main_uid',
        'abbr',
        'duplicate',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'duplicate' => 'boolean',
    ];

    /**
     * Get the main/primary user for this company.
     */
    public function mainUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'main_uid', 'id');
    }

    /**
     * Get all users associated with this company.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'company_id', 'id');
    }

    /**
     * Get all sample requests for this company.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(SampleRequest::class, 'company_id', 'id');
    }

    /**
     * Get all jobs/orders for this company.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'company_id', 'id');
    }

    /**
     * Scope a query to exclude duplicate companies.
     */
    public function scopeNotDuplicate($query)
    {
        return $query->where('duplicate', 0);
    }

    /**
     * Scope a query to only include duplicate companies.
     */
    public function scopeIsDuplicate($query)
    {
        return $query->where('duplicate', 1);
    }
}
