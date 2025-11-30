<?php

namespace App\Filament\Widgets;

use App\Models\SampleRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UrgentJobs extends BaseWidget
{
    protected static ?string $heading = 'Pending Requests';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected static ?string $maxHeight = '400px';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Show sample requests that need attention (pending status = 0)
                SampleRequest::query()
                    ->pending()
                    ->orderBy('request_date', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(fn ($state) => "REQ-{$state}")
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Customer')
                    ->description(fn ($record) => $record->company?->company ?? 'No company')
                    ->limit(20),

                Tables\Columns\TextColumn::make('request_date')
                    ->label('Date')
                    ->date('M j')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        SampleRequest::STATUS_PENDING => 'Pending',
                        SampleRequest::STATUS_PROCESSED => 'Processed',
                        SampleRequest::STATUS_SHIPPED => 'Shipped',
                        SampleRequest::STATUS_CANCELLED => 'Cancelled',
                        default => 'Unknown',
                    })
                    ->color(fn (int $state): string => match ($state) {
                        SampleRequest::STATUS_PENDING => 'warning',
                        SampleRequest::STATUS_PROCESSED => 'info',
                        SampleRequest::STATUS_SHIPPED => 'success',
                        SampleRequest::STATUS_CANCELLED => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->iconButton()
                    ->url(fn ($record) => route('filament.admin.resources.sample-requests.view', $record)),
            ])
            ->paginated(false)
            ->headerActions([
                Tables\Actions\Action::make('viewAll')
                    ->label('View Queue')
                    ->icon('heroicon-m-arrow-right')
                    ->url(route('filament.admin.resources.sample-requests.index'))
                    ->color('gray')
                    ->size('sm'),
            ])
            ->emptyStateHeading('All Clear')
            ->emptyStateDescription('No pending requests requiring attention.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
