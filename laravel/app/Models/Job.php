<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'user_jobs';

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
        'estimate_id',
        'user_id',
        'company_id',
        'order_total',
        'payments',
        'order_counts',
        'edg',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'order_total' => 'decimal:2',
        'payments' => 'decimal:2',
    ];

    /**
     * Get the user who owns this job.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the company associated with this job.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * Get the payment history for this job.
     */
    public function paymentHistory(): HasMany
    {
        return $this->hasMany(PaymentHistory::class, 'job_id', 'id');
    }

    /**
     * Get the sample requests linked to this job.
     */
    public function sampleRequests(): HasMany
    {
        return $this->hasMany(SampleRequest::class, 'job_id', 'id');
    }

    /**
     * Calculate the balance due.
     */
    public function getBalanceDueAttribute(): float
    {
        return $this->order_total - $this->payments;
    }

    /**
     * Check if the job is fully paid.
     */
    public function isFullyPaid(): bool
    {
        return $this->balance_due <= 0;
    }
}
