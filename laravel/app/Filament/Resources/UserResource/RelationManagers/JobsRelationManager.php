<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Job;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class JobsRelationManager extends RelationManager
{
    protected static string $relationship = 'jobs';

    protected static ?string $title = 'Order History';

    protected static ?string $icon = 'heroicon-o-shopping-cart';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('job_id')
            ->columns([
                Tables\Columns\TextColumn::make('job_id')
                    ->label('Job #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn (Job $record): string => $record->isFullyPaid() ? 'Paid' : ($record->payments > 0 ? 'Partial' : 'Unpaid'))
                    ->color(fn (Job $record): string => $record->isFullyPaid() ? 'success' : ($record->payments > 0 ? 'warning' : 'danger')),

                Tables\Columns\TextColumn::make('order_total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payments')
                    ->label('Paid')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('balance_due')
                    ->label('Balance')
                    ->money('USD')
                    ->sortable()
                    ->color(fn (Job $record): string => $record->balance_due > 0 ? 'danger' : 'success'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\Filter::make('unpaid')
                    ->label('Unpaid Only')
                    ->query(fn ($query) => $query->whereRaw('order_total > payments')),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Job $record) => route('filament.admin.resources.jobs.view', $record)),
            ])
            ->bulkActions([])
            ->emptyStateHeading('No orders yet')
            ->emptyStateDescription('This customer has not placed any orders.')
            ->emptyStateIcon('heroicon-o-shopping-cart');
    }
}
