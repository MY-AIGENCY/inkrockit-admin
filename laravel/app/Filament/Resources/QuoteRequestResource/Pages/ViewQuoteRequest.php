<?php

namespace App\Filament\Resources\QuoteRequestResource\Pages;

use App\Filament\Resources\QuoteRequestResource;
use App\Filament\Resources\QuoteResource;
use App\Models\Quote;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewQuoteRequest extends ViewRecord
{
    protected static string $resource = QuoteRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('convert')
                ->label('Convert to Quote')
                ->icon('heroicon-o-document-plus')
                ->color('success')
                ->visible(fn () => $this->record->status !== 'Converted')
                ->action(function () {
                    $quote = Quote::create([
                        'quote_number' => Quote::generateQuoteNumber(),
                        'project_name' => $this->record->project_name,
                        'status' => 'Draft',
                        'valid_until' => now()->addDays(30),
                        'internal_notes' => "Converted from Quote Request\n\nOriginal specs:\n" . $this->record->specs,
                    ]);

                    $this->record->update([
                        'status' => 'Converted',
                        'converted_quote_id' => $quote->id,
                    ]);

                    Notification::make()
                        ->title('Quote Created')
                        ->body("Quote {$quote->quote_number} has been created.")
                        ->success()
                        ->send();

                    return redirect(QuoteResource::getUrl('edit', ['record' => $quote]));
                })
                ->requiresConfirmation()
                ->modalHeading('Convert to Quote')
                ->modalDescription('This will create a new draft quote from this request. Continue?'),
        ];
    }
}
