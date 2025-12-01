<?php

namespace App\Filament\Resources\ShipmentResource\Pages;

use App\Filament\Resources\ShipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewShipment extends ViewRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('track')
                ->label('Track Package')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('info')
                ->url(fn () => $this->record->tracking_url, shouldOpenInNewTab: true)
                ->visible(fn () => $this->record->tracking_url !== null),
            Actions\Action::make('updateStatus')
                ->label('Update Status')
                ->icon('heroicon-o-arrow-path')
                ->form([
                    \Filament\Forms\Components\Select::make('status')
                        ->options([
                            'Pending' => 'Pending',
                            'Manifested' => 'Manifested',
                            'In Transit' => 'In Transit',
                            'Out for Delivery' => 'Out for Delivery',
                            'Delivered' => 'Delivered',
                            'Exception' => 'Exception',
                        ])
                        ->default(fn () => $this->record->status)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $updates = ['status' => $data['status']];

                    if ($data['status'] === 'Delivered' && !$this->record->actual_delivery) {
                        $updates['actual_delivery'] = now();
                    }

                    $this->record->update($updates);

                    \Filament\Notifications\Notification::make()
                        ->title('Status Updated')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('markDelivered')
                ->label('Mark Delivered')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => !in_array($this->record->status, ['Delivered', 'Exception']))
                ->action(function () {
                    $this->record->update([
                        'status' => 'Delivered',
                        'actual_delivery' => now(),
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Shipment Delivered')
                        ->body('Package has been marked as delivered.')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation(),
        ];
    }
}
