<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    public const CATEGORIES = ['culture', 'guides', 'creators', 'studio', 'news'];

    protected $fillable = [
        'slug', 'translations', 'category', 'cover', 'template', 'author', 'tags', 'products', 'reading_minutes',
        'is_featured', 'is_published', 'published_at', 'views',
    ];

    protected function casts(): array
    {
        return [
            'translations' => 'array', 'tags' => 'array', 'products' => 'array',
            'is_featured' => 'boolean', 'is_published' => 'boolean', 'published_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)->where(fn ($w) => $w->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    /** Clé de template effective : retombe sur « classic » si absente ou inconnue. */
    public function getTemplateAttribute(?string $value): string
    {
        return static::resolveTemplate($value);
    }

    public static function resolveTemplate(?string $key): string
    {
        $default = config('blog_templates.default', 'classic');

        return $key && array_key_exists($key, config('blog_templates.templates', [])) ? $key : $default;
    }

    /** Template à afficher : ?tpl= (aperçu, admins connectés uniquement) > réglage de l'article > classic. */
    public function templateFor(?string $override = null): string
    {
        if ($override && auth()->user()?->is_admin && array_key_exists($override, config('blog_templates.templates', []))) {
            return $override;
        }

        return $this->template;
    }

    public function getTitleAttribute(): string
    {
        return loc($this->translations, 'title', $this->slug);
    }

    public function getExcerptAttribute(): string
    {
        return loc($this->translations, 'excerpt', '');
    }

    public function getContentAttribute(): string
    {
        return loc($this->translations, 'content', '');
    }

    public function getCoverUrlAttribute(): string
    {
        if (! $this->cover) {
            return asset('images/placeholder.svg');
        }

        return str_starts_with($this->cover, 'http') ? $this->cover : asset($this->cover);
    }

    /** Temps de lecture : valeur saisie, sinon estimé (≈ 200 mots/min, ≈ 500 caractères/min pour le coréen). */
    public function getReadingTimeAttribute(): int
    {
        if ($this->reading_minutes) {
            return $this->reading_minutes;
        }
        $text = strip_tags($this->content);
        $units = app()->getLocale() === 'ko' ? mb_strlen($text) / 450 : str_word_count($text) / 200;

        return max(1, (int) ceil($units));
    }

    public function relatedProducts()
    {
        return Product::query()->with('category')->where('is_active', true)->whereIn('slug', $this->products ?? [])->get();
    }
}
