<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteRequest extends Model
{
    protected $fillable = [
        'customer_name',
        'email',
        'project_name',
        'specs',
        'status',
        'converted_quote_id',
    ];

    /**
     * Get the quote this request was converted to.
     */
    public function convertedQuote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'converted_quote_id');
    }

    /**
     * Get the status color for display.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'New' => 'info',
            'In Progress' => 'warning',
            'Converted' => 'success',
            default => 'gray',
        };
    }

    /**
     * Check if this request has been converted to a quote.
     */
    public function isConverted(): bool
    {
        return $this->status === 'Converted' && $this->converted_quote_id !== null;
    }
}
