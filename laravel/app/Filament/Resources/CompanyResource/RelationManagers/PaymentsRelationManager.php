<?php

namespace App\Filament\Resources\CompanyResource\RelationManagers;

use App\Filament\Resources\JobResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Payment History';

    protected static ?string $icon = 'heroicon-o-banknotes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('summ')
                    ->label('Amount')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('summ')
                    ->label('Amount')
                    ->money('usd')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Customer')
                    ->limit(25),
                Tables\Columns\TextColumn::make('job.name')
                    ->label('Job')
                    ->limit(25)
                    ->url(fn ($record) => $record->job_id
                        ? JobResource::getUrl('view', ['record' => $record->job_id])
                        : null)
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        0 => 'Payment',
                        1 => 'Refund',
                        2 => 'Adjustment',
                        default => 'Payment',
                    })
                    ->color(fn ($state) => match ($state) {
                        0 => 'success',
                        1 => 'danger',
                        2 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Notes')
                    ->limit(30)
                    ->toggleable(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([])
            ->emptyStateHeading('No payments recorded')
            ->emptyStateDescription('Payment history will appear here.');
    }
}
