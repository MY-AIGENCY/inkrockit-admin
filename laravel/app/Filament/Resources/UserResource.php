<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'email';

    protected static ?string $navigationLabel = 'Customers';

    public static function getGloballySearchableAttributes(): array
    {
        return ['email', 'first_name', 'last_name', 'login', 'company.company'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Account Information')
                    ->schema([
                        Forms\Components\TextInput::make('login')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email_alt')
                            ->email()
                            ->label('Alternate Email')
                            ->maxLength(255),
                        Forms\Components\Select::make('group_id')
                            ->label('Role')
                            ->options([
                                User::GROUP_CUSTOMER => 'Customer',
                                User::GROUP_STAFF => 'Staff',
                                User::GROUP_ADMIN => 'Admin',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone_ext')
                            ->label('Extension')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('position')
                            ->label('Job Title')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('fax')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Company & Address')
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->label('Company')
                            ->relationship('company', 'company')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('street')
                            ->label('Address Line 1')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('street2')
                            ->label('Address Line 2')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('state')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('zipcode')
                            ->label('ZIP Code')
                            ->maxLength(20),
                        Forms\Components\TextInput::make('country')
                            ->maxLength(100),
                    ])->columns(2),

                Forms\Components\Section::make('Admin Notes')
                    ->schema([
                        Forms\Components\Textarea::make('admin_comment')
                            ->label('Internal Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(1)
                                ->schema([
                                    Infolists\Components\TextEntry::make('full_name')
                                        ->label('')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->placeholder('No name'),

                                    Infolists\Components\TextEntry::make('email')
                                        ->label('')
                                        ->icon('heroicon-m-envelope')
                                        ->copyable(),
                                ])
                                ->grow(),

                            Infolists\Components\Grid::make(1)
                                ->schema([
                                    Infolists\Components\TextEntry::make('group_id')
                                        ->label('Role')
                                        ->badge()
                                        ->formatStateUsing(fn (int $state): string => match ($state) {
                                            User::GROUP_ADMIN => 'Admin',
                                            User::GROUP_STAFF => 'Staff',
                                            default => 'Customer',
                                        })
                                        ->color(fn (int $state): string => match ($state) {
                                            User::GROUP_ADMIN => 'danger',
                                            User::GROUP_STAFF => 'warning',
                                            default => 'success',
                                        }),

                                    Infolists\Components\TextEntry::make('id')
                                        ->label('ID')
                                        ->badge()
                                        ->color('gray'),
                                ])
                                ->grow(false),
                        ]),
                    ]),

                Infolists\Components\Section::make('Contact Information')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('phone')
                                    ->label('Phone')
                                    ->icon('heroicon-m-phone')
                                    ->placeholder('Not provided'),

                                Infolists\Components\TextEntry::make('phone_ext')
                                    ->label('Extension')
                                    ->placeholder('N/A'),

                                Infolists\Components\TextEntry::make('fax')
                                    ->label('Fax')
                                    ->placeholder('N/A'),

                                Infolists\Components\TextEntry::make('email')
                                    ->label('Primary Email')
                                    ->icon('heroicon-m-envelope')
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('email_alt')
                                    ->label('Alternate Email')
                                    ->icon('heroicon-m-envelope')
                                    ->placeholder('N/A'),

                                Infolists\Components\TextEntry::make('position')
                                    ->label('Job Title')
                                    ->placeholder('N/A'),
                            ]),
                    ])->collapsible(),

                Infolists\Components\Section::make('Company & Address')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('company.company')
                                    ->label('Company')
                                    ->icon('heroicon-m-building-office')
                                    ->url(fn (User $record) => $record->company ? route('filament.admin.resources.companies.view', $record->company) : null)
                                    ->placeholder('No company'),

                                Infolists\Components\TextEntry::make('industry')
                                    ->label('Industry')
                                    ->placeholder('N/A'),

                                Infolists\Components\TextEntry::make('full_address')
                                    ->label('Address')
                                    ->icon('heroicon-m-map-pin')
                                    ->getStateUsing(fn (User $record) => collect([
                                        $record->street,
                                        $record->street2,
                                        implode(', ', array_filter([$record->city, $record->state, $record->zipcode])),
                                        $record->country,
                                    ])->filter()->join("\n"))
                                    ->placeholder('No address')
                                    ->columnSpanFull(),
                            ]),
                    ])->collapsible(),

                Infolists\Components\Section::make('Admin Notes')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Infolists\Components\TextEntry::make('admin_comment')
                            ->label('')
                            ->markdown()
                            ->placeholder('No internal notes recorded.')
                            ->columnSpanFull(),
                    ])->collapsible()
                    ->collapsed(),
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
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name']),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                Tables\Columns\TextColumn::make('login')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('company.company')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\BadgeColumn::make('group_id')
                    ->label('Role')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        User::GROUP_ADMIN => 'Admin',
                        User::GROUP_STAFF => 'Staff',
                        default => 'Customer',
                    })
                    ->colors([
                        'danger' => User::GROUP_ADMIN,
                        'warning' => User::GROUP_STAFF,
                        'success' => User::GROUP_CUSTOMER,
                    ]),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('state')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('group_id')
                    ->label('Role')
                    ->options([
                        User::GROUP_CUSTOMER => 'Customer',
                        User::GROUP_STAFF => 'Staff',
                        User::GROUP_ADMIN => 'Admin',
                    ]),
                Tables\Filters\SelectFilter::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'company')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // No delete action for safety with 20k users
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\JobsRelationManager::class,
            RelationManagers\RequestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
