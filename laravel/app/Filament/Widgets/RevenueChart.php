<?php

namespace App\Filament\Widgets;

use App\Models\PaymentHistory;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue Overview';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 2;

    protected static ?string $maxHeight = '300px';

    public ?string $filter = '7';

    protected function getFilters(): ?array
    {
        return [
            '7' => 'Last 7 days',
            '30' => 'Last 30 days',
            '90' => 'Last 90 days',
        ];
    }

    protected function getData(): array
    {
        $days = (int) $this->filter;

        $data = [];
        $labels = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format($days <= 7 ? 'D' : 'M j');

            $revenue = PaymentHistory::whereDate('date', $date)
                ->active()
                ->sum('summ');

            $data[] = (float) $revenue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data,
                    'backgroundColor' => array_map(
                        fn($i) => $i === count($data) - 1 ? 'rgba(79, 70, 229, 0.8)' : 'rgba(199, 210, 254, 0.8)',
                        range(0, count($data) - 1)
                    ),
                    'borderColor' => array_map(
                        fn($i) => $i === count($data) - 1 ? 'rgb(79, 70, 229)' : 'rgb(165, 180, 252)',
                        range(0, count($data) - 1)
                    ),
                    'borderWidth' => 1,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'drawBorder' => false,
                    ],
                    'ticks' => [
                        'callback' => "function(value) { return '$' + value.toLocaleString(); }",
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }
}
