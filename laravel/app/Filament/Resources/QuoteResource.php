<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Models\Quote;
use App\Models\User;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'quote_number';

    /**
     * Calculate pricing for a price tier.
     */
    protected static function calculatePricing(Forms\Get $get, Forms\Set $set): void
    {
        $qty = floatval($get('qty') ?? 0);
        $unitCost = floatval($get('unit_cost') ?? 0);
        $markupPercent = floatval($get('markup_percent') ?? 0);

        if ($qty > 0 && $unitCost > 0) {
            $unitPrice = $unitCost * (1 + ($markupPercent / 100));
            $total = $unitPrice * $qty;

            $set('unit_price', round($unitPrice, 4));
            $set('total', round($total, 2));
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Quote Information')
                    ->schema([
                        Forms\Components\TextInput::make('quote_number')
                            ->label('Quote #')
                            ->default(fn () => Quote::generateQuoteNumber())
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Draft' => 'Draft',
                                'Review' => 'Review',
                                'Sent' => 'Sent',
                                'Accepted' => 'Accepted',
                                'Rejected' => 'Rejected',
                            ])
                            ->default('Draft')
                            ->required(),
                        Forms\Components\DatePicker::make('valid_until')
                            ->label('Valid Until')
                            ->default(now()->addDays(30)),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Customer')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required(),
                                Forms\Components\TextInput::make('login')
                                    ->required(),
                            ]),
                        Forms\Components\Select::make('company_id')
                            ->label('Company')
                            ->relationship('company', 'company')
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Project Details')
                    ->schema([
                        Forms\Components\TextInput::make('project_name')
                            ->label('Project Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('stock')
                            ->label('Stock/Material')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('size')
                            ->label('Size')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('color')
                            ->label('Color')
                            ->maxLength(50),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing Tiers')
                    ->schema([
                        Forms\Components\Repeater::make('price_options')
                            ->label('Price Options')
                            ->schema([
                                Forms\Components\TextInput::make('qty')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) =>
                                        static::calculatePricing($get, $set)),

                                Forms\Components\TextInput::make('unit_cost')
                                    ->label('Unit Cost')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) =>
                                        static::calculatePricing($get, $set)),

                                Forms\Components\TextInput::make('markup_percent')
                                    ->label('Markup %')
                                    ->numeric()
                                    ->suffix('%')
                                    ->default(50)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) =>
                                        static::calculatePricing($get, $set)),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Unit Price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\TextInput::make('total')
                                    ->label('Total')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\Toggle::make('selected')
                                    ->label('Selected')
                                    ->inline(false)
                                    ->helperText('Mark as chosen option'),
                            ])
                            ->columns(6)
                            ->defaultItems(1)
                            ->addActionLabel('Add Price Tier')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['qty'], $state['total'])
                                    ? number_format($state['qty']) . ' qty @ $' . number_format($state['total'], 2)
                                    : null
                            ),
                    ]),

                Forms\Components\Section::make('Options & Finishing')
                    ->schema([
                        Forms\Components\TagsInput::make('finishing')
                            ->label('Finishing Options')
                            ->placeholder('Add finishing option')
                            ->suggestions([
                                'Matte Lamination',
                                'Gloss Lamination',
                                'Spot UV',
                                'Foil Stamping',
                                'Embossing',
                                'Die Cut',
                                'Rounded Corners',
                                'Hole Punch',
                            ]),
                        Forms\Components\KeyValue::make('options')
                            ->label('Additional Options')
                            ->keyLabel('Option')
                            ->valueLabel('Value')
                            ->addActionLabel('Add Option'),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('internal_notes')
                            ->label('Internal Notes')
                            ->rows(3)
                            ->helperText('Only visible to staff'),
                        Forms\Components\Textarea::make('customer_message')
                            ->label('Customer Message')
                            ->rows(3)
                            ->helperText('Will be visible on the quote'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('quote_number')
                    ->label('Quote #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('project_name')
                    ->label('Project')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Customer')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'Draft',
                        'warning' => 'Review',
                        'info' => 'Sent',
                        'success' => 'Accepted',
                        'danger' => 'Rejected',
                    ]),
                Tables\Columns\TextColumn::make('valid_until')
                    ->label('Valid Until')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->valid_until && $record->valid_until->isPast() ? 'danger' : null),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Draft' => 'Draft',
                        'Review' => 'Review',
                        'Sent' => 'Sent',
                        'Accepted' => 'Accepted',
                        'Rejected' => 'Rejected',
                    ]),
                Tables\Filters\Filter::make('expired')
                    ->query(fn (Builder $query) => $query->where('valid_until', '<', now()))
                    ->label('Expired Quotes'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('send')
                    ->label('Send')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->visible(fn ($record) => in_array($record->status, ['Draft', 'Review']))
                    ->action(fn ($record) => $record->update(['status' => 'Sent'])),
                Tables\Actions\Action::make('accept')
                    ->label('Accept')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'Sent')
                    ->action(fn ($record) => $record->update(['status' => 'Accepted'])),
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
                Infolists\Components\Section::make('Quote Overview')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('quote_number')
                                    ->label('Quote #')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->copyable(),
                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'Draft' => 'gray',
                                        'Review' => 'warning',
                                        'Sent' => 'info',
                                        'Accepted' => 'success',
                                        'Rejected' => 'danger',
                                        default => 'gray',
                                    }),
                                Infolists\Components\TextEntry::make('valid_until')
                                    ->label('Valid Until')
                                    ->date()
                                    ->color(fn ($record) => $record->valid_until && $record->valid_until->isPast() ? 'danger' : null),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Created')
                                    ->dateTime(),
                            ]),
                    ]),

                Infolists\Components\Section::make('Customer Information')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('user.email')
                                    ->label('Customer Email')
                                    ->copyable(),
                                Infolists\Components\TextEntry::make('company.name')
                                    ->label('Company'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Project Details')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('project_name')
                                    ->label('Project Name'),
                                Infolists\Components\TextEntry::make('stock')
                                    ->label('Stock/Material'),
                                Infolists\Components\TextEntry::make('size')
                                    ->label('Size'),
                                Infolists\Components\TextEntry::make('color')
                                    ->label('Color'),
                            ]),
                        Infolists\Components\TextEntry::make('finishing')
                            ->label('Finishing Options')
                            ->badge()
                            ->separator(','),
                        Infolists\Components\KeyValueEntry::make('options')
                            ->label('Additional Options'),
                    ]),

                Infolists\Components\Section::make('Pricing Options')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('price_options')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('qty')
                                    ->label('Quantity')
                                    ->numeric(),
                                Infolists\Components\TextEntry::make('unit_cost')
                                    ->label('Unit Cost')
                                    ->money('usd'),
                                Infolists\Components\TextEntry::make('markup_percent')
                                    ->label('Markup')
                                    ->suffix('%'),
                                Infolists\Components\TextEntry::make('unit_price')
                                    ->label('Unit Price')
                                    ->money('usd'),
                                Infolists\Components\TextEntry::make('total')
                                    ->label('Total')
                                    ->money('usd')
                                    ->weight('bold'),
                                Infolists\Components\IconEntry::make('selected')
                                    ->label('Selected')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-minus')
                                    ->trueColor('success'),
                            ])
                            ->columns(6),
                    ])
                    ->visible(fn ($record) => !empty($record->price_options)),

                Infolists\Components\Section::make('Notes')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('internal_notes')
                                    ->label('Internal Notes')
                                    ->markdown(),
                                Infolists\Components\TextEntry::make('customer_message')
                                    ->label('Customer Message')
                                    ->markdown(),
                            ]),
                    ])
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
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'view' => Pages\ViewQuote::route('/{record}'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        // Note: company column is 'company' not 'name' in legacy users_company table
        return ['quote_number', 'project_name', 'user.email', 'company.company'];
    }
}
