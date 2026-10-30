<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Support\Art\ArtBuilder;
use Illuminate\Database\Seeder;

/** Articles de démonstration du blog (en/fr/ko) + couvertures SVG générées. */
class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = require database_path('data/catalog.php');
        $art = new ArtBuilder(public_path(), $catalog);

        foreach (require database_path('data/blog.php') as $post) {
            $cover = $art->blogCover($post['slug'], $post['cover'][0], $post['cover'][1]);
            $translations = [];
            foreach (['en', 'fr', 'ko'] as $loc) {
                $translations[$loc] = $post[$loc];
            }

            BlogPost::updateOrCreate(['slug' => $post['slug']], [
                'translations' => $translations,
                'category' => $post['category'],
                'cover' => $cover,
                'author' => $post['author'],
                'tags' => $post['tags'],
                'products' => $post['products'],
                'is_featured' => $post['featured'],
                'is_published' => true,
                'published_at' => now()->subDays($post['days_ago']),
            ]);
        }
    }
}
