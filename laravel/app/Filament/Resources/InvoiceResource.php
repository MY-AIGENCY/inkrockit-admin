<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'invoice_number';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'Overdue')
            ->orWhere(function ($query) {
                $query->whereIn('status', ['Sent', 'Partial'])
                    ->where('due_date', '<', now());
            })->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Invoice Information')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Invoice #')
                            ->default(fn () => Invoice::generateInvoiceNumber())
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Draft' => 'Draft',
                                'Sent' => 'Sent',
                                'Paid' => 'Paid',
                                'Partial' => 'Partial',
                                'Overdue' => 'Overdue',
                                'Cancelled' => 'Cancelled',
                            ])
                            ->default('Draft')
                            ->required(),
                        Forms\Components\DatePicker::make('issue_date')
                            ->label('Issue Date')
                            ->default(now()),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Due Date')
                            ->default(now()->addDays(30)),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Customer')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('company_id')
                            ->label('Company')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('job_id')
                            ->label('Related Job')
                            ->relationship('job', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "Job #{$record->id} - {$record->name}")
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Line Items')
                    ->schema([
                        Forms\Components\Repeater::make('line_items')
                            ->schema([
                                Forms\Components\TextInput::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->columnSpan(3),
                                Forms\Components\TextInput::make('qty')
                                    ->label('Qty')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),
                                Forms\Components\TextInput::make('rate')
                                    ->label('Rate')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required(),
                                Forms\Components\Placeholder::make('amount')
                                    ->label('Amount')
                                    ->content(fn ($get) => '$' . number_format(($get('qty') ?? 0) * ($get('rate') ?? 0), 2)),
                            ])
                            ->columns(6)
                            ->defaultItems(1)
                            ->addActionLabel('Add Line Item')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['description'] ?? null),
                    ]),

                Forms\Components\Section::make('Totals')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('tax_rate')
                            ->label('Tax Rate')
                            ->numeric()
                            ->suffix('%')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $subtotal = $get('subtotal') ?? 0;
                                $taxAmount = $subtotal * (($state ?? 0) / 100);
                                $set('tax_amount', round($taxAmount, 2));
                                $set('total', round($subtotal + $taxAmount, 2));
                            }),
                        Forms\Components\TextInput::make('tax_amount')
                            ->label('Tax Amount')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('amount_paid')
                            ->label('Amount Paid')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        Forms\Components\TextInput::make('balance_due')
                            ->label('Balance Due')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(6),

                Forms\Components\Section::make('Notes & Terms')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3),
                        Forms\Components\Textarea::make('terms')
                            ->label('Terms & Conditions')
                            ->rows(3)
                            ->default('Payment due within 30 days. Thank you for your business!'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Customer')
                    ->searchable()
                    ->limit(25)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => fn ($state) => in_array($state, ['Draft', 'Cancelled']),
                        'info' => 'Sent',
                        'success' => 'Paid',
                        'warning' => 'Partial',
                        'danger' => 'Overdue',
                    ]),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance_due')
                    ->label('Balance')
                    ->money('usd')
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('issue_date')
                    ->label('Issued')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Draft' => 'Draft',
                        'Sent' => 'Sent',
                        'Paid' => 'Paid',
                        'Partial' => 'Partial',
                        'Overdue' => 'Overdue',
                        'Cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('overdue')
                    ->query(fn (Builder $query) => $query
                        ->where('due_date', '<', now())
                        ->where('balance_due', '>', 0)
                        ->whereNotIn('status', ['Paid', 'Cancelled']))
                    ->label('Overdue Only'),
                Tables\Filters\Filter::make('unpaid')
                    ->query(fn (Builder $query) => $query->where('balance_due', '>', 0))
                    ->label('Unpaid Only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('send')
                    ->label('Send')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === 'Draft')
                    ->action(fn ($record) => $record->update(['status' => 'Sent']))
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('markPaid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->status, ['Sent', 'Partial', 'Overdue']))
                    ->action(fn ($record) => $record->update([
                        'status' => 'Paid',
                        'amount_paid' => $record->total,
                        'balance_due' => 0,
                        'paid_date' => now(),
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
                Infolists\Components\Section::make('Invoice Overview')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('invoice_number')
                                    ->label('Invoice #')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->copyable(),
                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'Draft', 'Cancelled' => 'gray',
                                        'Sent' => 'info',
                                        'Paid' => 'success',
                                        'Partial' => 'warning',
                                        'Overdue' => 'danger',
                                        default => 'gray',
                                    }),
                                Infolists\Components\TextEntry::make('issue_date')
                                    ->label('Issued')
                                    ->date(),
                                Infolists\Components\TextEntry::make('due_date')
                                    ->label('Due')
                                    ->date()
                                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),
                            ]),
                    ]),

                Infolists\Components\Section::make('Financial Summary')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->money('usd'),
                                Infolists\Components\TextEntry::make('tax_amount')
                                    ->label('Tax')
                                    ->money('usd')
                                    ->suffix(fn ($record) => " ({$record->tax_rate}%)"),
                                Infolists\Components\TextEntry::make('total')
                                    ->label('Total')
                                    ->money('usd')
                                    ->weight('bold'),
                                Infolists\Components\TextEntry::make('balance_due')
                                    ->label('Balance Due')
                                    ->money('usd')
                                    ->weight('bold')
                                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Customer Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('user.email')
                                    ->label('Customer Email')
                                    ->copyable(),
                                Infolists\Components\TextEntry::make('company.name')
                                    ->label('Company'),
                                Infolists\Components\TextEntry::make('job.id')
                                    ->label('Related Job')
                                    ->formatStateUsing(fn ($state) => $state ? "Job #{$state}" : '-')
                                    ->url(fn ($record) => $record->job_id
                                        ? JobResource::getUrl('view', ['record' => $record->job_id])
                                        : null),
                            ]),
                    ]),

                Infolists\Components\Section::make('Notes')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('notes')
                                    ->label('Notes')
                                    ->markdown(),
                                Infolists\Components\TextEntry::make('terms')
                                    ->label('Terms & Conditions')
                                    ->markdown(),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        // Note: company column is 'company' not 'name' in legacy users_company table
        return ['invoice_number', 'user.email', 'company.company'];
    }
}
