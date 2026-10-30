<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Latest orders';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest()->limit(8))
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('Order #')->weight('bold'),
                Tables\Columns\TextColumn::make('customer_name')->label('Customer'),
                Tables\Columns\TextColumn::make('total')->label('Total')->formatStateUsing(fn ($state) => '$'.number_format((float) $state, 2)),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => \App\Filament\Resources\OrderResource::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed'][$state] ?? $state)
                    ->color(fn (string $state) => ['pending' => 'warning', 'paid' => 'success', 'failed' => 'danger'][$state] ?? 'gray'),
                Tables\Columns\TextColumn::make('created_at')->label('Date')->dateTime('Y-m-d H:i'),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (Order $record) => OrderResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
