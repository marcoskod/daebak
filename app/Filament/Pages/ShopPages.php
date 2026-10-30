<?php

namespace App\Filament\Pages;

use App\Models\PageSetting;
use App\Support\PageEditor;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/** Édition des pages de la boutique : textes (en/fr/ko), images, couleurs, fonds et bordures. */
class ShopPages extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Shop pages';

    protected static ?string $title = 'Shop pages';

    protected static ?string $slug = 'shop-pages';

    protected static string $view = 'filament.pages.shop-pages';

    public ?array $data = [];

    protected const LOCALES = ['en' => 'English', 'fr' => 'Français', 'ko' => '한국어'];

    public function mount(): void
    {
        $key = request()->query('page');
        $key = isset(PageEditor::pages()[$key]) ? $key : 'home';
        $this->data = ['page' => $key];   // le schéma du formulaire dépend de la page choisie
        $this->loadPage($key);
    }

    public function loadPage(string $key): void
    {
        $setting = PageSetting::query()->where('page_key', $key)->first();
        $images = [];
        foreach ($setting?->images ?? [] as $slot => $path) {
            $images[$slot] = Str::startsWith($path, 'uploads/') ? Str::after($path, 'uploads/') : null;
        }
        $styles = $setting?->styles ?? [];
        foreach ($styles as $target => $props) {
            if (is_array($props) && ! empty($props['background_image'])) {
                $styles[$target]['background_image'] = Str::after($props['background_image'], 'uploads/');
            }
        }
        $texts = [];
        foreach (self::LOCALES as $locale => $label) {
            foreach (Arr::get($setting?->texts ?? [], $locale, []) as $k => $v) {
                $texts[$locale][$k] = $v;
            }
        }

        $this->form->fill(['page' => $key, 'texts' => $texts, 'images' => $images, 'styles' => $styles]);
    }

    public function form(Form $form): Form
    {
        $page = $this->data['page'] ?? 'home';
        $def = PageEditor::pages()[$page] ?? PageEditor::pages()['home'];

        $tabs = [$this->textsTab($page)];
        if ($def['images']) {
            $tabs[] = $this->imagesTab($def['images']);
        }
        $tabs[] = $this->styleTab($page);

        return $form->schema([
            Select::make('page')->label('Page to edit')->options(collect(PageEditor::pages())->map(fn ($p) => $p['label'])->all())
                ->native(false)->selectablePlaceholder(false)->live()
                ->afterStateUpdated(fn (?string $state) => $this->redirect(static::getUrl(['page' => $state ?: 'home'])))
                ->helperText('Everything you change is stored as an override: clearing a field restores the original text, image or style.'),
            Tabs::make('Editor')->persistTabInQueryString('tab')->tabs($tabs),
        ])->statePath('data');
    }

    protected function textsTab(string $page): Tab
    {
        $localeTabs = [];
        foreach (self::LOCALES as $locale => $label) {
            $groups = [];
            foreach (PageEditor::editableTexts($page, $locale) as $key => $original) {
                $parts = explode('.', $key);
                $groups[count($parts) > 2 ? $parts[0].' › '.$parts[1] : $parts[0].' › general'][$key] = $original;
            }

            $sections = [];
            foreach ($groups as $group => $items) {
                $fields = [];
                foreach ($items as $key => $original) {
                    $name = "texts.$locale.".str_replace('.', '__', $key);
                    $long = mb_strlen($original) > 70 || str_contains($original, "\n");
                    $field = $long ? Textarea::make($name)->rows(mb_strlen($original) > 200 ? 4 : 2) : TextInput::make($name);
                    $fields[] = $field->label(Str::after($key, explode('.', $key)[0].'.'))->placeholder(Str::limit($original, 160))->hint($key)->maxLength(2000);
                }
                $sections[] = Section::make(Str::headline($group))->description(count($items).' texts')->collapsed()->compact()->schema($fields);
            }
            $localeTabs[] = Tab::make($label)->badge(count($groups) ? array_sum(array_map('count', $groups)) : null)->schema($sections ?: [Placeholder::make("none_$locale")->content('No editable text on this page.')]);
        }

        return Tab::make('Texts')->icon('heroicon-o-language')->schema([
            Placeholder::make('texts_help')->hiddenLabel()->content(new HtmlString('<span class="text-sm text-gray-500">The grey text is the current default. Type to replace it, leave empty to keep it. Each language is edited separately.</span>')),
            Tabs::make('Languages')->tabs($localeTabs),
        ]);
    }

    protected function imagesTab(array $slots): Tab
    {
        $fields = [];
        foreach ($slots as $slot => [$label, $default]) {
            $fields[] = FileUpload::make("images.$slot")->label($label)->disk('uploads')->directory('pages')->visibility('public')->image()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])->maxSize(4096)->imageEditor()
                ->helperText('Default: '.$default.' — JPG, PNG, WebP or SVG, 4 MB max. Remove the file to restore the default.');
        }

        return Tab::make('Images')->icon('heroicon-o-photo')->schema([Section::make('Page images')->columns(2)->schema($fields)]);
    }

    protected function styleTab(string $page): Tab
    {
        $sections = [];

        $vars = [];
        foreach (config('page_editor.vars') as $key => [$label]) {
            $vars[] = ColorPicker::make("styles.vars.$key")->label($label);
        }
        $sections[] = Section::make('Colors')->description($page === 'global' ? 'Applied on every page.' : 'Applied on this page only (light theme).')->columns(3)->schema($vars);

        $targets = $page === 'global' ? config('page_editor.global_targets') : config('page_editor.page_targets');
        foreach ($targets as $target) {
            $label = config("page_editor.targets.$target.0");
            $sections[] = Section::make($label)->collapsed()->compact()->columns(4)->schema([
                ColorPicker::make("styles.$target.background_color")->label('Background'),
                ColorPicker::make("styles.$target.background_color_2")->label('Gradient to (optional)'),
                ColorPicker::make("styles.$target.text_color")->label('Text color'),
                ColorPicker::make("styles.$target.border_color")->label('Border color'),
                TextInput::make("styles.$target.border_width")->label('Border width (px)')->numeric()->minValue(0)->maxValue(12),
                Select::make("styles.$target.border_style")->label('Border style')->options(array_combine(PageEditor::BORDER_STYLES, array_map('ucfirst', PageEditor::BORDER_STYLES)))->native(false),
                TextInput::make("styles.$target.radius")->label('Corner radius (px)')->numeric()->minValue(0)->maxValue(80),
                FileUpload::make("styles.$target.background_image")->label('Background image')->disk('uploads')->directory('pages')->visibility('public')->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])->maxSize(4096)->columnSpan(2),
            ]);
        }

        return Tab::make('Colors & style')->icon('heroicon-o-swatch')->schema($sections);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $page = $state['page'];
        abort_unless(isset(PageEditor::pages()[$page]), 404);

        // Textes : on ne garde que les valeurs saisies et différentes de l'original.
        $texts = [];
        foreach (self::LOCALES as $locale => $label) {
            $originals = PageEditor::editableTexts($page, $locale);
            foreach (Arr::get($state, "texts.$locale", []) as $name => $value) {
                $key = str_replace('__', '.', $name);
                $value = is_string($value) ? trim($value) : '';
                if ($value !== '' && isset($originals[$key]) && $value !== $originals[$key]) {
                    $texts[$locale][$name] = $value;
                }
            }
        }

        $images = [];
        foreach (Arr::get($state, 'images', []) as $slot => $path) {
            if ($path = is_array($path) ? Arr::first($path) : $path) {
                $images[$slot] = 'uploads/'.ltrim($path, '/');
            }
        }

        $styles = [];
        foreach (Arr::get($state, 'styles', []) as $target => $props) {
            $props = collect($props)->map(function ($v) {
                $v = is_array($v) ? Arr::first($v) : $v;

                return is_string($v) ? trim($v) : $v;
            })->filter(fn ($v) => $v !== null && $v !== '')->all();
            if (! empty($props['background_image'])) {
                $props['background_image'] = 'uploads/'.ltrim($props['background_image'], '/');
            }
            if ($props) {
                $styles[$target] = $props;
            }
        }

        if (! $texts && ! $images && ! $styles) {
            PageSetting::query()->where('page_key', $page)->get()->each->delete();
        } else {
            PageSetting::updateOrCreate(['page_key' => $page], ['texts' => $texts, 'images' => $images, 'styles' => $styles]);
        }

        Notification::make()->title('Page saved')->body('The changes are live on the shop.')->success()->send();
        $this->loadPage($page);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')->label('View page')->icon('heroicon-o-eye')->color('gray')
                ->url(fn () => url(PageEditor::pages()[$this->data['page'] ?? 'home']['url']))->openUrlInNewTab(),
            Action::make('reset')->label('Reset this page')->icon('heroicon-o-arrow-uturn-left')->color('danger')->requiresConfirmation()
                ->modalDescription('All custom texts, images and styles of this page will be removed and the original design restored.')
                ->action(function () {
                    $page = $this->data['page'] ?? 'home';
                    PageSetting::query()->where('page_key', $page)->get()->each->delete();
                    Notification::make()->title('Page restored to its defaults')->success()->send();
                    $this->loadPage($page);
                }),
        ];
    }
}
