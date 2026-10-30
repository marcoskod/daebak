<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Analyse une description produit « texte enrichi léger » en sections structurées, puis laisse
 * les templates (resources/views/description-templates/*) la rendre sous différents styles.
 *
 * Syntaxe : "## Titre" = nouvelle section · "- item" ou "• item" = liste · "Clé: valeur" (2 lignes ou +) = tableau de specs
 *           "> texte" = citation · **gras** · *italique* · ligne vide = nouveau paragraphe.
 * Fonctionne aussi avec une simple description sans balisage (devient l'introduction).
 */
class ProductDescription
{
    /** Libellés de repli (français) si l'application n'a pas de système de traduction `t()`. */
    protected const FALLBACK_LABELS = [
        'overview' => 'Description',
        'details' => 'Détails',
        'step' => 'Étape',
        'read_more' => 'En savoir plus',
    ];

    public static function templates(): array
    {
        return config('description_templates.templates', []);
    }

    public static function label(string $key): string
    {
        if (function_exists('t')) {
            $value = t("desc.{$key}");

            return $value === "desc.{$key}" ? (self::FALLBACK_LABELS[$key] ?? $key) : $value;
        }

        return self::FALLBACK_LABELS[$key] ?? $key;
    }

    /** Libellé / aide localisés d'un template (locale courante, repli anglais puis français). */
    public static function templateText(string $key, string $field = 'label'): string
    {
        $tpl = self::templates()[$key][$field] ?? [];
        $locale = app()->getLocale();

        return $tpl[$locale] ?? $tpl['en'] ?? $tpl['fr'] ?? $key;
    }

    /**
     * @return array{intro: array, sections: array<int, array{title: string, icon: string, blocks: array}>, has_sections: bool}
     */
    public static function parse(?string $text): array
    {
        $text = trim(str_replace(["\r\n", "\r"], "\n", (string) $text));
        $result = ['intro' => [], 'sections' => [], 'has_sections' => false];

        if ($text === '') {
            return $result;
        }

        $current = null; // null = intro
        $buffer = [];

        $flush = function () use (&$buffer, &$current, &$result) {
            $blocks = self::blocksFromLines($buffer);
            $buffer = [];
            if (! $blocks) {
                return;
            }
            if ($current === null) {
                $result['intro'] = array_merge($result['intro'], $blocks);
            } else {
                $result['sections'][$current]['blocks'] = array_merge($result['sections'][$current]['blocks'], $blocks);
            }
        };

        foreach (explode("\n", $text) as $line) {
            if (preg_match('/^#{1,4}\s+(.+)$/u', trim($line), $m)) {
                $flush();
                $result['sections'][] = ['title' => trim($m[1]), 'icon' => self::iconFor($m[1], count($result['sections'])), 'blocks' => []];
                $current = array_key_last($result['sections']);

                continue;
            }
            $buffer[] = $line;
        }
        $flush();

        $result['has_sections'] = count($result['sections']) > 0;

        return $result;
    }

    protected static function blocksFromLines(array $lines): array
    {
        $blocks = [];
        $para = [];
        $list = [];
        $specs = [];

        $flushPara = function () use (&$para, &$blocks) {
            if ($para) {
                $blocks[] = ['type' => 'p', 'html' => self::inline(implode(' ', $para))];
                $para = [];
            }
        };
        $flushList = function () use (&$list, &$blocks) {
            if ($list) {
                $blocks[] = ['type' => 'ul', 'items' => array_map([self::class, 'inline'], $list)];
                $list = [];
            }
        };
        $flushSpecs = function () use (&$specs, &$blocks) {
            if (count($specs) >= 2) {
                $blocks[] = ['type' => 'specs', 'rows' => array_map(fn ($r) => [self::inline($r[0]), self::inline($r[1])], $specs)];
            } elseif (count($specs) === 1) {
                $blocks[] = ['type' => 'p', 'html' => self::inline($specs[0][0].': '.$specs[0][1])];
            }
            $specs = [];
        };

        foreach ($lines as $raw) {
            $line = trim($raw);

            if ($line === '') {
                $flushPara();
                $flushList();
                $flushSpecs();

                continue;
            }

            if (preg_match('/^(?:[-*•]\s+)(.+)$/u', $line, $m)) {
                $flushPara();
                $flushSpecs();
                $list[] = $m[1];
            } elseif (preg_match('/^>\s?(.+)$/u', $line, $m)) {
                $flushPara();
                $flushList();
                $flushSpecs();
                $blocks[] = ['type' => 'quote', 'html' => self::inline($m[1])];
            } elseif (preg_match('/^([^:：]{2,32})[:：]\s+(.+)$/u', $line, $m) && ! str_contains($m[1], '.')) {
                $flushPara();
                $flushList();
                $specs[] = [$m[1], $m[2]];
            } else {
                $flushList();
                $flushSpecs();
                $para[] = $line;
            }
        }
        $flushPara();
        $flushList();
        $flushSpecs();

        return $blocks;
    }

    /** Échappe puis applique **gras** et *italique* (sûr : le HTML saisi est neutralisé). */
    public static function inline(string $text): string
    {
        $html = e($text);
        $html = preg_replace('/\*\*(.+?)\*\*/u', '<strong>$1</strong>', $html);
        $html = preg_replace('/(?<!\*)\*(?!\s)(.+?)(?<!\s)\*(?!\*)/u', '<em>$1</em>', $html);

        return $html;
    }

    /** Sections normalisées pour les templates : [sections, section d'introduction|null]. */
    public static function normalize(array $d): array
    {
        $overview = ['title' => self::label('overview'), 'icon' => 'align-left', 'blocks' => $d['intro']];
        if ($d['has_sections']) {
            return [$d['sections'], $d['intro'] ? $overview : null];
        }

        return [[$overview], null];
    }

    /** Premier paragraphe de l'introduction (pour accroches, citations). */
    public static function lead(array $parsed): ?string
    {
        foreach ($parsed['intro'] as $b) {
            if ($b['type'] === 'p') {
                return $b['html'];
            }
        }

        return null;
    }

    /** Icône (nom du jeu SVG interne) selon le titre de section, sinon rotation. */
    public static function iconFor(string $title, int $index): string
    {
        $map = [
            'check' => ['highlight', 'feature', 'point', 'benefit', 'atout', 'point fort', '특징', '하이라이트', '장점'],
            'heart' => ['story', 'histoire', 'inspiration', 'about', 'culture', '이야기', '스토리'],
            'ruler' => ['spec', 'detail', 'technique', 'size', 'dimension', 'fiche', '사양', '스펙', '상세'],
            'leaf' => ['care', 'entretien', 'wash', 'lavage', 'material', 'matière', '관리', '세탁', '소재'],
            'truck' => ['ship', 'livraison', 'delivery', 'production', '배송', '제작'],
            'sparkles' => ['how', 'comment', 'usage', 'use', 'ingrédient', 'ingredient', '사용', '성분'],
        ];
        $needle = Str::lower($title);
        foreach ($map as $icon => $words) {
            foreach ($words as $w) {
                if (str_contains($needle, $w)) {
                    return $icon;
                }
            }
        }
        $cycle = ['sparkles', 'check', 'heart', 'leaf', 'ruler', 'truck'];

        return $cycle[$index % count($cycle)];
    }

    /** Petit jeu d'icônes SVG inline (aucune dépendance front). */
    public static function svg(string $name, int $size = 22): string
    {
        $paths = [
            'check' => '<path d="M20 6 9 17l-5-5"/>',
            'heart' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/>',
            'ruler' => '<path d="M21.3 8.7 8.7 21.3a1 1 0 0 1-1.4 0L2.7 16.7a1 1 0 0 1 0-1.4L15.3 2.7a1 1 0 0 1 1.4 0l4.6 4.6a1 1 0 0 1 0 1.4z"/><path d="m7.5 10.5 2 2M10.5 7.5l2 2M13.5 4.5l2 2M4.5 13.5l2 2"/>',
            'leaf' => '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.2 2 8 0 5.5-4.8 10-10 10z"/><path d="M2 21c0-3 1.9-5.4 5.1-6.3C9.5 14 12 12 13 10"/>',
            'truck' => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18h4a1 1 0 0 0 1-1v-4l-3-4h-2"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
            'sparkles' => '<path d="m12 3-1.9 5.8L4 11l6.1 2.2L12 19l1.9-5.8L20 11l-6.1-2.2z"/><path d="M5 3v4M3 5h4M19 17v4M17 19h4"/>',
            'align-left' => '<path d="M17 10H3M21 6H3M21 14H3M17 18H3"/>',
            'newspaper' => '<path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8M15 18h-5M10 6h8v4h-8z"/>',
            'grid' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
            'tabs' => '<rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 10h7V6M14 6v4h7"/>',
            'list' => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
            'steps' => '<circle cx="6" cy="6" r="2.5"/><circle cx="18" cy="12" r="2.5"/><circle cx="6" cy="18" r="2.5"/><path d="M8.5 6H14a4 4 0 0 1 0 8M15.5 12H9a3 3 0 0 0 0 6"/>',
            'quote' => '<path d="M3 21c3 0 7-1 7-8V5c0-1.3-.7-2-2-2H4c-1.3 0-2 .7-2 2v6c0 1.3.7 2 2 2h4M15 21c3 0 7-1 7-8V5c0-1.3-.7-2-2-2h-4c-1.3 0-2 .7-2 2v6c0 1.3.7 2 2 2h4"/>',
            'chevron' => '<path d="m6 9 6 6 6-6"/>',
        ];

        return '<svg class="pd-ico" width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($paths[$name] ?? $paths['sparkles']).'</svg>';
    }
}
