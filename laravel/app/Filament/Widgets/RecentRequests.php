<?php

namespace App\Filament\Widgets;

use App\Models\SampleRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentRequests extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Sample Requests';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SampleRequest::query()
                    ->with(['user', 'company'])
                    ->latest('id')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Customer')
                    ->limit(30),
                Tables\Columns\TextColumn::make('company.company')
                    ->label('Company')
                    ->limit(20),
                Tables\Columns\BadgeColumn::make('status')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        SampleRequest::STATUS_PENDING => 'Pending',
                        SampleRequest::STATUS_PROCESSED => 'Processed',
                        SampleRequest::STATUS_SHIPPED => 'Shipped',
                        SampleRequest::STATUS_CANCELLED => 'Cancelled',
                        default => 'Unknown',
                    })
                    ->colors([
                        'warning' => SampleRequest::STATUS_PENDING,
                        'info' => SampleRequest::STATUS_PROCESSED,
                        'success' => SampleRequest::STATUS_SHIPPED,
                        'danger' => SampleRequest::STATUS_CANCELLED,
                    ]),
                Tables\Columns\TextColumn::make('request_date')
                    ->label('Date')
                    ->dateTime()
                    ->since(),
            ])
            ->paginated(false)
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (SampleRequest $record): string => route('filament.admin.resources.sample-requests.view', $record))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
