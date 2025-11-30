<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobResource\Pages;
use App\Models\Job;
use Filament\Forms;
use Filament\Forms\Form;
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
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('company.company')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->limit(20),
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
                Tables\Columns\IconColumn::make('is_fully_paid')
                    ->label('Paid')
                    ->boolean()
                    ->getStateUsing(fn (Job $record): bool => $record->isFullyPaid())
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
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
            //
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
