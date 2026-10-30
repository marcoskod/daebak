<?php

namespace App\Support;

use App\Models\PageSetting;
use Illuminate\Support\Arr;

/**
 * Éditeur de pages : applique les surcharges saisies dans l'admin (textes, images, couleurs, bordures…)
 * par-dessus les fichiers existants, sans toucher aux vues ni au CSS d'origine.
 */
class PageEditor
{
    public const BORDER_STYLES = ['solid', 'dashed', 'dotted', 'double', 'none'];

    public static function pages(): array
    {
        return config('page_editor.pages', []);
    }

    /** Page courante d'après le nom de la route (null si la route n'est rattachée à aucune page). */
    public static function currentKey(): ?string
    {
        $name = request()->route()?->getName();
        foreach (self::pages() as $key => $page) {
            if ($name && in_array($name, $page['routes'], true)) {
                return $key;
            }
        }

        return null;
    }

    /** Surcharges de texte d'une langue, toutes pages confondues : [clé pointée => valeur]. */
    public static function textOverrides(string $locale): array
    {
        $out = [];
        foreach (PageSetting::allCached() as $setting) {
            foreach (Arr::get($setting->texts ?? [], $locale, []) as $key => $value) {
                if (is_string($value) && $value !== '') {
                    $out[str_replace('__', '.', $key)] = $value;
                }
            }
        }

        return $out;
    }

    /** Applique les surcharges de texte à l'arbre de traduction d'une langue. */
    public static function applyTexts(array $tree, string $locale): array
    {
        foreach (self::textOverrides($locale) as $key => $value) {
            Arr::set($tree, $key, $value);
        }

        return $tree;
    }

    /** Chemin d'une image : surcharge admin, sinon image par défaut. */
    public static function image(string $page, string $slot, string $default): string
    {
        $path = Arr::get(PageSetting::forPage($page)?->images ?? [], $slot);

        return $path ?: $default;
    }

    /** Liste « à plat » [clé pointée => texte] des textes éditables d'une page, dans la langue donnée. */
    public static function editableTexts(string $page, string $locale): array
    {
        $out = [];
        $tree = self::baseTree($locale);
        foreach (config("page_editor.pages.$page.prefixes", []) as $prefix) {
            if (isset($tree[$prefix])) {
                self::flatten($tree[$prefix], $prefix, $out);
            }
        }

        return $out;
    }

    /** Arbre de traduction issu des fichiers uniquement (sans surcharges). */
    public static function baseTree(string $locale): array
    {
        $tree = [];
        foreach (glob(lang_path("{$locale}/*.json")) ?: [] as $file) {
            $data = json_decode(file_get_contents($file), true);
            if (is_array($data)) {
                $tree = array_replace_recursive($tree, $data);
            }
        }

        return $tree;
    }

    protected static function flatten(mixed $node, string $path, array &$out): void
    {
        if (is_array($node)) {
            foreach ($node as $k => $v) {
                self::flatten($v, "$path.$k", $out);
            }
        } elseif (is_string($node)) {
            $out[$path] = $node;
        }
    }

    /** CSS de la page courante + styles globaux, prêt à être injecté dans <style>. */
    public static function css(?string $page): string
    {
        $css = '';
        foreach (array_filter(['global', $page]) as $key) {
            $styles = PageSetting::forPage($key)?->styles ?? [];
            $scope = $key === 'global' ? 'body' : 'body[data-page="'.$key.'"]';
            $css .= self::varsCss($styles['vars'] ?? [], $scope);
            foreach ($styles as $target => $props) {
                $selector = config("page_editor.targets.$target.1");
                if ($target !== 'vars' && $selector && is_array($props)) {
                    $css .= self::targetCss($selector, $props, $scope, $target);
                }
            }
        }

        return $css;
    }

    protected static function varsCss(array $vars, string $scope): string
    {
        $decl = '';
        foreach (config('page_editor.vars') as $key => [, $var]) {
            if ($color = self::color($vars[$key] ?? null)) {
                $decl .= "$var:$color;";
                if ($key === 'brand') {
                    $decl .= "--brand:$color;";
                }
            }
        }

        return $decl ? 'html:not([data-theme="dark"]) '.$scope.'{'.$decl.'}' : '';
    }

    protected static function targetCss(string $selector, array $p, string $scope, string $target): string
    {
        $decl = '';
        $bg = self::color($p['background_color'] ?? null);
        $bg2 = self::color($p['background_color_2'] ?? null);
        $img = self::imageUrl($p['background_image'] ?? null);
        if ($img) {
            $decl .= "background:url('$img') center/cover no-repeat".($bg ? " $bg" : '').';';
        } elseif ($bg && $bg2) {
            $decl .= "background:linear-gradient(120deg,$bg,$bg2);";
        } elseif ($bg) {
            $decl .= "background:$bg;";
        }
        if ($c = self::color($p['text_color'] ?? null)) {
            $decl .= "color:$c;";
        }
        $style = in_array($p['border_style'] ?? 'solid', self::BORDER_STYLES, true) ? ($p['border_style'] ?? 'solid') : 'solid';
        $width = isset($p['border_width']) && $p['border_width'] !== '' ? max(0, min(12, (int) $p['border_width'])) : null;
        $bc = self::color($p['border_color'] ?? null);
        if ($width !== null || $bc) {
            $decl .= 'border:'.($style === 'none' ? 'none' : ($width ?? 1)."px $style ".($bc ?? 'var(--line)')).';';
        }
        if (isset($p['radius']) && $p['radius'] !== '') {
            $decl .= 'border-radius:'.max(0, min(80, (int) $p['radius'])).'px;';
        }
        if (! $decl) {
            return '';
        }

        // Sélecteurs à plat : on préfixe chaque sélecteur de la liste avec la portée (page courante ou global).
        $scoped = collect(explode(',', $selector))->map(fn ($s) => $scope.' '.trim($s))->implode(',');
        $css = $scoped.'{'.$decl.'}';
        if ($target === 'buttons') {
            $css .= collect(explode(',', $selector))->map(fn ($s) => $scope.' '.trim($s).':hover')->implode(',').'{filter:brightness(.92)}';
        }

        return $css;
    }

    /** Couleur hexadécimale valide (#rgb, #rrggbb, #rrggbbaa) ou null — évite toute injection CSS. */
    public static function color(?string $value): ?string
    {
        return is_string($value) && preg_match('/^#[0-9a-fA-F]{3,8}$/', $value) ? $value : null;
    }

    protected static function imageUrl(?string $path): ?string
    {
        if (! $path || ! preg_match('#^[A-Za-z0-9_./\- ]+$#', $path) || str_contains($path, '..')) {
            return null;
        }

        return asset($path);
    }
}
