<?php

/**
 * ÉDITEUR DE PAGES (admin → Content → Shop pages).
 * Les valeurs saisies dans l'admin ne remplacent jamais les fichiers : ce sont des surcharges
 * (table page_settings) appliquées par-dessus lang/*.json, les images par défaut et le CSS existant.
 *
 * pages.{clé}.routes   : noms de routes rattachées à cette page
 * pages.{clé}.prefixes : groupes de clés de traduction éditables (premier segment de la clé JSON)
 * pages.{clé}.images   : emplacements d'images remplaçables (slot => [libellé, image par défaut])
 */
$hero = fn (string $img) => ['hero' => ['Page banner', "images/heroes/{$img}.svg"]];

return [
    'pages' => [
        'global' => ['label' => 'Global (sidebar, footer, brand)', 'url' => '/', 'routes' => ['*'], 'prefixes' => ['brand', 'nav', 'footer', 'common'], 'images' => []],
        'home' => ['label' => 'Home', 'url' => '/', 'routes' => ['shop.home'], 'prefixes' => ['home', 'lookbook'], 'images' => ['hero_art' => ['Hero illustration', 'images/brand/hero-collage.svg']]],
        'catalogue' => ['label' => 'Catalogue', 'url' => '/shop', 'routes' => ['shop.catalogue'], 'prefixes' => ['shop'], 'images' => $hero('catalogue')],
        'collections' => ['label' => 'Collections', 'url' => '/collections', 'routes' => ['shop.collections', 'shop.collection'], 'prefixes' => ['collections'], 'images' => $hero('collections')],
        'product' => ['label' => 'Product page', 'url' => '/shop', 'routes' => ['shop.produit'], 'prefixes' => ['product'], 'images' => $hero('product')],
        'cart' => ['label' => 'Cart', 'url' => '/cart', 'routes' => ['shop.panier'], 'prefixes' => ['cart'], 'images' => $hero('cart')],
        'checkout' => ['label' => 'Checkout & confirmation', 'url' => '/checkout', 'routes' => ['checkout.index', 'checkout.confirmation', 'checkout.demo'], 'prefixes' => ['checkout', 'confirm', 'demo'], 'images' => $hero('checkout')],
        'tracking' => ['label' => 'Order tracking', 'url' => '/track-order', 'routes' => ['tracking.index', 'tracking.find'], 'prefixes' => ['tracking'], 'images' => $hero('tracking')],
        'wishlist' => ['label' => 'Wishlist', 'url' => '/wishlist', 'routes' => ['shop.wishlist'], 'prefixes' => ['wishlist'], 'images' => $hero('wishlist')],
        'how' => ['label' => 'How it works', 'url' => '/how-it-works', 'routes' => ['pages.how'], 'prefixes' => ['how'], 'images' => $hero('how')],
        'studio' => ['label' => 'Studio', 'url' => '/studio', 'routes' => ['pages.studio'], 'prefixes' => ['studio'], 'images' => $hero('studio')],
        'creators' => ['label' => 'Creators', 'url' => '/creators', 'routes' => ['pages.creators'], 'prefixes' => ['creators'], 'images' => $hero('creators')],
        'faq' => ['label' => 'FAQ', 'url' => '/faq', 'routes' => ['pages.faq'], 'prefixes' => ['faq'], 'images' => $hero('faq')],
        'shipping' => ['label' => 'Shipping & returns', 'url' => '/shipping-returns', 'routes' => ['pages.shipping'], 'prefixes' => ['shipping'], 'images' => $hero('shipping')],
        'size_guide' => ['label' => 'Size guide', 'url' => '/size-guide', 'routes' => ['pages.size-guide'], 'prefixes' => ['size_guide'], 'images' => $hero('shipping')],
        'contact' => ['label' => 'Contact', 'url' => '/contact', 'routes' => ['pages.contact'], 'prefixes' => ['contact'], 'images' => $hero('contact')],
        'legal' => ['label' => 'Legal pages (terms, privacy, notice)', 'url' => '/terms', 'routes' => ['pages.cgv', 'pages.confidentialite', 'pages.mentions-legales'], 'prefixes' => ['legal'], 'images' => $hero('legal')],
        'blog' => ['label' => 'Blog (journal)', 'url' => '/blog', 'routes' => ['blog.index', 'blog.show'], 'prefixes' => ['blog'], 'images' => []],
    ],

    // Variables CSS (couleurs de la page, mode clair) : clé => [libellé, variable CSS]
    'vars' => [
        'brand' => ['Primary color (buttons, links)', '--brand-solid'],
        'accent' => ['Accent color', '--accent'],
        'bg' => ['Page background', '--bg'],
        'card' => ['Card background', '--card'],
        'ink' => ['Main text color', '--ink'],
        'line' => ['Border / divider color', '--line'],
    ],

    // Blocs stylables : clé => [libellé, sélecteur CSS existant]
    'targets' => [
        'hero' => ['Hero banner', '.hero, .page-hero'],
        'cards' => ['Cards & tiles', '.card, .card-flat, .how-step, .tile, .quote-card, .blog-card'],
        'band' => ['Call-to-action band', '.band'],
        'buttons' => ['Primary buttons', '.btn-primary'],
        'headings' => ['Section headings', '.sec-head h2, .page-hero h1, .hero h1'],
        'sidebar' => ['Sidebar & top bar', '.sidebar, .topbar'],
        'footer' => ['Footer', '.footer'],
    ],
    'global_targets' => ['sidebar', 'footer'],
    'page_targets' => ['hero', 'cards', 'band', 'buttons', 'headings'],
];
