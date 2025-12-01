<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\SampleRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'requests';

    protected static ?string $title = 'Sample Requests';

    protected static ?string $icon = 'heroicon-o-clipboard-document-list';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(fn ($state) => "REQ-{$state}")
                    ->sortable(),

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

                Tables\Columns\TextColumn::make('industry')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('request_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('job.job_id')
                    ->label('Linked Job')
                    ->placeholder('N/A')
                    ->badge()
                    ->color('gray'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        SampleRequest::STATUS_PENDING => 'Pending',
                        SampleRequest::STATUS_PROCESSED => 'Processed',
                        SampleRequest::STATUS_SHIPPED => 'Shipped',
                        SampleRequest::STATUS_CANCELLED => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->url(fn (SampleRequest $record) => route('filament.admin.resources.sample-requests.view', $record)),
            ])
            ->bulkActions([])
            ->emptyStateHeading('No requests')
            ->emptyStateDescription('This customer has not submitted any sample requests.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }
}
