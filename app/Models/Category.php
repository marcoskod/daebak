<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = ['parent_id', 'name', 'slug', 'description', 'translations', 'icon', 'image', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'translations' => 'array'];
    }

    public function getLocalizedNameAttribute(): string
    {
        return loc($this->translations, 'name', $this->name);
    }

    public function getLocalizedDescriptionAttribute(): string
    {
        return loc($this->translations, 'description', (string) $this->description);
    }

    /** Chemin relatif à public/ (formes acceptées : « images/… » legacy, « uploads/categories/… », URL http(s)). */
    public static function normalizeImagePath(?string $path): ?string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return null;
        }

        return preg_match('#^(https?:)?//#i', $path) ? $path : ltrim($path, '/');
    }

    /** Valeur du champ FileUpload (disque « uploads », relatif à public/uploads) -> colonne (relatif à public/). */
    public static function imageFromUpload(mixed $value): ?string
    {
        $value = is_array($value) ? (reset($value) ?: null) : $value;
        $value = self::normalizeImagePath(is_string($value) ? $value : null);
        if ($value === null || preg_match('#^(https?:)?//#i', $value) || str_starts_with($value, 'uploads/') || str_starts_with($value, 'images/')) {
            return $value;
        }

        return 'uploads/'.$value;
    }

    /** Colonne -> valeur du FileUpload ; null si l'image n'est pas dans public/uploads (legacy/URL : conservée telle quelle). */
    public static function imageToUpload(?string $path): ?string
    {
        $path = self::normalizeImagePath($path);

        return $path !== null && str_starts_with($path, 'uploads/') ? substr($path, 8) : null;
    }

    public function hasImage(): bool
    {
        return self::normalizeImagePath($this->image) !== null;
    }

    public function getImageUrlAttribute(): string
    {
        $path = self::normalizeImagePath($this->image);

        return $path ? asset($path) : asset('images/placeholder.svg');
    }

    /** URL de la miniature, ou null quand la catégorie n'a pas d'image (fallback icône Font Awesome). */
    public function getThumbUrlAttribute(): ?string
    {
        $path = self::normalizeImagePath($this->image);

        return $path ? asset($path) : null;
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
