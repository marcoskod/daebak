<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Support\Art\ArtBuilder;
use Illuminate\Database\Seeder;

/**
 * Miniatures de catégories : génère un visuel SVG (même style que `daebak:art`) pour chaque catégorie sans image.
 * Idempotent : ne touche jamais aux catégories qui ont déjà une image (legacy ou upload admin).
 * Usage : php artisan db:seed --class=CategoryThumbnailSeeder
 */
class CategoryThumbnailSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = require database_path('data/catalog.php');
        $extra = require database_path('data/catalog_extra.php');
        $catalog['categories'] = array_merge($catalog['categories'], $extra['categories']);
        $catalog['products'] = array_merge($catalog['products'], $extra['products']);
        $art = new ArtBuilder(public_path(), $catalog);

        $specs = [];
        foreach ($catalog['categories'] as $cat) {
            $specs[$cat['slug']] = $cat;
            foreach ($cat['children'] ?? [] as $child) {
                $specs[$child['slug']] = $child + ['parent_art' => $cat['art'] ?? null];
            }
        }

        $bgs = ['#f6e6e0', '#e8efe6', '#f3ecd9', '#e9e4f0', '#e6ecf3', '#f1e6d4'];
        $n = 0;
        foreach (Category::query()->orderBy('sort_order')->get() as $category) {
            if ($category->image || ! isset($specs[$category->slug])) {
                continue;
            }
            $cat = $specs[$category->slug];
            if (empty($cat['art'])) {
                // sous-catégorie : premier produit de la catégorie (type, coloris, motif)
                $product = collect($catalog['products'])->firstWhere('cat', $category->slug);
                if (! $product) {
                    continue;
                }
                $cat['art'] = [$product['type'], $catalog['colors'][$product['colors'][0]][0], $product['motif'], $bgs[$n++ % count($bgs)]];
            }
            $path = $art->category($cat);
            if ($path) {
                $category->forceFill(['image' => $path])->save();
            }
        }
    }
}
