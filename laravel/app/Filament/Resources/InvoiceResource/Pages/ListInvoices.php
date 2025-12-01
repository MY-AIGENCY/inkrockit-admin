<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

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
            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Draft')),
            'sent' => Tab::make('Sent')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Sent')),
            'overdue' => Tab::make('Overdue')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('due_date', '<', now())
                    ->where('balance_due', '>', 0)
                    ->whereNotIn('status', ['Paid', 'Cancelled']))
                ->badge(fn () => \App\Models\Invoice::query()
                    ->where('due_date', '<', now())
                    ->where('balance_due', '>', 0)
                    ->whereNotIn('status', ['Paid', 'Cancelled'])
                    ->count() ?: null)
                ->badgeColor('danger'),
            'paid' => Tab::make('Paid')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Paid')),
        ];
    }
}
