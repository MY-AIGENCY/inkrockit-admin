<?php

namespace App\Filament\Resources\CompanyResource\RelationManagers;

use App\Filament\Resources\JobResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class JobsRelationManager extends RelationManager
{
    protected static string $relationship = 'jobs';

    protected static ?string $title = 'Jobs';

    protected static ?string $icon = 'heroicon-o-briefcase';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Job Name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Job #')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Customer')
                    ->limit(25),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => fn ($state) => in_array($state, ['Quote', 'Cancelled']),
                        'warning' => fn ($state) => in_array($state, ['Waiting', 'Review', 'Proofing', 'On Hold']),
                        'primary' => fn ($state) => in_array($state, ['Production', 'Pre-Production']),
                        'info' => 'Ready to Ship',
                        'success' => fn ($state) => in_array($state, ['Shipped', 'Complete', 'Delivered']),
                    ]),
                Tables\Columns\TextColumn::make('date')
                    ->label('Created')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('summ')
                    ->label('Total')
                    ->money('usd')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Production' => 'Production',
                        'Proofing' => 'Proofing',
                        'Waiting' => 'Waiting',
                        'Shipped' => 'Shipped',
                        'Complete' => 'Complete',
                    ]),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => JobResource::getUrl('view', ['record' => $record])),
            ])
            ->bulkActions([]);
    }
}
