<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'company';

    public static function getGloballySearchableAttributes(): array
    {
        return ['company', 'abbr'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Information')
                    ->schema([
                        Forms\Components\TextInput::make('company')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('abbr')
                            ->label('Abbreviation')
                            ->maxLength(50),
                        Forms\Components\Select::make('main_uid')
                            ->label('Primary Contact')
                            ->relationship('mainUser', 'email')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Toggle::make('duplicate')
                            ->label('Marked as Duplicate')
                            ->helperText('Duplicate companies are hidden from main lists'),
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
                Tables\Columns\TextColumn::make('company')
                    ->label('Company Name')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('abbr')
                    ->label('Abbr')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('mainUser.email')
                    ->label('Primary Contact')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->sortable(),
                Tables\Columns\IconColumn::make('duplicate')
                    ->label('Duplicate')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('warning')
                    ->falseColor('success'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('duplicate')
                    ->label('Duplicate Status')
                    ->placeholder('All Companies')
                    ->trueLabel('Duplicates Only')
                    ->falseLabel('Non-Duplicates Only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // No delete for safety
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(1)
                                ->schema([
                                    Infolists\Components\TextEntry::make('company')
                                        ->label('')
                                        ->size('lg')
                                        ->weight('bold'),

                                    Infolists\Components\TextEntry::make('abbr')
                                        ->label('Abbreviation')
                                        ->placeholder('No abbreviation'),
                                ])
                                ->grow(),

                            Infolists\Components\Grid::make(1)
                                ->schema([
                                    Infolists\Components\TextEntry::make('duplicate')
                                        ->label('Status')
                                        ->badge()
                                        ->formatStateUsing(fn ($state) => $state ? 'Duplicate' : 'Active')
                                        ->color(fn ($state) => $state ? 'warning' : 'success'),

                                    Infolists\Components\TextEntry::make('id')
                                        ->label('ID')
                                        ->badge()
                                        ->color('gray'),
                                ])
                                ->grow(false),
                        ]),
                    ]),

                Infolists\Components\Section::make('Financial Summary')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('total_revenue')
                                    ->label('Total Revenue')
                                    ->getStateUsing(fn (Model $record) => $record->payments()->sum('summ'))
                                    ->money('usd')
                                    ->weight('bold')
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('outstanding_balance')
                                    ->label('Outstanding Balance')
                                    ->getStateUsing(fn (Model $record) => $record->invoices()->where('balance_due', '>', 0)->sum('balance_due'))
                                    ->money('usd')
                                    ->weight('bold')
                                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),

                                Infolists\Components\TextEntry::make('total_jobs')
                                    ->label('Total Jobs')
                                    ->getStateUsing(fn (Model $record) => $record->jobs()->count())
                                    ->badge()
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('last_payment')
                                    ->label('Last Payment')
                                    ->getStateUsing(fn (Model $record) => $record->payments()->latest('date')->first()?->date)
                                    ->date()
                                    ->placeholder('No payments'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Primary Contact')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('mainUser.full_name')
                                    ->label('Name')
                                    ->placeholder('Not set'),

                                Infolists\Components\TextEntry::make('mainUser.email')
                                    ->label('Email')
                                    ->icon('heroicon-m-envelope')
                                    ->copyable()
                                    ->placeholder('Not set'),

                                Infolists\Components\TextEntry::make('mainUser.phone')
                                    ->label('Phone')
                                    ->icon('heroicon-m-phone')
                                    ->placeholder('Not set'),
                            ]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersRelationManager::class,
            RelationManagers\JobsRelationManager::class,
            RelationManagers\InvoicesRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
            RelationManagers\AddressesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'view' => Pages\ViewCompany::route('/{record}'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
