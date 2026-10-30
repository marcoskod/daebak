<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Articles commandés';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_name')
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Produit'),
                Tables\Columns\TextColumn::make('product_sku')
                    ->label('SKU'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Prix unitaire')
                    ->numeric()
                    ->suffix(' USD'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantité'),
                Tables\Columns\TextColumn::make('line_total')
                    ->label('Total ligne')
                    ->numeric()
                    ->suffix(' USD'),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
