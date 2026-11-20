<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\OrderItem;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $revenue = Order::query()->whereNotIn('status', ['cancelled'])->sum('total');
        $topProduct = OrderItem::query()
            ->selectRaw('product_name, SUM(quantity) AS units_sold')
            ->groupBy('product_name')
            ->orderByDesc('units_sold')
            ->value('product_name') ?? 'No sales yet';

        return [
            Stat::make('Orders', Order::query()->count())->description('All customer orders'),
            Stat::make('Revenue', 'PKR '.number_format($revenue))->description('Excludes cancelled orders'),
            Stat::make('Needs attention', Order::query()->whereIn('status', ['pending', 'confirmed'])->count())->description('Pending or being prepared'),
            Stat::make('Top product', $topProduct)->description('By units ordered'),
        ];
    }
}
