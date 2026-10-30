<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Categories';

    protected static ?string $modelLabel = 'category';

    protected static ?string $pluralModelLabel = 'categories';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('parent_id')
                    ->label('Parent category')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')->columnSpanFull(),
                Forms\Components\TextInput::make('icon')->label('FontAwesome icon class')->placeholder('fa-solid fa-shirt'),
                Forms\Components\Placeholder::make('current_thumbnail')
                    ->label('Current thumbnail')
                    ->visible(fn (?Category $record) => (bool) $record?->thumb_url)
                    ->content(fn (?Category $record) => new \Illuminate\Support\HtmlString('<img src="'.e($record?->thumb_url).'" alt="" style="width:72px;height:72px;object-fit:cover;border-radius:16px">')),
                Forms\Components\FileUpload::make('image')
                    ->label('Thumbnail image')
                    ->helperText('Square or 4:3 image (min. 400 x 400 px), JPG, PNG, WebP or SVG, max 2 MB. Optional: without it the icon is shown. Uploading a new image replaces the current one.')
                    ->disk('uploads')
                    ->directory('categories')
                    ->visibility('public')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios(['1:1', '4:3'])
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                    ->maxSize(2048)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                Forms\Components\Section::make('Translations')->columnSpanFull()->columns(3)->schema([
                    Forms\Components\TextInput::make('translations.en.name')->label('Name (EN)'), Forms\Components\TextInput::make('translations.fr.name')->label('Name (FR)'), Forms\Components\TextInput::make('translations.ko.name')->label('Name (KO)'),
                    Forms\Components\Textarea::make('translations.en.description')->label('Description (EN)'), Forms\Components\Textarea::make('translations.fr.description')->label('Description (FR)'), Forms\Components\Textarea::make('translations.ko.description')->label('Description (KO)'),
                ]),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Thumbnail')
                    ->getStateUsing(fn (Category $record) => $record->thumb_url)
                    ->circular()
                    ->defaultImageUrl(asset('images/placeholder.svg')),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('Products')
                    ->state(fn (Category $record) => $record->products()->count() + \App\Models\Product::whereIn('category_id', $record->children()->select('id'))->count()),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
