<?php

/**
 * Templates de mise en page des articles de blog (/blog/{slug}).
 * Chaque clé = resources/views/blog/templates/{view}.blade.php + public/css/blog-templates.css.
 * Textes : lang/{locale}/blog_templates.json -> blog_templates.{clé}.name / .desc
 * 'classic' = rendu historique, à ne jamais modifier.
 */
return [
    'default' => 'classic',
    'templates' => [
        'classic' => ['icon' => 'align-left', 'view' => 'classic'],
        'magazine' => ['icon' => 'newspaper', 'view' => 'magazine'],
        'sidebar-toc' => ['icon' => 'list-ol', 'view' => 'sidebar-toc'],
        'centered-minimal' => ['icon' => 'align-center', 'view' => 'centered-minimal'],
        'hero-overlay' => ['icon' => 'image', 'view' => 'hero-overlay'],
        'split' => ['icon' => 'table-columns', 'view' => 'split'],
        'longform-numbered' => ['icon' => 'arrow-down-1-9', 'view' => 'longform-numbered'],
        'cards' => ['icon' => 'grip', 'view' => 'cards'],
        'timeline' => ['icon' => 'timeline', 'view' => 'timeline'],
        'gallery-showcase' => ['icon' => 'images', 'view' => 'gallery-showcase'],
    ],
];
