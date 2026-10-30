<?php

namespace App\Filament\Widgets;

use App\Models\BlogPost;
use App\Models\ChatConversation;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ShopStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Shop overview';

    protected function getColumns(): int
    {
        return 4;
    }

    /** Valeurs journalières sur 7 jours (pour les mini-courbes). */
    protected function daily(string $expr, bool $paidOnly = false): array
    {
        $days = collect(range(6, 0))->map(fn ($i) => today()->subDays($i));
        $q = Order::query()->where('created_at', '>=', $days->first());
        if ($paidOnly) {
            $q->where('payment_status', 'paid');
        }
        $rows = $q->selectRaw("DATE(created_at) as d, $expr as v")->groupBy('d')->pluck('v', 'd');

        return $days->map(fn ($d) => (float) ($rows[$d->toDateString()] ?? 0))->all();
    }

    protected function getStats(): array
    {
        $revenue = (float) Order::where('payment_status', 'paid')->sum('total');
        $ordersToday = Order::whereDate('created_at', today())->count();
        $pending = Order::whereIn('status', ['pending', 'processing'])->count();
        $aov = (float) Order::where('payment_status', 'paid')->avg('total');
        $lowStock = Product::where('is_active', true)->whereColumn('stock_quantity', '<=', 'stock_alert_threshold')->count();

        return [
            Stat::make('Revenue (USD)', '$'.number_format($revenue, 2))->description('Paid orders · avg. basket $'.number_format($aov, 2))
                ->descriptionIcon('heroicon-m-banknotes')->chart($this->daily('SUM(total)', true))->color('success'),
            Stat::make('Orders today', $ordersToday)->description($pending.' in progress')
                ->descriptionIcon('heroicon-m-shopping-bag')->chart($this->daily('COUNT(*)'))->color($pending > 0 ? 'warning' : 'success'),
            Stat::make('Live chat waiting', ChatConversation::where('status', 'waiting_agent')->count())->description(ContactMessage::where('status', 'new')->count().' unread contact messages')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')->color('info'),
            Stat::make('Catalogue', Product::where('is_active', true)->count().' products')->description($lowStock.' low stock · '.BlogPost::published()->count().' articles live')
                ->descriptionIcon('heroicon-m-cube')->color($lowStock > 0 ? 'danger' : 'gray'),
        ];
    }
}
