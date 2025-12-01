<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobResource\Pages;
use App\Filament\Resources\JobResource\RelationManagers;
use App\Models\Job;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'job_id';

    protected static ?string $navigationLabel = 'Jobs';

    public static function getGloballySearchableAttributes(): array
    {
        return ['job_id', 'user.email', 'company.company'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Job Details')
                    ->schema([
                        Forms\Components\TextInput::make('job_id')
                            ->label('Job ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('estimate_id')
                            ->label('Estimate ID')
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('company_id')
                            ->label('Company')
                            ->relationship('company', 'company')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Forms\Components\Section::make('Financials')
                    ->schema([
                        Forms\Components\TextInput::make('order_total')
                            ->label('Order Total')
                            ->prefix('$')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('payments')
                            ->label('Payments Received')
                            ->prefix('$')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\Placeholder::make('balance_due')
                            ->label('Balance Due')
                            ->content(fn (Job $record): string => '$' . number_format($record->balance_due, 2)),
                        Forms\Components\TextInput::make('order_counts')
                            ->label('Order Count')
                            ->numeric()
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('job_id')
                                    ->label('Job #')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable()
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('payment_status')
                                    ->label('Status')
                                    ->badge()
                                    ->getStateUsing(fn (Job $record): string => $record->isFullyPaid() ? 'Paid' : 'Balance Due')
                                    ->color(fn (Job $record): string => $record->isFullyPaid() ? 'success' : 'warning'),

                                Infolists\Components\TextEntry::make('order_total')
                                    ->label('Order Total')
                                    ->money('USD')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('balance_due')
                                    ->label('Balance')
                                    ->money('USD')
                                    ->size('lg')
                                    ->color(fn (Job $record): string => $record->balance_due > 0 ? 'danger' : 'success'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Customer Information')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('user.email')
                                    ->label('Email')
                                    ->icon('heroicon-m-envelope')
                                    ->copyable()
                                    ->url(fn (Job $record) => $record->user ? route('filament.admin.resources.users.view', $record->user) : null),

                                Infolists\Components\TextEntry::make('user.full_name')
                                    ->label('Name')
                                    ->icon('heroicon-m-user')
                                    ->getStateUsing(fn (Job $record) => trim(($record->user?->first_name ?? '') . ' ' . ($record->user?->last_name ?? '')) ?: 'N/A'),

                                Infolists\Components\TextEntry::make('company.company')
                                    ->label('Company')
                                    ->icon('heroicon-m-building-office')
                                    ->url(fn (Job $record) => $record->company ? route('filament.admin.resources.companies.view', $record->company) : null),

                                Infolists\Components\TextEntry::make('estimate_id')
                                    ->label('Estimate ID')
                                    ->icon('heroicon-m-document-text')
                                    ->placeholder('N/A'),
                            ]),
                    ])->collapsible(),

                Infolists\Components\Section::make('Financial Details')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('order_total')
                                    ->label('Order Total')
                                    ->money('USD'),

                                Infolists\Components\TextEntry::make('payments')
                                    ->label('Payments Received')
                                    ->money('USD'),

                                Infolists\Components\TextEntry::make('balance_due')
                                    ->label('Balance Due')
                                    ->money('USD')
                                    ->color(fn (Job $record): string => $record->balance_due > 0 ? 'danger' : 'success'),

                                Infolists\Components\TextEntry::make('order_counts')
                                    ->label('Order Count')
                                    ->placeholder('0'),
                            ]),
                    ])->collapsible(),
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
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->description(fn (Job $record) => $record->company?->company),
                Tables\Columns\TextColumn::make('order_total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payments')
                    ->label('Paid')
                    ->money('USD')
                    ->sortable()
                    ->color('success'),
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
                    ->query(fn (Builder $query): Builder => $query->whereRaw('order_total > payments')),
                Tables\Filters\Filter::make('paid')
                    ->label('Fully Paid')
                    ->query(fn (Builder $query): Builder => $query->whereRaw('order_total <= payments')),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\NotesRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobs::route('/'),
            'view' => Pages\ViewJob::route('/{record}'),
            'edit' => Pages\EditJob::route('/{record}/edit'),
        ];
    }
}
