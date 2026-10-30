<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopProducts extends BaseWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Top products';

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query()->where('is_active', true)->orderByDesc('reviews_count')->limit(6))
            ->paginated(false)
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('')->getStateUsing(fn (Product $r) => $r->image_url)->height(44)->extraImgAttributes(['class' => 'rounded-xl object-cover']),
                Tables\Columns\TextColumn::make('name')->label('Product')->limit(34)->weight('bold')->description(fn (Product $r) => $r->category?->name),
                Tables\Columns\TextColumn::make('rating')->icon('heroicon-s-star')->iconColor('warning')->description(fn (Product $r) => $r->reviews_count.' reviews'),
                Tables\Columns\TextColumn::make('price')->prefix('$')->numeric(2),
            ])
            ->recordUrl(fn (Product $r) => \App\Filament\Resources\ProductResource::getUrl('edit', ['record' => $r]));
    }
}
