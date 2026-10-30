<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Revenue & orders — last 14 days';

    protected int|string|array $columnSpan = 1;

    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $days = collect(range(13, 0))->map(fn (int $i) => today()->subDays($i));

        $rows = Order::query()->where('created_at', '>=', $days->first())
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c, SUM(CASE WHEN payment_status = "paid" THEN total ELSE 0 END) as r')
            ->groupBy('d')->get()->keyBy('d');

        return [
            'datasets' => [
                ['type' => 'bar', 'label' => 'Revenue (USD)', 'data' => $days->map(fn ($d) => round((float) ($rows[$d->toDateString()]->r ?? 0), 2))->all(), 'backgroundColor' => '#fed7ca', 'borderRadius' => 8, 'yAxisID' => 'y'],
                ['type' => 'line', 'label' => 'Orders', 'data' => $days->map(fn ($d) => (int) ($rows[$d->toDateString()]->c ?? 0))->all(), 'borderColor' => '#c8553d', 'backgroundColor' => '#c8553d', 'tension' => .35, 'pointRadius' => 3, 'yAxisID' => 'y1'],
            ],
            'labels' => $days->map(fn ($d) => $d->format('M j'))->all(),
        ];
    }

    protected function getOptions(): array
    {
        return ['scales' => ['y' => ['beginAtZero' => true, 'grid' => ['color' => 'rgba(128,128,128,.12)']], 'y1' => ['position' => 'right', 'beginAtZero' => true, 'grid' => ['drawOnChartArea' => false], 'ticks' => ['precision' => 0]], 'x' => ['grid' => ['display' => false]]], 'plugins' => ['legend' => ['position' => 'bottom']]];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
