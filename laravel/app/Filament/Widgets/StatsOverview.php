<?php

namespace App\Filament\Widgets;

use App\Models\Company;
use App\Models\Job;
use App\Models\PaymentHistory;
use App\Models\SampleRequest;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', number_format(User::count()))
                ->description('Registered accounts')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8]),

            Stat::make('Total Companies', number_format(Company::notDuplicate()->count()))
                ->description('Active companies')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('success'),

            Stat::make('Total Jobs', number_format(Job::count()))
                ->description('All time orders')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('warning'),

            Stat::make('Pending Requests', number_format(SampleRequest::pending()->count()))
                ->description('Awaiting processing')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),

            Stat::make('Admin/Staff', number_format(User::adminUsers()->count()))
                ->description('Team members')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Active Payments', number_format(PaymentHistory::active()->count()))
                ->description('Payment records')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('success'),
        ];
    }
}
