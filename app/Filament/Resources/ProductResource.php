<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\StockMovementsRelationManager;
use App\Models\Product;
use App\Support\ProductDescription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Products';

    protected static ?string $modelLabel = 'product';

    protected static ?string $pluralModelLabel = 'products';

    /** Options du sélecteur de template : « Nom — description » localisés en anglais côté admin. */
    public static function templateOptions(): array
    {
        $out = [];
        foreach (ProductDescription::templates() as $key => $tpl) {
            $out[$key] = $tpl['label']['en'];
        }

        return $out;
    }

    public static function templateDescriptions(): array
    {
        $out = [];
        foreach (ProductDescription::templates() as $key => $tpl) {
            $out[$key] = $tpl['hint']['en'];
        }

        return $out;
    }

    public static function form(Form $form): Form
    {
        $locales = ['en' => 'English', 'fr' => 'Français', 'ko' => '한국어'];

        return $form->schema([
            Forms\Components\Tabs::make('Product')->columnSpanFull()->tabs([
                Forms\Components\Tabs\Tab::make('General')->icon('heroicon-o-information-circle')->columns(2)->schema([
                    Forms\Components\Select::make('category_id')->label('Category')->relationship('category', 'name')->searchable()->preload()->required(),
                    Forms\Components\Select::make('product_type')->label('Product type')->options(array_combine(
                        ['tee', 'hoodie', 'cap', 'mug', 'tumbler', 'cushion', 'poster', 'canvas', 'tote', 'phone', 'stickers', 'notebook', 'deskmat', 'keychain'],
                        ['T-shirt', 'Hoodie', 'Cap', 'Mug', 'Tumbler', 'Cushion', 'Poster', 'Canvas', 'Tote bag', 'Phone case', 'Stickers', 'Notebook', 'Desk mat', 'Keychain'],
                    ))->searchable(),
                    Forms\Components\TextInput::make('name')->label('Name (default / English)')->required()->maxLength(255)->live(onBlur: true)
                        ->afterStateUpdated(fn (Forms\Set $set, ?string $state, Forms\Get $get) => $get('slug') ? null : $set('slug', Str::slug((string) $state))),
                    Forms\Components\TextInput::make('slug')->required()->maxLength(255),
                    Forms\Components\TextInput::make('sku')->label('SKU')->maxLength(255),
                    Forms\Components\Toggle::make('is_active')->label('Active')->default(true),
                    Forms\Components\Select::make('badge')->options(['bestseller' => 'Bestseller', 'new' => 'New', 'limited' => 'Limited', 'sale' => 'Sale'])->placeholder('None'),
                    Forms\Components\TagsInput::make('tags')->label('Tags (motifs, themes — used by collections)'),
                ]),

                Forms\Components\Tabs\Tab::make('Content')->icon('heroicon-o-language')->schema([
                    Forms\Components\Tabs::make('Languages')->tabs(collect($locales)->map(fn ($label, $code) => Forms\Components\Tabs\Tab::make($label)->schema([
                        Forms\Components\TextInput::make("translations.$code.name")->label('Name'),
                        Forms\Components\TextInput::make("translations.$code.short")->label('Short pitch'),
                        Forms\Components\Textarea::make("translations.$code.description")->label('Description')->rows(14)
                            ->helperText('Syntax: "## Section title", "- bullet", "Label: value" lines (spec table), "> quote", **bold**, *italic*. The chosen template changes only the layout.'),
                    ]))->values()->all()),
                ]),

                Forms\Components\Tabs\Tab::make('Description layout')->icon('heroicon-o-swatch')->schema([
                    Forms\Components\Radio::make('description_template')->label('Template')
                        ->options(static::templateOptions())->descriptions(static::templateDescriptions())
                        ->default('classic')->columns(2)
                        ->helperText('Responsive layouts for the product description. Preview any of them on the live page with ?tpl=name.'),
                    Forms\Components\Placeholder::make('tpl_preview')->label('Preview')->content(function (?Product $record) {
                        if (! $record) {
                            return 'Save the product first, then open it on the shop with ?tpl=classic, ?tpl=editorial, ?tpl=cards, ?tpl=tabs, ?tpl=accordion, ?tpl=spec-sheet, ?tpl=timeline or ?tpl=showcase.';
                        }
                        $links = collect(static::templateOptions())->map(fn ($label, $key) => '<a href="'.route('shop.produit', [$record->slug, 'tpl' => $key]).'" target="_blank" style="text-decoration:underline;margin-right:12px">'.e($label).'</a>')->implode('');

                        return new HtmlString($links);
                    }),
                ]),

                Forms\Components\Tabs\Tab::make('Pricing & fulfilment')->icon('heroicon-o-banknotes')->columns(3)->schema([
                    Forms\Components\TextInput::make('price')->label('Price')->required()->numeric()->suffix('USD'),
                    Forms\Components\TextInput::make('sale_price')->label('Sale price')->numeric()->suffix('USD'),
                    Forms\Components\TextInput::make('stock_quantity')->label('Virtual stock (print on demand)')->required()->numeric()->default(9999),
                    Forms\Components\Select::make('provider')->label('Print provider')->options(collect(config('site.providers'))->map(fn ($p) => $p['name'])->all()),
                    Forms\Components\TextInput::make('provider_sku')->label('Provider catalogue ID / SKU'),
                    Forms\Components\TextInput::make('stock_alert_threshold')->label('Stock alert threshold')->numeric()->default(0),
                    Forms\Components\TextInput::make('production_min')->label('Production min (days)')->numeric()->default(2),
                    Forms\Components\TextInput::make('production_max')->label('Production max (days)')->numeric()->default(5),
                ]),

                Forms\Components\Tabs\Tab::make('Images & variants')->icon('heroicon-o-photo')->schema([
                    Forms\Components\TextInput::make('image')->label('Main image path or URL (e.g. images/products/…)'),
                    Forms\Components\TagsInput::make('images')->label('Gallery (paths or URLs)'),
                    Forms\Components\TagsInput::make('variants.sizes')->label('Sizes'),
                    Forms\Components\Repeater::make('variants.colors')->label('Colors')->columns(4)->collapsible()->schema([
                        Forms\Components\TextInput::make('key')->required()->helperText('lang key colors.{key}'),
                        Forms\Components\ColorPicker::make('hex')->required(),
                        Forms\Components\TextInput::make('image')->label('Mockup path / URL'),
                        Forms\Components\TextInput::make('name')->label('Label'),
                    ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('')->getStateUsing(fn (Product $record) => $record->image_url)->height(48)
                    ->extraImgAttributes(['loading' => 'lazy', 'class' => 'rounded-lg object-cover bg-white']),
                Tables\Columns\TextColumn::make('name')->searchable()->description(fn (Product $r) => $r->category?->name),
                Tables\Columns\TextColumn::make('product_type')->label('Type')->badge()->toggleable(),
                Tables\Columns\TextColumn::make('provider')->badge()->color('gray')->toggleable(),
                Tables\Columns\TextColumn::make('description_template')->label('Layout')->badge()->color('info')->placeholder('classic')->toggleable(),
                Tables\Columns\TextColumn::make('price')->numeric(2)->prefix('$')->sortable(),
                Tables\Columns\TextColumn::make('sale_price')->label('Sale')->numeric(2)->prefix('$')->placeholder('—')->sortable(),
                Tables\Columns\TextColumn::make('rating')->icon('heroicon-s-star')->iconColor('warning')->sortable()->toggleable(),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')->label('Category')->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('provider')->options(collect(config('site.providers'))->map(fn ($p) => $p['name'])->all()),
                Tables\Filters\SelectFilter::make('description_template')->label('Layout')->options(static::templateOptions()),
            ])
            ->actions([
                Tables\Actions\Action::make('adjustStock')->label('Adjust stock')->icon('heroicon-o-archive-box')->color('gray')
                    ->form([
                        Forms\Components\Select::make('type')->options(['in' => 'In (restock)', 'out' => 'Out (loss, adjustment…)'])->required()->default('in'),
                        Forms\Components\TextInput::make('quantity')->numeric()->minValue(1)->required(),
                        Forms\Components\TextInput::make('reason')->maxLength(255),
                    ])
                    ->action(function (Product $record, array $data): void {
                        if ($data['type'] === 'out' && $data['quantity'] > $record->stock_quantity) {
                            Notification::make()->title('Quantity exceeds stock ('.$record->stock_quantity.').')->danger()->send();

                            return;
                        }
                        $record->adjustStock($data['type'], (int) $data['quantity'], $data['reason'] ?? null);
                        Notification::make()->title('Stock movement saved.')->success()->send();
                    }),
                Tables\Actions\Action::make('view')->label('View')->icon('heroicon-o-eye')->url(fn (Product $r) => route('shop.produit', $r->slug))->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('setTemplate')->label('Set description layout')->icon('heroicon-o-swatch')
                        ->form([Forms\Components\Select::make('tpl')->label('Template')->options(static::templateOptions())->required()])
                        ->action(function ($records, array $data) {
                            $records->each->update(['description_template' => $data['tpl']]);
                            Notification::make()->title('Layout applied to '.$records->count().' products.')->success()->send();
                        })->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [StockMovementsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
