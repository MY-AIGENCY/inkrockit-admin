<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SampleRequest extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'requests';

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
        'job_id',
        'user_id',
        'company_id',
        'request_date',
        'industry',
        'industry_send',
        'complete_address',
        'user_ip',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'request_date' => 'date',
    ];

    /**
     * Status constants matching the legacy system.
     */
    const STATUS_PENDING = 0;
    const STATUS_PROCESSED = 1;
    const STATUS_SHIPPED = 2;
    const STATUS_CANCELLED = 3;

    /**
     * Get the user who made this request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the company associated with this request.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * Get the job/order linked to this request.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id', 'id');
    }

    /**
     * Get the notes for this request.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(RequestNote::class, 'request_id', 'id');
    }

    /**
     * Check if the request has been processed.
     */
    public function isProcessed(): bool
    {
        return $this->status >= self::STATUS_PROCESSED;
    }

    /**
     * Check if the request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include processed requests.
     */
    public function scopeProcessed($query)
    {
        return $query->where('status', '>=', self::STATUS_PROCESSED);
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSED => 'Processed',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_CANCELLED => 'Cancelled',
            default => 'Unknown',
        };
    }
}
