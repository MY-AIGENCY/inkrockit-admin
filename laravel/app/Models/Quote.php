<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quote extends Model
{
    protected $fillable = [
        'quote_number',
        'user_id',
        'company_id',
        'project_name',
        'status',
        'stock',
        'size',
        'color',
        'finishing',
        'options',
        'valid_until',
        'internal_notes',
        'customer_message',
    ];

    protected $casts = [
        'finishing' => 'array',
        'options' => 'array',
        'valid_until' => 'date',
    ];

    /**
     * Get the user (customer) for this quote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company for this quote.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the quote request that was converted to this quote.
     */
    public function quoteRequest(): HasOne
    {
        return $this->hasOne(QuoteRequest::class, 'converted_quote_id');
    }

    /**
     * Generate a unique quote number.
     */
    public static function generateQuoteNumber(): string
    {
        $prefix = 'QT';
        $year = date('Y');
        $lastQuote = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastQuote && preg_match('/QT-(\d{4})-(\d+)/', $lastQuote->quote_number, $matches)) {
            $sequence = (int) $matches[2] + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $year, $sequence);
    }

    /**
     * Get the status color for display.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Draft' => 'gray',
            'Review' => 'warning',
            'Sent' => 'info',
            'Accepted' => 'success',
            'Rejected' => 'danger',
            default => 'gray',
        };
    }
}
