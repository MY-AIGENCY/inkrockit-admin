<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentHistoryResource\Pages;
use App\Models\PaymentHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentHistoryResource extends Resource
{
    protected static ?string $model = PaymentHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel = 'Payments';

    public static function getGloballySearchableAttributes(): array
    {
        return ['job.job_id', 'client.email'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\Select::make('job_id')
                            ->label('Job')
                            ->relationship('job', 'job_id')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        Forms\Components\Select::make('client_id')
                            ->label('Customer')
                            ->relationship('client', 'email')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        Forms\Components\TextInput::make('summ')
                            ->label('Amount')
                            ->prefix('$')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('total')
                            ->label('Total')
                            ->prefix('$')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('type')
                            ->label('Payment Type')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('date')
                            ->label('Payment Date')
                            ->disabled(),
                        Forms\Components\Toggle::make('removed')
                            ->label('Removed/Voided'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('job.job_id')
                    ->label('Job #')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.email')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('summ')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('removed')
                    ->label('Voided')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('removed')
                    ->label('Status')
                    ->placeholder('All Payments')
                    ->trueLabel('Voided Only')
                    ->falseLabel('Active Only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for payment history
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentHistories::route('/'),
            'view' => Pages\ViewPaymentHistory::route('/{record}'),
        ];
    }
}
