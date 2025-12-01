<?php

namespace App\Filament\Resources\ShipmentResource\Pages;

use App\Filament\Resources\ShipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListShipments extends ListRecords
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Pending'))
                ->badge(fn () => \App\Models\Shipment::where('status', 'Pending')->count() ?: null),
            'in_transit' => Tab::make('In Transit')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['In Transit', 'Out for Delivery']))
                ->badge(fn () => \App\Models\Shipment::whereIn('status', ['In Transit', 'Out for Delivery'])->count() ?: null)
                ->badgeColor('primary'),
            'delivered' => Tab::make('Delivered')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Delivered')),
            'exception' => Tab::make('Exceptions')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Exception'))
                ->badge(fn () => \App\Models\Shipment::where('status', 'Exception')->count() ?: null)
                ->badgeColor('danger'),
        ];
    }
}
