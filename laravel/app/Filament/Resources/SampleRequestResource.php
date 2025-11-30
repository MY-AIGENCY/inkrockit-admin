<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SampleRequestResource\Pages;
use App\Models\SampleRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SampleRequestResource extends Resource
{
    protected static ?string $model = SampleRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel = 'Sample Requests';

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.email', 'company.company', 'industry'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Request Details')
                    ->schema([
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
                        Forms\Components\Select::make('job_id')
                            ->label('Job')
                            ->relationship('job', 'job_id')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('status')
                            ->options([
                                SampleRequest::STATUS_PENDING => 'Pending',
                                SampleRequest::STATUS_PROCESSED => 'Processed',
                                SampleRequest::STATUS_SHIPPED => 'Shipped',
                                SampleRequest::STATUS_CANCELLED => 'Cancelled',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\TextInput::make('industry')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('industry_send')
                            ->label('Industry (Send)')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('complete_address')
                            ->label('Shipping Address')
                            ->rows(3),
                        Forms\Components\TextInput::make('user_ip')
                            ->label('IP Address')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('request_date')
                            ->label('Request Date')
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
                Tables\Columns\TextColumn::make('job.job_id')
                    ->label('Job')
                    ->searchable()
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('industry')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('request_date')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('updateStatus')
                        ->label('Update Status')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->options([
                                    SampleRequest::STATUS_PENDING => 'Pending',
                                    SampleRequest::STATUS_PROCESSED => 'Processed',
                                    SampleRequest::STATUS_SHIPPED => 'Shipped',
                                    SampleRequest::STATUS_CANCELLED => 'Cancelled',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $data, $records): void {
                            $records->each->update(['status' => $data['status']]);
                        }),
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
            'index' => Pages\ListSampleRequests::route('/'),
            'view' => Pages\ViewSampleRequest::route('/{record}'),
            'edit' => Pages\EditSampleRequest::route('/{record}/edit'),
        ];
    }
}
