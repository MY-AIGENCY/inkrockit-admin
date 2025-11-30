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

        // Pending requests - sample requests in pending status (status = 0)
        $pendingRequests = SampleRequest::pending()->count();

        // Active/In Production - jobs with balance due (not fully paid)
        $activeProduction = Job::whereRaw('order_total > payments')->count();

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

        return [
            Stat::make('Revenue Today', '$' . number_format($revenueToday, 2))
                ->description($percentChange >= 0 ? "+{$percentChange}% vs last week" : "{$percentChange}% vs last week")
                ->descriptionIcon($percentChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($percentChange >= 0 ? 'success' : 'danger')
                ->chart($weeklyRevenue)
                ->url(route('filament.admin.resources.payment-histories.index')),

            Stat::make('Total Jobs', number_format($totalJobs))
                ->description('All orders')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('warning')
                ->url(route('filament.admin.resources.jobs.index')),

            Stat::make('Pending Requests', (string) $pendingRequests)
                ->description('Awaiting processing')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info')
                ->url(route('filament.admin.resources.sample-requests.index')),

            Stat::make('With Balance', (string) $activeProduction)
                ->description('Unpaid orders')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary')
                ->url(route('filament.admin.resources.jobs.index')),
        ];
    }
}
