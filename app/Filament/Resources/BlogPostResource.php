<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Blog';

    protected static ?string $modelLabel = 'article';

    protected static ?string $pluralModelLabel = 'articles';

    public static function form(Form $form): Form
    {
        $locales = ['en' => 'English', 'fr' => 'Français', 'ko' => '한국어'];

        return $form->schema([
            Forms\Components\Section::make('Choose a template')->icon('heroicon-o-swatch')->columnSpanFull()
                ->description('Pick the layout of the public article page. You can preview any of them with ?tpl=name while logged in as admin.')
                ->schema([
                    Forms\Components\ViewField::make('template')->label('Template')->hiddenLabel()->view('filament.forms.blog-template-picker')
                        ->default('classic')->required()->rule(Rule::in(array_keys(config('blog_templates.templates')))),
                ]),
            Forms\Components\Tabs::make('Post')->columnSpanFull()->tabs([
                Forms\Components\Tabs\Tab::make('Content')->icon('heroicon-o-language')->schema([
                    Forms\Components\Tabs::make('Languages')->tabs(collect($locales)->map(fn ($label, $code) => Forms\Components\Tabs\Tab::make($label)->schema([
                        Forms\Components\TextInput::make("translations.$code.title")->label('Title')->required($code === 'en')->maxLength(180)->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state, Forms\Get $get) => ($code === 'en' && ! $get('slug')) ? $set('slug', Str::slug((string) $state)) : null),
                        Forms\Components\Textarea::make("translations.$code.excerpt")->label('Excerpt')->rows(2)->maxLength(300),
                        Forms\Components\Textarea::make("translations.$code.content")->label('Article')->rows(18)->required($code === 'en')
                            ->helperText('Syntax: "## Section title" (adds an anchor in the table of contents), "- bullet", "> quote", **bold**, *italic*.'),
                    ]))->values()->all()),
                ]),
                Forms\Components\Tabs\Tab::make('Settings')->icon('heroicon-o-cog-6-tooth')->columns(2)->schema([
                    Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(190),
                    Forms\Components\Select::make('category')->options(collect(BlogPost::CATEGORIES)->mapWithKeys(fn ($c) => [$c => ucfirst($c)])->all())->required()->default('culture'),
                    Forms\Components\TextInput::make('cover')->label('Cover image (path or URL, 1200×630)')->placeholder('images/blog/my-post.svg'),
                    Forms\Components\TextInput::make('author')->maxLength(80),
                    Forms\Components\TagsInput::make('tags'),
                    Forms\Components\Select::make('products')->label('Linked products')->multiple()->searchable()
                        ->options(fn () => Product::orderBy('name')->pluck('name', 'slug')->all()),
                    Forms\Components\TextInput::make('reading_minutes')->numeric()->helperText('Leave empty to estimate automatically.'),
                    Forms\Components\DateTimePicker::make('published_at')->label('Publish at')->default(now()),
                    Forms\Components\Toggle::make('is_published')->label('Published')->default(false),
                    Forms\Components\Toggle::make('is_featured')->label('Featured on the blog home'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('cover')->label('')->getStateUsing(fn (BlogPost $r) => $r->cover_url)->height(40)->extraImgAttributes(['class' => 'rounded object-cover']),
                Tables\Columns\TextColumn::make('title')->label('Title')->getStateUsing(fn (BlogPost $r) => $r->translations['en']['title'] ?? $r->slug)->searchable(query: fn ($q, $s) => $q->where('translations', 'like', "%{$s}%"))->description(fn (BlogPost $r) => $r->author),
                Tables\Columns\TextColumn::make('category')->badge(),
                Tables\Columns\TextColumn::make('template')->badge()->color('info')->toggleable(),
                Tables\Columns\IconColumn::make('is_featured')->label('Featured')->boolean(),
                Tables\Columns\IconColumn::make('is_published')->label('Live')->boolean(),
                Tables\Columns\TextColumn::make('views')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('published_at')->dateTime('Y-m-d')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')->options(collect(BlogPost::CATEGORIES)->mapWithKeys(fn ($c) => [$c => ucfirst($c)])->all()),
                Tables\Filters\TernaryFilter::make('is_published')->label('Published'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')->label('View')->icon('heroicon-o-eye')->url(fn (BlogPost $r) => route('blog.show', $r->slug))->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
