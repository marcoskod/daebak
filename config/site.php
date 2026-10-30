<?php

/**
 * IDENTITÉ DE LA PLATEFORME — source unique de toutes les informations d'identité affichées.
 * Pour dupliquer le projet pour une autre société : modifier uniquement ce fichier (+ le .env),
 * remplacer public/images/brand/* et adapter les fichiers de traduction lang/{locale}/*.json.
 * Accès : site('brand.name'), site('contact.email'), …
 */
return [
    'brand' => [
        'name' => env('APP_NAME', 'Daebak'),
        'native_name' => '대박',
        'legal_name' => 'Daebak Studio',
        'tagline_key' => 'brand.tagline',          // clé de traduction
        'logo' => 'images/brand/logo.svg',
        'logo_mark' => 'images/brand/mark.svg',
        'favicon' => 'images/brand/mark.svg',
        'founded' => 2026,
    ],

    // Palette (reprend celle de Be.Bright). Injectée en variables CSS par le layout.
    'colors' => [
        'primary' => '#442a22',
        'primary_container' => '#5d4037',
        'secondary' => '#75584d',
        'secondary_container' => '#fed7ca',
        'tertiary' => '#173723',
        'surface' => '#faf9f7',
        'accent' => '#c8553d',
    ],

    'contact' => [
        'email' => env('ADMIN_EMAIL', 'hello@daebak.store'),
        'support_email' => 'support@daebak.store',
        'phone' => '+82 2-555-0100',
        'address' => 'Seongsu-dong, Seoul, South Korea',
        'hours_key' => 'contact.hours',
    ],

    'social' => [
        'instagram' => 'https://instagram.com/daebak.store',
        'tiktok' => 'https://tiktok.com/@daebak.store',
        'youtube' => 'https://youtube.com/@daebak.store',
        'pinterest' => 'https://pinterest.com/daebakstore',
        'kakao' => 'https://pf.kakao.com/_daebak',
    ],

    'locales' => [
        'en' => ['label' => 'English', 'flag' => 'us', 'currency' => 'USD', 'html' => 'en'],
        'fr' => ['label' => 'Français', 'flag' => 'fr', 'currency' => 'EUR', 'html' => 'fr'],
        'ko' => ['label' => '한국어', 'flag' => 'kr', 'currency' => 'KRW', 'html' => 'ko'],
    ],
    'default_locale' => 'en',

    // Devise de base = devise de stockage des prix. Les autres sont des taux indicatifs d'affichage.
    'currency' => [
        'base' => 'USD',
        'rates' => ['USD' => 1.0, 'EUR' => 0.92, 'KRW' => 1370.0],
        'symbols' => ['USD' => '$', 'EUR' => '€', 'KRW' => '₩'],
        'decimals' => ['USD' => 2, 'EUR' => 2, 'KRW' => 0],
    ],

    'shipping' => [
        'free_threshold' => 75.0,            // USD
        'zones' => [                          // coût forfaitaire USD par pays (code ISO) puis "*" par défaut
            'KR' => 3.5, 'US' => 6.9, 'CA' => 8.5, 'GB' => 7.5, 'FR' => 7.9, 'DE' => 7.9, 'AU' => 11.0, 'JP' => 7.0, '*' => 12.0,
        ],
        'countries' => ['KR', 'US', 'FR', 'GB', 'CA', 'AU', 'DE', 'JP', 'ES', 'IT', 'SG'],
        'business_days' => ['KR' => [2, 4], 'US' => [4, 8], 'CA' => [5, 9], 'GB' => [4, 8], 'FR' => [4, 8], 'DE' => [4, 8], 'AU' => [6, 11], 'JP' => [3, 6], '*' => [7, 14]],
    ],

    // Partenaires print-on-demand affichés dans les fiches produit / pages d'information.
    'providers' => [
        'printful' => ['name' => 'Printful', 'icon' => 'fa-solid fa-print', 'color' => '#e0523a'],
        'printify' => ['name' => 'Printify', 'icon' => 'fa-solid fa-layer-group', 'color' => '#2f8f5b'],
        'gelato' => ['name' => 'Gelato', 'icon' => 'fa-solid fa-earth-asia', 'color' => '#3d5afe'],
        'gooten' => ['name' => 'Gooten', 'icon' => 'fa-solid fa-boxes-packing', 'color' => '#7a4fd1'],
    ],

    // Collections thématiques (filtrent les produits par tags). Textes : collections.{clé}.title / .text
    'collections' => [
        'hangul' => ['tags' => ['hangul', 'typography', 'love', 'study'], 'tone' => 'a', 'icon' => 'fa-solid fa-font', 'cover' => 'images/products/sarang-hangul-tee-white.svg'],
        'palace' => ['tags' => ['dancheong', 'palace', 'hanok', 'lattice', 'joseon', 'royal', 'moon-jar', 'hanji'], 'tone' => 'b', 'icon' => 'fa-solid fa-landmark', 'cover' => 'images/products/five-peaks-fine-art-print-ebony.svg'],
        'folk' => ['tags' => ['minhwa', 'folk-art', 'folklore', 'tiger', 'crane', 'guardian', 'goblin', 'bojagi'], 'tone' => 'c', 'icon' => 'fa-solid fa-dragon', 'cover' => 'images/products/minhwa-tiger-heavyweight-tee-sand.svg'],
        'seoul' => ['tags' => ['seoul', 'skyline', 'night', 'neon', 'han-river', 'sunset', 'arcade', 'gaming', 'namsan'], 'tone' => 'd', 'icon' => 'fa-solid fa-city', 'cover' => 'images/products/seoul-midnight-skyline-hoodie-navy.svg'],
        'kawaii' => ['tags' => ['kimchi', 'kawaii', 'snack', 'cute', 'banana-milk', 'food'], 'tone' => 'e', 'icon' => 'fa-solid fa-face-smile-beam', 'cover' => 'images/products/kimchi-friends-sticker-sheet-white.svg'],
        'blossom' => ['tags' => ['plum', 'maehwa', 'cherry-blossom', 'floral', 'mugunghwa', 'spring', 'summer', 'jeju', 'tangerine'], 'tone' => 'f', 'icon' => 'fa-solid fa-spa', 'cover' => 'images/products/cherry-blossom-season-hoodie-blush.svg'],
    ],

    // Lookbook : photos libres de droit (Unsplash, licence Unsplash) liées depuis leur CDN — aucun fichier copié.
    'lookbook' => [
        ['photo' => '1551748179-673f4dc0dd82', 'key' => 'sweat', 'route' => ['shop.collection', 'seoul']],
        ['photo' => '1771445269848-e04262f8ac88', 'key' => 'navy', 'route' => ['shop.catalogue', ['category' => 'hoodies']]],
        ['photo' => '1544032527-816db7031a16', 'key' => 'tee', 'route' => ['shop.catalogue', ['category' => 'tees']]],
        ['photo' => '1682783432407-75b14dd288d3', 'key' => 'tote', 'route' => ['shop.catalogue', ['category' => 'bags']]],
        ['photo' => '1588676907230-020a495f4cc9', 'key' => 'hanbok', 'route' => ['shop.collection', 'palace']],
    ],

    'payments' => ['card', 'paypal'],           // clés de traduction : checkout.pay.{key}

    'chat' => [
        'enabled' => true,
        'agent_name' => 'Dae',
        'idle_toggle_seconds' => 5,
        'ai_model' => env('CHAT_AI_MODEL', 'claude-haiku-5-5'),
    ],

    'description_templates' => [
        'default' => 'classic',
    ],

    // Menu principal (clé de traduction, route, icône FontAwesome)
    'nav' => [
        ['key' => 'home', 'route' => 'shop.home', 'icon' => 'fa-solid fa-house', 'match' => 'shop.home'],
        ['key' => 'shop', 'route' => 'shop.catalogue', 'icon' => 'fa-solid fa-bag-shopping', 'match' => 'shop.catalogue,shop.produit'],
        ['key' => 'collections', 'route' => 'shop.collections', 'icon' => 'fa-solid fa-swatchbook', 'match' => 'shop.collections,shop.collection'],
        ['key' => 'studio', 'route' => 'pages.studio', 'icon' => 'fa-solid fa-wand-magic-sparkles', 'match' => 'pages.studio'],
        ['key' => 'how', 'route' => 'pages.how', 'icon' => 'fa-solid fa-route', 'match' => 'pages.how'],
        ['key' => 'blog', 'label' => 'blog.nav', 'route' => 'blog.index', 'icon' => 'fa-solid fa-newspaper', 'match' => 'blog.*'],
        ['key' => 'creators', 'route' => 'pages.creators', 'icon' => 'fa-solid fa-palette', 'match' => 'pages.creators'],
        ['key' => 'tracking', 'route' => 'tracking.index', 'icon' => 'fa-solid fa-truck-fast', 'match' => 'tracking.*'],
    ],
];
