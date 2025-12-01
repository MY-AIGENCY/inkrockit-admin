<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $fillable = [
        'tracking_number',
        'job_id',
        'user_id',
        'company_id',
        'carrier',
        'status',
        'service_type',
        'recipient_name',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'weight',
        'dimensions',
        'contents_description',
        'ship_date',
        'estimated_delivery',
        'actual_delivery',
        'shipping_cost',
        'notes',
    ];

    protected $casts = [
        'ship_date' => 'date',
        'estimated_delivery' => 'date',
        'actual_delivery' => 'date',
        'weight' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
    ];

    /**
     * Get the job associated with this shipment.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the customer for this shipment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company for this shipment.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the formatted full address.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line1,
            $this->address_line2,
            implode(', ', array_filter([$this->city, $this->state])) . ' ' . $this->postal_code,
            $this->country !== 'US' ? $this->country : null,
        ]);

        return implode("\n", $parts);
    }

    /**
     * Get the tracking URL for the carrier.
     */
    public function getTrackingUrlAttribute(): ?string
    {
        if (!$this->tracking_number) {
            return null;
        }

        return match (strtoupper($this->carrier ?? '')) {
            'UPS' => "https://www.ups.com/track?tracknum={$this->tracking_number}",
            'FEDEX' => "https://www.fedex.com/fedextrack/?trknbr={$this->tracking_number}",
            'USPS' => "https://tools.usps.com/go/TrackConfirmAction?tLabels={$this->tracking_number}",
            'DHL' => "https://www.dhl.com/en/express/tracking.html?AWB={$this->tracking_number}",
            default => null,
        };
    }

    /**
     * Get the status color for display.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Pending' => 'gray',
            'Manifested' => 'info',
            'In Transit' => 'primary',
            'Out for Delivery' => 'warning',
            'Delivered' => 'success',
            'Exception' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Check if the shipment is delivered.
     */
    public function isDelivered(): bool
    {
        return $this->status === 'Delivered';
    }

    /**
     * Check if the shipment has an exception.
     */
    public function hasException(): bool
    {
        return $this->status === 'Exception';
    }
}
