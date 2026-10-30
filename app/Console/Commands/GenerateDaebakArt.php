<?php

namespace App\Console\Commands;

use App\Support\Art\ArtBuilder;
use Illuminate\Console\Command;

class GenerateDaebakArt extends Command
{
    protected $signature = 'daebak:art';

    protected $description = 'Génère les visuels SVG de démonstration (produits, catégories, bannières, avatars, logo) dans public/images';

    public function handle(): int
    {
        $catalog = require database_path('data/catalog.php');
        $extra = require database_path('data/catalog_extra.php');
        $catalog['categories'] = array_merge($catalog['categories'], $extra['categories']);
        $catalog['products'] = array_merge($catalog['products'], $extra['products']);
        $art = new ArtBuilder(public_path(), $catalog);

        $art->brand();
        $art->homeCollage();

        foreach ($catalog['products'] as $i => $spec) {
            $art->product($spec, $i);
        }
        foreach ($catalog['categories'] as $cat) {
            $art->category($cat);
        }

        foreach ([
            'catalogue' => 'dancheong', 'collections' => 'bojagi', 'product' => 'dots', 'cart' => 'waves', 'checkout' => 'lattice',
            'confirmation' => 'moon', 'tracking' => 'peaks', 'faq' => 'plum', 'contact' => 'moon', 'shipping' => 'waves',
            'legal' => 'lattice', 'how' => 'peaks', 'studio' => 'dancheong', 'creators' => 'bojagi', 'about' => 'plum', 'account' => 'dots', 'auth' => 'plum', 'error' => 'moon', 'wishlist' => 'plum',
        ] as $key => $kind) {
            $art->hero($key, $kind);
        }

        foreach ([['#f2c9a5', '#2a1d18', '#254232', 'bob'], ['#e8b99a', '#1c1b1a', '#c8553d', 'long'], ['#f5d3b8', '#4a3228', '#1f2f4d', 'short'], ['#d9a98a', '#1c1b1a', '#b3262e', 'bun'], ['#f0c4a0', '#5a3a28', '#75584d', 'short'], ['#e9bd9d', '#2a1d18', '#a9b8a0', 'long']] as $n => [$skin, $hair, $shirt, $style]) {
            $art->creator($n + 1, $skin, $hair, $shirt, $style);
        }

        $this->info('Visuels générés dans public/images.');

        return self::SUCCESS;
    }
}
