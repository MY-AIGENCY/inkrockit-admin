<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShipmentResource\Pages;
use App\Models\Shipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Fulfillment';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'tracking_number';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['In Transit', 'Out for Delivery'])->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Shipment Information')
                    ->schema([
                        Forms\Components\TextInput::make('tracking_number')
                            ->label('Tracking Number')
                            ->maxLength(100),
                        Forms\Components\Select::make('carrier')
                            ->options([
                                'UPS' => 'UPS',
                                'FedEx' => 'FedEx',
                                'USPS' => 'USPS',
                                'DHL' => 'DHL',
                                'Other' => 'Other',
                            ])
                            ->required(),
                        Forms\Components\Select::make('service_type')
                            ->options([
                                'Ground' => 'Ground',
                                '2-Day' => '2-Day',
                                'Overnight' => 'Overnight',
                                'Express' => 'Express',
                                'Priority' => 'Priority',
                                'First Class' => 'First Class',
                                'Economy' => 'Economy',
                            ]),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Pending' => 'Pending',
                                'Manifested' => 'Manifested',
                                'In Transit' => 'In Transit',
                                'Out for Delivery' => 'Out for Delivery',
                                'Delivered' => 'Delivered',
                                'Exception' => 'Exception',
                            ])
                            ->default('Pending')
                            ->required(),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Linked Records')
                    ->schema([
                        Forms\Components\Select::make('job_id')
                            ->label('Job')
                            ->relationship('job', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "Job #{$record->id} - {$record->name}")
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('company_id')
                            ->label('Company')
                            ->relationship('company', 'company')  // Note: column is 'company' not 'name' in legacy users_company table
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Shipping Address')
                    ->schema([
                        Forms\Components\TextInput::make('recipient_name')
                            ->label('Recipient Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address_line1')
                            ->label('Address Line 1')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address_line2')
                            ->label('Address Line 2')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->label('City')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('state')
                            ->label('State')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('Postal Code')
                            ->maxLength(20),
                        Forms\Components\Select::make('country')
                            ->options([
                                'US' => 'United States',
                                'CA' => 'Canada',
                                'MX' => 'Mexico',
                            ])
                            ->default('US'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Package Details')
                    ->schema([
                        Forms\Components\TextInput::make('weight')
                            ->label('Weight (lbs)')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('dimensions')
                            ->label('Dimensions (LxWxH)')
                            ->placeholder('12x8x6'),
                        Forms\Components\Textarea::make('contents_description')
                            ->label('Contents Description')
                            ->rows(2),
                        Forms\Components\TextInput::make('shipping_cost')
                            ->label('Shipping Cost')
                            ->numeric()
                            ->prefix('$'),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Dates')
                    ->schema([
                        Forms\Components\DatePicker::make('ship_date')
                            ->label('Ship Date')
                            ->default(now()),
                        Forms\Components\DatePicker::make('estimated_delivery')
                            ->label('Estimated Delivery'),
                        Forms\Components\DatePicker::make('actual_delivery')
                            ->label('Actual Delivery'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Internal Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('Tracking #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->url(fn ($record) => $record->tracking_url, shouldOpenInNewTab: true)
                    ->color('primary'),
                Tables\Columns\TextColumn::make('carrier')
                    ->label('Carrier')
                    ->badge(),
                Tables\Columns\TextColumn::make('job.job_id')
                    ->label('Job')
                    ->limit(20)
                    ->url(fn ($record) => $record->job_id
                        ? JobResource::getUrl('view', ['record' => $record->job_id])
                        : null)
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('recipient_name')
                    ->label('Recipient')
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('city')
                    ->label('Destination')
                    ->formatStateUsing(fn ($record) => implode(', ', array_filter([$record->city, $record->state]))),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'Pending',
                        'info' => 'Manifested',
                        'primary' => 'In Transit',
                        'warning' => 'Out for Delivery',
                        'success' => 'Delivered',
                        'danger' => 'Exception',
                    ]),
                Tables\Columns\TextColumn::make('ship_date')
                    ->label('Shipped')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_delivery')
                    ->label('ETA')
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Manifested' => 'Manifested',
                        'In Transit' => 'In Transit',
                        'Out for Delivery' => 'Out for Delivery',
                        'Delivered' => 'Delivered',
                        'Exception' => 'Exception',
                    ]),
                Tables\Filters\SelectFilter::make('carrier')
                    ->options([
                        'UPS' => 'UPS',
                        'FedEx' => 'FedEx',
                        'USPS' => 'USPS',
                        'DHL' => 'DHL',
                    ]),
                Tables\Filters\Filter::make('in_transit')
                    ->query(fn (Builder $query) => $query->whereIn('status', ['In Transit', 'Out for Delivery']))
                    ->label('In Transit Only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('track')
                    ->label('Track')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->url(fn ($record) => $record->tracking_url, shouldOpenInNewTab: true)
                    ->visible(fn ($record) => $record->tracking_url !== null),
                Tables\Actions\Action::make('markDelivered')
                    ->label('Delivered')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => !in_array($record->status, ['Delivered', 'Exception']))
                    ->action(fn ($record) => $record->update([
                        'status' => 'Delivered',
                        'actual_delivery' => now(),
                    ]))
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Shipment Overview')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('tracking_number')
                                    ->label('Tracking #')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->copyable()
                                    ->url(fn ($record) => $record->tracking_url, shouldOpenInNewTab: true),
                                Infolists\Components\TextEntry::make('carrier')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('service_type')
                                    ->label('Service'),
                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'Pending' => 'gray',
                                        'Manifested' => 'info',
                                        'In Transit' => 'primary',
                                        'Out for Delivery' => 'warning',
                                        'Delivered' => 'success',
                                        'Exception' => 'danger',
                                        default => 'gray',
                                    }),
                            ]),
                    ]),

                Infolists\Components\Section::make('Dates & Cost')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('ship_date')
                                    ->label('Shipped')
                                    ->date(),
                                Infolists\Components\TextEntry::make('estimated_delivery')
                                    ->label('Est. Delivery')
                                    ->date(),
                                Infolists\Components\TextEntry::make('actual_delivery')
                                    ->label('Delivered')
                                    ->date()
                                    ->placeholder('Pending'),
                                Infolists\Components\TextEntry::make('shipping_cost')
                                    ->label('Cost')
                                    ->money('usd'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Shipping Address')
                    ->schema([
                        Infolists\Components\TextEntry::make('recipient_name')
                            ->label('Recipient'),
                        Infolists\Components\TextEntry::make('full_address')
                            ->label('Address')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Package Details')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('weight')
                                    ->label('Weight')
                                    ->suffix(' lbs'),
                                Infolists\Components\TextEntry::make('dimensions')
                                    ->label('Dimensions'),
                                Infolists\Components\TextEntry::make('contents_description')
                                    ->label('Contents'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Linked Records')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('job.job_id')
                                    ->label('Job')
                                    ->url(fn ($record) => $record->job_id
                                        ? JobResource::getUrl('view', ['record' => $record->job_id])
                                        : null)
                                    ->placeholder('-'),
                                Infolists\Components\TextEntry::make('user.email')
                                    ->label('Customer')
                                    ->placeholder('-'),
                                Infolists\Components\TextEntry::make('company.name')
                                    ->label('Company')
                                    ->placeholder('-'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Notes')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->notes)
                    ->collapsible(),
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
            'index' => Pages\ListShipments::route('/'),
            'create' => Pages\CreateShipment::route('/create'),
            'view' => Pages\ViewShipment::route('/{record}'),
            'edit' => Pages\EditShipment::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        // Note: user_jobs table doesn't have a name column, use job_id instead
        return ['tracking_number', 'recipient_name', 'user.email', 'job.job_id'];
    }
}
