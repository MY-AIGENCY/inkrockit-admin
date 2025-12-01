<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyAddress extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'company_addresses';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'label',
        'line1',
        'line2',
        'city',
        'state',
        'zip',
        'country',
        'is_billing_default',
        'is_shipping_default',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_billing_default' => 'boolean',
        'is_shipping_default' => 'boolean',
    ];

    /**
     * Label options for addresses.
     */
    public const LABELS = [
        'HQ' => 'Headquarters',
        'Billing' => 'Billing',
        'Shipping' => 'Shipping',
        'Warehouse' => 'Warehouse',
        'Office' => 'Office',
        'Other' => 'Other',
    ];

    /**
     * Get the company that owns this address.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * Get the full address as a single string.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->line1,
            $this->line2,
            implode(', ', array_filter([$this->city, $this->state, $this->zip])),
            $this->country !== 'US' ? $this->country : null,
        ]);

        return implode("\n", $parts);
    }
}
