<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteRequestResource\Pages;
use App\Models\QuoteRequest;
use App\Models\Quote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;

class QuoteRequestResource extends Resource
{
    protected static ?string $model = QuoteRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'project_name';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'New')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Request Information')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Customer Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('project_name')
                            ->label('Project Name')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options([
                                'New' => 'New',
                                'In Progress' => 'In Progress',
                                'Converted' => 'Converted',
                            ])
                            ->default('New')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Project Specifications')
                    ->schema([
                        Forms\Components\Textarea::make('specs')
                            ->label('Specifications')
                            ->rows(6)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Conversion')
                    ->schema([
                        Forms\Components\Select::make('converted_quote_id')
                            ->label('Converted to Quote')
                            ->relationship('convertedQuote', 'quote_number')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                    ])
                    ->visible(fn ($record) => $record?->converted_quote_id !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('project_name')
                    ->label('Project')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'info' => 'New',
                        'warning' => 'In Progress',
                        'success' => 'Converted',
                    ]),
                Tables\Columns\TextColumn::make('convertedQuote.quote_number')
                    ->label('Quote #')
                    ->placeholder('-')
                    ->url(fn ($record) => $record->converted_quote_id
                        ? QuoteResource::getUrl('view', ['record' => $record->converted_quote_id])
                        : null),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'New' => 'New',
                        'In Progress' => 'In Progress',
                        'Converted' => 'Converted',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('convert')
                    ->label('Convert to Quote')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'Converted')
                    ->action(function ($record) {
                        // Create a new quote from this request
                        $quote = Quote::create([
                            'quote_number' => Quote::generateQuoteNumber(),
                            'project_name' => $record->project_name,
                            'status' => 'Draft',
                            'valid_until' => now()->addDays(30),
                            'internal_notes' => "Converted from Quote Request\n\nOriginal specs:\n" . $record->specs,
                        ]);

                        // Update the request
                        $record->update([
                            'status' => 'Converted',
                            'converted_quote_id' => $quote->id,
                        ]);

                        Notification::make()
                            ->title('Quote Created')
                            ->body("Quote {$quote->quote_number} has been created.")
                            ->success()
                            ->send();

                        return redirect(QuoteResource::getUrl('edit', ['record' => $quote]));
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Convert to Quote')
                    ->modalDescription('This will create a new draft quote from this request. Continue?'),
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
                Infolists\Components\Section::make('Request Details')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('customer_name')
                                    ->label('Customer'),
                                Infolists\Components\TextEntry::make('email')
                                    ->label('Email')
                                    ->copyable(),
                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'New' => 'info',
                                        'In Progress' => 'warning',
                                        'Converted' => 'success',
                                        default => 'gray',
                                    }),
                            ]),
                    ]),

                Infolists\Components\Section::make('Project Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('project_name')
                            ->label('Project Name'),
                        Infolists\Components\TextEntry::make('specs')
                            ->label('Specifications')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Conversion')
                    ->schema([
                        Infolists\Components\TextEntry::make('convertedQuote.quote_number')
                            ->label('Converted to Quote')
                            ->url(fn ($record) => $record->converted_quote_id
                                ? QuoteResource::getUrl('view', ['record' => $record->converted_quote_id])
                                : null)
                            ->color('primary'),
                    ])
                    ->visible(fn ($record) => $record->converted_quote_id !== null),

                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Received')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime(),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
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
            'index' => Pages\ListQuoteRequests::route('/'),
            'create' => Pages\CreateQuoteRequest::route('/create'),
            'view' => Pages\ViewQuoteRequest::route('/{record}'),
            'edit' => Pages\EditQuoteRequest::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['customer_name', 'email', 'project_name'];
    }
}
