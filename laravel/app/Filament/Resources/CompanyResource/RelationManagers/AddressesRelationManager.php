<?php

namespace App\Filament\Resources\CompanyResource\RelationManagers;

use App\Models\CompanyAddress;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    protected static ?string $title = 'Addresses';

    protected static ?string $icon = 'heroicon-o-map-pin';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('label')
                    ->options(CompanyAddress::LABELS)
                    ->required(),
                Forms\Components\TextInput::make('line1')
                    ->label('Address Line 1')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('line2')
                    ->label('Address Line 2')
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('state')
                    ->maxLength(50),
                Forms\Components\TextInput::make('zip')
                    ->label('ZIP Code')
                    ->maxLength(20),
                Forms\Components\Select::make('country')
                    ->options([
                        'US' => 'United States',
                        'CA' => 'Canada',
                        'MX' => 'Mexico',
                    ])
                    ->default('US'),
                Forms\Components\Toggle::make('is_billing_default')
                    ->label('Default Billing Address'),
                Forms\Components\Toggle::make('is_shipping_default')
                    ->label('Default Shipping Address'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => CompanyAddress::LABELS[$state] ?? $state),
                Tables\Columns\TextColumn::make('line1')
                    ->label('Address')
                    ->limit(40),
                Tables\Columns\TextColumn::make('city')
                    ->label('City'),
                Tables\Columns\TextColumn::make('state')
                    ->label('State'),
                Tables\Columns\TextColumn::make('zip')
                    ->label('ZIP'),
                Tables\Columns\IconColumn::make('is_billing_default')
                    ->label('Billing')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success'),
                Tables\Columns\IconColumn::make('is_shipping_default')
                    ->label('Shipping')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Address'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No addresses on file')
            ->emptyStateDescription('Add shipping and billing addresses for this company.');
    }
}
