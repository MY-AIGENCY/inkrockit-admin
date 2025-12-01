<?php

namespace App\Filament\Resources\JobResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class NotesRelationManager extends RelationManager
{
    protected static string $relationship = 'notes';

    protected static ?string $title = 'Activity Log & Notes';

    protected static ?string $icon = 'heroicon-o-chat-bubble-left-right';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('text')
                    ->label('Note')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->rows(4),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('text')
            ->columns([
                Tables\Columns\TextColumn::make('text')
                    ->label('Note')
                    ->limit(100)
                    ->wrap()
                    ->searchable(),

                Tables\Columns\TextColumn::make('author.email')
                    ->label('Author')
                    ->placeholder('System')
                    ->limit(20),

                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        0 => 'Note',
                        1 => 'Status Update',
                        2 => 'System',
                        default => 'Note',
                    })
                    ->color(fn ($state) => match ($state) {
                        0 => 'primary',
                        1 => 'success',
                        2 => 'gray',
                        default => 'primary',
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Note')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['date'] = now();
                        $data['author_id'] = auth()->id();
                        $data['type'] = 0;
                        $data['removed'] = 0;
                        // Legacy table requires these fields with NOT NULL constraints
                        $data['request_id'] = $this->ownerRecord->estimate_id ?? 0;
                        $data['company_id'] = $this->ownerRecord->company_id ?? 0;
                        $data['type_user'] = 0;
                        $data['required_uid'] = 0;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No notes recorded')
            ->emptyStateDescription('Add a note to track activity on this job.')
            ->emptyStateIcon('heroicon-o-chat-bubble-bottom-center-text');
    }
}
