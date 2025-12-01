<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentHistory extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'payment_history';

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
        'invoice_id',
        'job_id',
        'client_id',
        'summ',
        'total',
        'procent',
        'type',
        'user_type',
        'edg',
        'removed',
        'card_id',
        'date',
    ];

    /**
     * The attributes that should be cast.
     * Note: summ/total are not cast to decimal due to legacy data having empty strings
     */
    protected $casts = [
        'date' => 'date',
        'removed' => 'boolean',
    ];

    /**
     * Get the summ attribute as a float, defaulting to 0 for empty values.
     */
    public function getSummAttribute($value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }

    /**
     * Get the total attribute as a float, defaulting to 0 for empty values.
     */
    public function getTotalAttribute($value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }

    /**
     * Get the job this payment belongs to.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id', 'id');
    }

    /**
     * Get the client (user) this payment is from.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id', 'id');
    }

    /**
     * Get the invoice this payment belongs to.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Scope a query to exclude removed payments.
     */
    public function scopeActive($query)
    {
        return $query->where('removed', 0);
    }
}
