<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Payment History';

    protected static ?string $icon = 'heroicon-o-banknotes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('summ')
                    ->label('Amount')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->label('Payment Date')
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('Payment Type')
                    ->options([
                        'credit_card' => 'Credit Card',
                        'check' => 'Check',
                        'cash' => 'Cash',
                        'wire' => 'Wire Transfer',
                        'other' => 'Other',
                    ])
                    ->default('credit_card'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('summ')
                    ->label('Amount')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge(),
                Tables\Columns\TextColumn::make('client.email')
                    ->label('Customer')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['invoice_id'] = $this->ownerRecord->id;
                        $data['job_id'] = $this->ownerRecord->job_id;
                        $data['client_id'] = $this->ownerRecord->user_id;
                        return $data;
                    })
                    ->after(function () {
                        // Update invoice payment totals
                        $totalPaid = $this->ownerRecord->payments()->sum('summ');
                        $this->ownerRecord->update([
                            'amount_paid' => $totalPaid,
                            'balance_due' => $this->ownerRecord->total - $totalPaid,
                            'status' => $totalPaid >= $this->ownerRecord->total ? 'Paid' : ($totalPaid > 0 ? 'Partial' : $this->ownerRecord->status),
                            'paid_date' => $totalPaid >= $this->ownerRecord->total ? now() : null,
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function () {
                        // Recalculate totals
                        $totalPaid = $this->ownerRecord->payments()->sum('summ');
                        $this->ownerRecord->update([
                            'amount_paid' => $totalPaid,
                            'balance_due' => $this->ownerRecord->total - $totalPaid,
                            'status' => $totalPaid >= $this->ownerRecord->total ? 'Paid' : ($totalPaid > 0 ? 'Partial' : 'Sent'),
                        ]);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function () {
                        // Recalculate totals after deletion
                        $totalPaid = $this->ownerRecord->payments()->sum('summ');
                        $this->ownerRecord->update([
                            'amount_paid' => $totalPaid,
                            'balance_due' => $this->ownerRecord->total - $totalPaid,
                            'status' => $totalPaid >= $this->ownerRecord->total ? 'Paid' : ($totalPaid > 0 ? 'Partial' : 'Sent'),
                            'paid_date' => $totalPaid >= $this->ownerRecord->total ? $this->ownerRecord->paid_date : null,
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
