<?php

namespace App\Filament\Widgets;

use App\Models\Job;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentJobs extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Jobs';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Job::query()
                    ->with(['user', 'company'])
                    ->latest('id')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('job_id')
                    ->label('Job #')
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Customer')
                    ->limit(25),
                Tables\Columns\TextColumn::make('company.company')
                    ->label('Company')
                    ->limit(20),
                Tables\Columns\TextColumn::make('order_total')
                    ->label('Total')
                    ->money('USD'),
                Tables\Columns\TextColumn::make('balance_due')
                    ->label('Balance')
                    ->money('USD')
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
            ->paginated(false)
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Job $record): string => route('filament.admin.resources.jobs.view', $record))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
