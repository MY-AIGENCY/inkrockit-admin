<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewQuote extends ViewRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('send')
                ->label('Send Quote')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->visible(fn () => in_array($this->record->status, ['Draft', 'Review']))
                ->action(fn () => $this->record->update(['status' => 'Sent']))
                ->requiresConfirmation()
                ->modalHeading('Send Quote')
                ->modalDescription('Are you sure you want to mark this quote as sent?'),
            Actions\Action::make('accept')
                ->label('Mark Accepted')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status === 'Sent')
                ->action(fn () => $this->record->update(['status' => 'Accepted']))
                ->requiresConfirmation(),
            Actions\Action::make('reject')
                ->label('Mark Rejected')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->record->status === 'Sent')
                ->action(fn () => $this->record->update(['status' => 'Rejected']))
                ->requiresConfirmation(),
        ];
    }
}
