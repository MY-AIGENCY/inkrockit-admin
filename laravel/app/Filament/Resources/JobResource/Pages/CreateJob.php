<?php

namespace App\Filament\Resources\JobResource\Pages;

use App\Filament\Resources\JobResource;
use App\Models\Job;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateJob extends CreateRecord
{
    protected static string $resource = JobResource::class;

    protected static ?string $title = 'Create New Job';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Customer')
                        ->icon('heroicon-o-user')
                        ->description('Select the customer')
                        ->schema([
                            Forms\Components\Select::make('user_id')
                                ->label('Customer')
                                ->relationship('user', 'email')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                    if ($state) {
                                        $user = User::find($state);
                                        if ($user && $user->company_id) {
                                            $set('company_id', $user->company_id);
                                        }
                                    }
                                })
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('email')
                                        ->email()
                                        ->required(),
                                    Forms\Components\TextInput::make('login')
                                        ->required(),
                                    Forms\Components\TextInput::make('first_name'),
                                    Forms\Components\TextInput::make('last_name'),
                                ])
                                ->createOptionUsing(function (array $data): int {
                                    $data['group_id'] = User::GROUP_CUSTOMER;
                                    return User::create($data)->id;
                                }),

                            Forms\Components\Select::make('company_id')
                                ->label('Company')
                                ->relationship('company', 'company')
                                ->searchable()
                                ->preload()
                                ->helperText('Auto-filled from customer if available'),

                            Forms\Components\Placeholder::make('customer_info')
                                ->label('Customer Details')
                                ->content(function (Forms\Get $get): string {
                                    $userId = $get('user_id');
                                    if (!$userId) {
                                        return 'Select a customer to see details';
                                    }
                                    $user = User::with('company')->find($userId);
                                    if (!$user) {
                                        return 'Customer not found';
                                    }
                                    $info = [];
                                    if ($user->full_name) {
                                        $info[] = "Name: {$user->full_name}";
                                    }
                                    if ($user->phone) {
                                        $info[] = "Phone: {$user->phone}";
                                    }
                                    if ($user->company) {
                                        $info[] = "Company: {$user->company->company}";
                                    }
                                    return implode("\n", $info) ?: 'No additional details';
                                })
                                ->visible(fn (Forms\Get $get): bool => (bool) $get('user_id')),
                        ])
                        ->columns(2),

                    Step::make('Job Details')
                        ->icon('heroicon-o-briefcase')
                        ->description('Enter job information')
                        ->schema([
                            Forms\Components\TextInput::make('job_id')
                                ->label('Job ID')
                                ->default(fn () => Job::generateJobId())
                                ->required()
                                ->maxLength(50)
                                ->helperText('Auto-generated, can be modified'),

                            Forms\Components\TextInput::make('estimate_id')
                                ->label('Estimate/Quote ID')
                                ->maxLength(50)
                                ->helperText('Link to original estimate if applicable'),

                            Forms\Components\Textarea::make('description')
                                ->label('Job Description')
                                ->rows(3)
                                ->columnSpanFull()
                                ->helperText('Internal notes about this job (stored in notes after creation)'),
                        ])
                        ->columns(2),

                    Step::make('Pricing')
                        ->icon('heroicon-o-currency-dollar')
                        ->description('Set order pricing')
                        ->schema([
                            Forms\Components\TextInput::make('order_total')
                                ->label('Order Total')
                                ->numeric()
                                ->prefix('$')
                                ->default(0)
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                    $payments = $get('payments') ?? 0;
                                    $set('balance_preview', '$' . number_format($state - $payments, 2));
                                }),

                            Forms\Components\TextInput::make('payments')
                                ->label('Initial Payment')
                                ->numeric()
                                ->prefix('$')
                                ->default(0)
                                ->helperText('Payment received at order creation')
                                ->live()
                                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                    $total = $get('order_total') ?? 0;
                                    $set('balance_preview', '$' . number_format($total - $state, 2));
                                }),

                            Forms\Components\Placeholder::make('balance_preview')
                                ->label('Balance Due')
                                ->content(fn (Forms\Get $get): string =>
                                    '$' . number_format(($get('order_total') ?? 0) - ($get('payments') ?? 0), 2)
                                ),

                            Forms\Components\TextInput::make('order_counts')
                                ->label('Item Count')
                                ->numeric()
                                ->default(1)
                                ->helperText('Number of items in this order'),
                        ])
                        ->columns(2),

                    Step::make('Review')
                        ->icon('heroicon-o-check-circle')
                        ->description('Review and confirm')
                        ->schema([
                            Forms\Components\Placeholder::make('review_summary')
                                ->label('Order Summary')
                                ->content(function (Forms\Get $get): string {
                                    $userId = $get('user_id');
                                    $user = $userId ? User::find($userId) : null;

                                    $lines = [
                                        "Job ID: " . ($get('job_id') ?? 'N/A'),
                                        "Customer: " . ($user?->email ?? 'Not selected'),
                                        "Order Total: $" . number_format($get('order_total') ?? 0, 2),
                                        "Initial Payment: $" . number_format($get('payments') ?? 0, 2),
                                        "Balance Due: $" . number_format(($get('order_total') ?? 0) - ($get('payments') ?? 0), 2),
                                    ];

                                    return implode("\n", $lines);
                                })
                                ->columnSpanFull(),
                        ]),
                ])
                ->submitAction(view('filament.forms.components.wizard-submit-button'))
                ->columnSpanFull(),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure required fields have values
        $data['edg'] = $data['edg'] ?? 0;
        $data['order_counts'] = $data['order_counts'] ?? 1;
        $data['payments'] = $data['payments'] ?? 0;
        $data['order_total'] = $data['order_total'] ?? 0;
        $data['estimate_id'] = $data['estimate_id'] ?? '';

        // Remove non-database fields
        unset($data['description']);
        unset($data['balance_preview']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // If a description was provided, create a note
        $description = $this->data['description'] ?? null;
        if ($description) {
            $this->record->notes()->create([
                'text' => $description,
                'date' => now(),
                'author_id' => auth()->id() ?? 0,
                'type' => 0,
                'removed' => 0,
                'request_id' => 0,
                'company_id' => $this->record->company_id ?? 0,
                'type_user' => 0,
                'required_uid' => 0,
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
