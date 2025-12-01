<?php

namespace App\Filament\Widgets;

use App\Models\Job;
use App\Models\PaymentHistory;
use App\Models\SampleRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class DashboardStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $today = Carbon::today();

        // Revenue today - sum of payments made today (using 'date' and 'summ' fields)
        $revenueToday = PaymentHistory::whereDate('date', $today)
            ->active()
            ->sum('summ');

        // Jobs count - total active jobs
        $totalJobs = Job::count();

        // Active jobs (in production/proofing etc)
        $activeJobs = Job::whereIn('status', ['Production', 'Pre-Production', 'Proofing', 'Waiting', 'Review', 'On Hold'])
            ->count();

        // Pending requests - sample requests in pending status (status = 0)
        $pendingRequests = SampleRequest::pending()->count();

        // Orders with balance due (not fully paid)
        $ordersWithBalance = Job::whereRaw('order_total > payments')->count();

        // Weekly revenue trend for sparkline
        $weeklyRevenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weeklyRevenue[] = (float) PaymentHistory::whereDate('date', $date)
                ->active()
                ->sum('summ');
        }

        // Last week's revenue for comparison
        $lastWeekRevenue = PaymentHistory::whereBetween('date', [
            Carbon::today()->subDays(13),
            Carbon::today()->subDays(7),
        ])->active()->sum('summ');

        $thisWeekRevenue = PaymentHistory::whereBetween('date', [
            Carbon::today()->subDays(6),
            Carbon::today(),
        ])->active()->sum('summ');

        $percentChange = $lastWeekRevenue > 0
            ? round((($thisWeekRevenue - $lastWeekRevenue) / $lastWeekRevenue) * 100, 1)
            : 0;

        // Get yesterday's revenue for additional context
        $yesterdayRevenue = PaymentHistory::whereDate('date', Carbon::yesterday())
            ->active()
            ->sum('summ');

        $todayVsYesterday = $yesterdayRevenue > 0
            ? round((($revenueToday - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
            : ($revenueToday > 0 ? 100 : 0);

        return [
            Stat::make('Revenue Today', '$' . number_format($revenueToday, 2))
                ->description($percentChange >= 0 ? "+{$percentChange}% vs last week" : "{$percentChange}% vs last week")
                ->descriptionIcon($percentChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($percentChange >= 0 ? 'success' : 'danger')
                ->chart($weeklyRevenue)
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition',
                ])
                ->url(route('filament.admin.resources.payment-histories.index', [
                    'tableFilters[date][date_from]' => $today->format('Y-m-d'),
                    'tableFilters[date][date_until]' => $today->format('Y-m-d'),
                ])),

            Stat::make('Total Jobs', number_format($totalJobs))
                ->description($activeJobs . ' active')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition',
                ])
                ->url(route('filament.admin.resources.jobs.index')),

            Stat::make('Pending Requests', (string) $pendingRequests)
                ->description('Awaiting processing')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition',
                ])
                ->url(route('filament.admin.resources.sample-requests.index', [
                    'tableFilters[status][value]' => '0',
                ])),

            Stat::make('Orders With Balance', (string) $ordersWithBalance)
                ->description('Awaiting payment')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition',
                ])
                ->url(route('filament.admin.resources.jobs.index', [
                    'tableFilters[with_balance][isActive]' => true,
                ])),
        ];
    }
}
