<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate totals from line items
        $subtotal = 0;
        if (isset($data['line_items']) && is_array($data['line_items'])) {
            foreach ($data['line_items'] as $item) {
                $subtotal += ($item['qty'] ?? 0) * ($item['rate'] ?? 0);
            }
        }

        $taxRate = $data['tax_rate'] ?? 0;
        $taxAmount = $subtotal * ($taxRate / 100);
        $total = $subtotal + $taxAmount;
        $amountPaid = $data['amount_paid'] ?? 0;

        $data['subtotal'] = round($subtotal, 2);
        $data['tax_amount'] = round($taxAmount, 2);
        $data['total'] = round($total, 2);
        $data['balance_due'] = round($total - $amountPaid, 2);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
