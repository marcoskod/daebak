<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

/**
 * Panier en session. Une ligne = produit + taille + coloris (clé "id|taille|couleur").
 */
class Cart
{
    protected const SESSION_KEY = 'cart_v2';

    public static function content(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public static function key(int $productId, ?string $size, ?string $color): string
    {
        return $productId.'|'.($size ?? '').'|'.($color ?? '');
    }

    public static function add(int $productId, int $qty = 1, ?string $size = null, ?string $color = null): void
    {
        $cart = self::content();
        $key = self::key($productId, $size, $color);
        $line = $cart[$key] ?? ['id' => $productId, 'size' => $size, 'color' => $color, 'qty' => 0];
        $line['qty'] = min(20, $line['qty'] + $qty);
        $cart[$key] = $line;
        Session::put(self::SESSION_KEY, $cart);
    }

    public static function update(string $key, int $qty): void
    {
        $cart = self::content();
        if (! isset($cart[$key])) {
            return;
        }
        if ($qty <= 0) {
            unset($cart[$key]);
        } else {
            $cart[$key]['qty'] = min(20, $qty);
        }
        Session::put(self::SESSION_KEY, $cart);
    }

    public static function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public static function count(): int
    {
        return (int) array_sum(array_column(self::content(), 'qty'));
    }

    /** @return Collection<int, array> */
    public static function items(): Collection
    {
        $cart = self::content();
        if (! $cart) {
            return collect();
        }

        $products = Product::query()->whereIn('id', array_column($cart, 'id'))->get()->keyBy('id');

        return collect($cart)->map(function (array $line, string $key) use ($products) {
            $product = $products[$line['id']] ?? null;
            if (! $product || ! $product->is_active) {
                return null;
            }
            $unit = $product->current_price;
            $sizes = $product->sizes();
            $idx = $line['size'] ? array_search($line['size'], $sizes, true) : false;
            if ($idx !== false) {
                $unit += (float) ($product->variants['size_prices'][$idx] ?? 0);
            }

            $parts = array_filter([$line['size'], $line['color'] ? t('colors.'.$line['color']) : null]);

            return [
                'key' => $key,
                'product' => $product,
                'size' => $line['size'],
                'color' => $line['color'],
                'options_label' => implode(' · ', $parts),
                'image' => $product->imageForColor($line['color']),
                'qty' => $line['qty'],
                'unit_price' => $unit,
                'subtotal' => $unit * $line['qty'],
            ];
        })->filter()->values();
    }

    public static function total(): float
    {
        return (float) self::items()->sum('subtotal');
    }

    public static function shippingCost(float $subtotal, ?string $country): float
    {
        if ($subtotal <= 0 || $subtotal >= (float) config('site.shipping.free_threshold')) {
            return 0.0;
        }
        $zones = config('site.shipping.zones');

        return (float) ($zones[$country] ?? $zones['*']);
    }
}
