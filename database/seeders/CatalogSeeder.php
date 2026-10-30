<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Support\Art\ArtBuilder;
use Illuminate\Database\Seeder;

/** Catalogue de démonstration : catégories, produits multilingues, visuels et descriptions structurées. */
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = require database_path('data/catalog.php');
        $extra = require database_path('data/catalog_extra.php');
        $catalog['categories'] = array_merge($catalog['categories'], $extra['categories']);
        $catalog['products'] = array_merge($catalog['products'], $extra['products']);
        $types = require database_path('data/catalog_types.php');
        $art = new ArtBuilder(public_path(), $catalog);
        $art->brand();

        $order = 0;
        $catIds = [];
        foreach ($catalog['categories'] as $cat) {
            $parent = $this->saveCategory($cat, null, $art->category($cat), $order++);
            $catIds[$cat['slug']] = $parent->id;
            foreach ($cat['children'] ?? [] as $child) {
                $catIds[$child['slug']] = $this->saveCategory($child, $parent->id, null, $order++)->id;
            }
        }

        foreach ($catalog['products'] as $i => $spec) {
            $built = $art->product($spec, $i);
            $colors = array_map(fn ($c) => $c + ['name' => $catalog['colors'][$c['key']][1]], $built['colors']);
            $translations = [];
            foreach (['en', 'fr', 'ko'] as $loc) {
                $t = $spec[$loc];
                $translations[$loc] = [
                    'name' => $t['name'],
                    'short' => $t['short'],
                    'description' => $this->description($loc, $t, $types, $spec),
                ];
            }

            $variants = ['colors' => $colors];
            if (! empty($spec['sizes'])) {
                $variants['sizes'] = $spec['sizes'];
                if (! empty($spec['size_prices'])) {
                    $variants['size_prices'] = $spec['size_prices'];
                }
            }

            Product::updateOrCreate(['slug' => $spec['slug']], [
                'category_id' => $catIds[$spec['cat']],
                'name' => $spec['en']['name'],
                'sku' => 'DB-'.str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'description' => $translations['en']['description'],
                'translations' => $translations,
                'product_type' => $spec['type'],
                'provider' => $spec['provider'],
                'provider_sku' => $spec['provider_sku'],
                'variants' => $variants,
                'tags' => $spec['tags'],
                'motif' => explode(':', $spec['motif'])[0],
                'badge' => $spec['badge'],
                'rating' => $spec['rating'],
                'reviews_count' => $spec['reviews'],
                'production_min' => $spec['days'][0],
                'production_max' => $spec['days'][1],
                'description_template' => $spec['tpl'],
                'price' => $spec['price'],
                'sale_price' => $spec['sale'],
                'stock_quantity' => 9999,   // produits à la demande : stock virtuel
                'stock_alert_threshold' => 0,
                'image' => $built['image'],
                'images' => $built['images'],
                'keywords' => $spec['tags'],
                'is_active' => true,
            ]);
        }
    }

    protected function saveCategory(array $cat, ?int $parentId, ?string $image, int $order): Category
    {
        $translations = [];
        foreach (['en', 'fr', 'ko'] as $loc) {
            $translations[$loc] = ['name' => $cat[$loc][0], 'description' => $cat[$loc][1]];
        }

        return Category::updateOrCreate(['slug' => $cat['slug']], [
            'parent_id' => $parentId,
            'name' => $cat['en'][0],
            'description' => $cat['en'][1],
            'translations' => $translations,
            'icon' => $cat['icon'] ?? null,
            'image' => $image,
            'sort_order' => $order,
            'is_active' => true,
        ]);
    }

    /** Assemble la description structurée (syntaxe « ## Titre / - puces / Clé: valeur ») pour une langue. */
    protected function description(string $loc, array $t, array $types, array $spec): string
    {
        $h = $types['headings'][$loc];
        $info = $types['types'][$spec['type']][$loc];
        $provider = config('site.providers.'.$spec['provider'].'.name', ucfirst($spec['provider']));

        $out = $t['intro']."\n\n## {$h['highlights']}\n";
        foreach ($t['bullets'] as $b) {
            $out .= "- {$b}\n";
        }
        $out .= "\n## {$h['story']}\n{$t['story']}\n\n## {$h['specs']}\n";
        foreach ($info['specs'] as $k => $v) {
            $out .= "{$k}: {$v}\n";
        }
        $out .= "\n## {$h['care']}\n";
        foreach ($info['care'] as $c) {
            $out .= "- {$c}\n";
        }
        $ship = str_replace([':provider', ':min', ':max'], [$provider, $spec['days'][0], $spec['days'][1]], $types['shipping'][$loc]);
        $out .= "\n## {$h['shipping']}\n{$ship}\n";

        return trim($out);
    }
}
