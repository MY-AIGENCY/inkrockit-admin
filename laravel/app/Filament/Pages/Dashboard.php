<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    public function getHeaderWidgetsColumns(): int|array
    {
        return 4;
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\DashboardStats::class,
            \App\Filament\Widgets\RevenueChart::class,
            \App\Filament\Widgets\UrgentJobs::class,
        ];
    }
}
