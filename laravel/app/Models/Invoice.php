<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'user_id',
        'company_id',
        'job_id',
        'status',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total',
        'amount_paid',
        'balance_due',
        'issue_date',
        'due_date',
        'paid_date',
        'line_items',
        'notes',
        'terms',
    ];

    protected $casts = [
        'line_items' => 'array',
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
    ];

    /**
     * Get the customer for this invoice.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company for this invoice.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the job associated with this invoice.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the payment history records for this invoice.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(PaymentHistory::class);
    }

    /**
     * Generate a unique invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $lastInvoice = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice && preg_match('/INV-(\d{4})-(\d+)/', $lastInvoice->invoice_number, $matches)) {
            $sequence = (int) $matches[2] + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    /**
     * Calculate totals from line items.
     */
    public function calculateTotals(): void
    {
        $subtotal = 0;

        if (is_array($this->line_items)) {
            foreach ($this->line_items as $item) {
                $subtotal += ($item['qty'] ?? 0) * ($item['rate'] ?? 0);
            }
        }

        $this->subtotal = $subtotal;
        $this->tax_amount = $subtotal * ($this->tax_rate / 100);
        $this->total = $subtotal + $this->tax_amount;
        $this->balance_due = $this->total - $this->amount_paid;
    }

    /**
     * Record a payment.
     */
    public function recordPayment(float $amount): void
    {
        $this->amount_paid += $amount;
        $this->balance_due = $this->total - $this->amount_paid;

        if ($this->balance_due <= 0) {
            $this->status = 'Paid';
            $this->paid_date = now();
        } elseif ($this->amount_paid > 0) {
            $this->status = 'Partial';
        }

        $this->save();
    }

    /**
     * Check if the invoice is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && $this->balance_due > 0
            && $this->status !== 'Paid';
    }

    /**
     * Get the status color for display.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Draft' => 'gray',
            'Sent' => 'info',
            'Paid' => 'success',
            'Partial' => 'warning',
            'Overdue' => 'danger',
            'Cancelled' => 'gray',
            default => 'gray',
        };
    }
}
