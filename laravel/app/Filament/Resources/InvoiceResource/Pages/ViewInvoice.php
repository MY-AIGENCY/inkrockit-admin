<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('send')
                ->label('Send Invoice')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->visible(fn () => $this->record->status === 'Draft')
                ->action(fn () => $this->record->update(['status' => 'Sent']))
                ->requiresConfirmation()
                ->modalHeading('Send Invoice')
                ->modalDescription('Are you sure you want to mark this invoice as sent?'),
            Actions\Action::make('recordPayment')
                ->label('Record Payment')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->visible(fn () => $this->record->balance_due > 0)
                ->form([
                    \Filament\Forms\Components\TextInput::make('amount')
                        ->label('Payment Amount')
                        ->numeric()
                        ->prefix('$')
                        ->default(fn () => $this->record->balance_due)
                        ->required(),
                    \Filament\Forms\Components\DatePicker::make('date')
                        ->label('Payment Date')
                        ->default(now())
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('reference')
                        ->label('Reference/Transaction ID'),
                ])
                ->action(function (array $data) {
                    $amount = (float) $data['amount'];
                    $this->record->recordPayment($amount);

                    // Create payment history record
                    \App\Models\PaymentHistory::create([
                        'invoice_id' => $this->record->id,
                        'job_id' => $this->record->job_id,
                        'client_id' => $this->record->user_id,
                        'summ' => $amount,
                        'date' => $data['date'],
                        'type' => 'invoice',
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Payment Recorded')
                        ->body('$' . number_format($amount, 2) . ' payment has been recorded.')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('markPaid')
                ->label('Mark as Paid')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => in_array($this->record->status, ['Sent', 'Partial', 'Overdue']) && $this->record->balance_due > 0)
                ->action(fn () => $this->record->update([
                    'status' => 'Paid',
                    'amount_paid' => $this->record->total,
                    'balance_due' => 0,
                    'paid_date' => now(),
                ]))
                ->requiresConfirmation(),
            Actions\Action::make('cancel')
                ->label('Cancel Invoice')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => !in_array($this->record->status, ['Paid', 'Cancelled']))
                ->action(fn () => $this->record->update(['status' => 'Cancelled']))
                ->requiresConfirmation()
                ->modalHeading('Cancel Invoice')
                ->modalDescription('Are you sure you want to cancel this invoice?'),
        ];
    }
}
