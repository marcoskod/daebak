<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'sku', 'external_id', 'description', 'translations', 'product_type',
        'provider', 'provider_sku', 'variants', 'tags', 'motif', 'badge', 'rating', 'reviews_count',
        'production_min', 'production_max', 'description_template',
        'price', 'sale_price', 'stock_quantity', 'stock_alert_threshold', 'image', 'images', 'keywords', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'rating' => 'decimal:1',
            'stock_quantity' => 'integer',
            'stock_alert_threshold' => 'integer',
            'translations' => 'array',
            'variants' => 'array',
            'tags' => 'array',
            'images' => 'array',
            'keywords' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ---- Contenu localisé ------------------------------------------------------------

    public function getLocalizedNameAttribute(): string
    {
        return loc($this->translations, 'name', $this->name);
    }

    public function getLocalizedShortAttribute(): string
    {
        return loc($this->translations, 'short', '');
    }

    public function getLocalizedDescriptionAttribute(): string
    {
        return loc($this->translations, 'description', (string) $this->description);
    }

    /** Template de description effectif : ?tpl= (aperçu) > réglage du produit > défaut du site. */
    public function descriptionTemplate(?string $override = null): string
    {
        $keys = array_keys(config('description_templates.templates'));

        foreach ([$override, $this->description_template, config('site.description_templates.default')] as $candidate) {
            if ($candidate && in_array($candidate, $keys, true)) {
                return $candidate;
            }
        }

        return 'classic';
    }

    // ---- Prix / variantes -----------------------------------------------------------------

    public function getCurrentPriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->price);
    }

    public function getDiscountPercentAttribute(): int
    {
        return $this->sale_price && $this->price > 0 ? (int) round((1 - $this->sale_price / $this->price) * 100) : 0;
    }

    public function sizes(): array
    {
        return $this->variants['sizes'] ?? [];
    }

    public function colors(): array
    {
        return $this->variants['colors'] ?? [];
    }

    /** Image du coloris demandé (ou image principale). */
    public function imageForColor(?string $color): string
    {
        foreach ($this->colors() as $c) {
            if ($c['key'] === $color && ! empty($c['image'])) {
                return asset($c['image']);
            }
        }

        return $this->image_url;
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return str_starts_with($this->image, 'http') ? $this->image : asset($this->image);
        }

        return asset('images/placeholder.svg');
    }

    public function getGalleryAttribute(): array
    {
        $images = $this->images ?: ($this->image ? [$this->image] : []);

        return array_map(fn ($p) => str_starts_with($p, 'http') ? $p : asset($p), $images) ?: [asset('images/placeholder.svg')];
    }

    public function providerInfo(): ?array
    {
        return $this->provider ? config("site.providers.{$this->provider}") : null;
    }

    // ---- Stock (produits à la demande : stock « virtuel » élevé) -----------------------------

    public function decrementStock(int $qty): void
    {
        $this->decrement('stock_quantity', $qty);
    }

    public function adjustStock(string $type, int $quantity, ?string $reason = null): StockMovement
    {
        return DB::transaction(function () use ($type, $quantity, $reason) {
            $movement = $this->stockMovements()->create([
                'type' => $type,
                'quantity' => $quantity,
                'reason' => $reason,
                'user_id' => Auth::id(),
                'created_at' => now(),
            ]);

            $this->increment('stock_quantity', $type === 'in' ? $quantity : -$quantity);

            return $movement;
        });
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->stock_alert_threshold;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }
}
