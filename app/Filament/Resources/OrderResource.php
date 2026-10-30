<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Orders';

    protected static ?string $modelLabel = 'order';

    protected static ?string $pluralModelLabel = 'orders';

    public static function statusOptions(): array
    {
        return collect(Order::STATUSES)->mapWithKeys(fn ($s) => [$s => ucfirst($s === 'processing' ? 'in production' : $s)])->all();
    }

    public static function paymentOptions(): array
    {
        return ['card' => 'Credit / debit card', 'paypal' => 'PayPal'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Order #')
                            ->required()
                            ->maxLength(30)
                            ->disabledOn('edit'),
                        Forms\Components\Select::make('user_id')
                            ->label('Customer account')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->default(null),
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Customer name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(255)
                            ->default(null),
                    ]),

                Forms\Components\Section::make('Shipping')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('shipping_address')
                            ->label('Address')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('shipping_country')
                            ->label('Country (ISO)')
                            ->maxLength(2),
                        Forms\Components\TextInput::make('shipping_city')
                            ->label('City')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('shipping_postal_code')
                            ->label('Postal code')
                            ->maxLength(255)
                            ->default(null),
                    ]),

                Forms\Components\Section::make('Payment & status')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->required()
                            ->numeric()
                            ->suffix('USD'),
                        Forms\Components\TextInput::make('shipping_cost')
                            ->label('Livraison')
                            ->required()
                            ->numeric()
                            ->suffix('USD')
                            ->default(0),
                        Forms\Components\TextInput::make('total')
                            ->label('Total')
                            ->required()
                            ->numeric()
                            ->suffix('USD'),
                        Forms\Components\Select::make('status')
                            ->label('Order status')
                            ->options(static::statusOptions())
                            ->required()
                            ->default('pending'),
                        Forms\Components\Select::make('payment_method')
                            ->label('Payment method')
                            ->options(static::paymentOptions())
                            ->required()
                            ->default('card'),
                        Forms\Components\Select::make('payment_status')
                            ->label('Payment status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                            ])
                            ->required()
                            ->default('pending'),
                    ]),

                Forms\Components\Textarea::make('notes')
                    ->label('Customer note')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->description(fn (Order $record) => $record->customer_email),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->numeric()
                    ->suffix(' USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => static::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'info',
                        'shipped' => 'warning',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Method')
                    ->formatStateUsing(fn (string $state) => static::paymentOptions()[$state] ?? $state),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        default => 'Pending',
                    })
                    ->color(fn (string $state) => match ($state) {
                        'paid' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(static::statusOptions()),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Payment')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count() ?: null;
    }
}
