<?php

namespace App\Filament\Resources\JobResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'paymentHistory';

    protected static ?string $title = 'Payment History';

    protected static ?string $icon = 'heroicon-o-credit-card';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('summ')
                    ->label('Amount')
                    ->prefix('$')
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make('type')
                    ->label('Payment Type')
                    ->options([
                        'credit_card' => 'Credit Card',
                        'check' => 'Check',
                        'cash' => 'Cash',
                        'wire' => 'Wire Transfer',
                        'other' => 'Other',
                    ]),

                Forms\Components\DatePicker::make('date')
                    ->label('Payment Date')
                    ->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('summ')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Running Total')
                    ->money('USD'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\IconColumn::make('removed')
                    ->label('Active')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !$record->removed)
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('removed')
                    ->label('Status')
                    ->trueLabel('Removed')
                    ->falseLabel('Active')
                    ->default(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->emptyStateHeading('No payments recorded')
            ->emptyStateDescription('No payment history for this job.')
            ->emptyStateIcon('heroicon-o-banknotes');
    }
}
