<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     */
    protected $table = 'users';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     * The legacy table doesn't have created_at/updated_at columns.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'login',
        'password',
        'email',
        'email_alt',
        'first_name',
        'last_name',
        'group_id',
        'user_abbr',
        'company_id',
        'country',
        'street',
        'street2',
        'city',
        'state',
        'zipcode',
        'phone',
        'phone_ext',
        'phone_type',
        'position',
        'industry',
        'fax',
        'admin_comment',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'group_id' => 'integer',
        'company_id' => 'integer',
    ];

    /**
     * Group ID constants matching the legacy system.
     */
    const GROUP_CUSTOMER = 1;
    const GROUP_STAFF = 2;
    const GROUP_ADMIN = 6;

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Get the user's name (required by Filament).
     * Falls back to login or email if no name is set.
     */
    public function getNameAttribute(): string
    {
        $fullName = trim("{$this->first_name} {$this->last_name}");

        if (!empty($fullName)) {
            return $fullName;
        }

        return $this->login ?? $this->email ?? 'User';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->group_id === self::GROUP_ADMIN;
    }

    /**
     * Check if user is staff (can access admin panel).
     */
    public function isStaff(): bool
    {
        return in_array($this->group_id, [self::GROUP_STAFF, self::GROUP_ADMIN]);
    }

    /**
     * Determine if the user can access a Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isStaff();
    }

    /**
     * Check if password is MD5 hashed (legacy).
     * MD5 hashes are 32 characters, bcrypt hashes start with $2y$ and are 60 chars.
     */
    public function hasLegacyPassword(): bool
    {
        return strlen($this->password) === 32;
    }

    /**
     * Get the company that the user belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * Get the requests submitted by this user.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(SampleRequest::class, 'user_id', 'id');
    }

    /**
     * Get the jobs/orders associated with this user.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'user_id', 'id');
    }

    /**
     * Get the invoices for this user.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'user_id', 'id');
    }

    /**
     * Get the payments for this user.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(PaymentHistory::class, 'user_id', 'id');
    }

    /**
     * Get the quotes for this user.
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'user_id', 'id');
    }

    /**
     * Scope a query to only include admin/staff users.
     */
    public function scopeAdminUsers($query)
    {
        return $query->whereIn('group_id', [self::GROUP_STAFF, self::GROUP_ADMIN]);
    }

    /**
     * Scope a query to only include customer users.
     */
    public function scopeCustomers($query)
    {
        return $query->where('group_id', self::GROUP_CUSTOMER);
    }
}
