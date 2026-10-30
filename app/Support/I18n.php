<?php

namespace App\Support;

use Illuminate\Support\Arr;

/**
 * Traductions 100 % JSON : lang/{locale}/*.json (arbres imbriqués, clés pointées : "home.hero.title").
 * Tous les fichiers d'une langue sont fusionnés. Repli : locale par défaut, puis la clé elle-même.
 */
class I18n
{
    protected static array $trees = [];

    /** Vide le cache mémoire des arbres (après modification des surcharges d'admin). */
    public static function flush(): void
    {
        self::$trees = [];
    }

    public static function tree(string $locale): array
    {
        if (isset(self::$trees[$locale])) {
            return self::$trees[$locale];
        }

        $tree = [];
        foreach (glob(lang_path("{$locale}/*.json")) ?: [] as $file) {
            $data = json_decode(file_get_contents($file), true);
            if (is_array($data)) {
                $tree = array_replace_recursive($tree, $data);
            }
        }

        // Surcharges de textes saisies dans l'admin (Content → Shop pages).
        return self::$trees[$locale] = PageEditor::applyTexts($tree, $locale);
    }

    public static function get(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $value = Arr::get(self::tree($locale), $key);

        if (! is_string($value)) {
            $value = Arr::get(self::tree(config('site.default_locale', 'en')), $key);
        }

        if (! is_string($value)) {
            return $key;
        }

        foreach ($replace as $name => $val) {
            $value = str_replace(':'.$name, (string) $val, $value);
        }

        return $value;
    }

    /** Sous-arbre brut (listes, tableaux) avec repli sur la langue par défaut. */
    public static function group(string $key, ?string $locale = null): array
    {
        $locale ??= app()->getLocale();
        $value = Arr::get(self::tree($locale), $key);
        $fallback = Arr::get(self::tree(config('site.default_locale', 'en')), $key);

        if (is_array($value) && is_array($fallback) && ! array_is_list($value)) {
            return array_replace_recursive($fallback, $value);
        }

        return is_array($value) ? $value : (is_array($fallback) ? $fallback : []);
    }

    /** Clés exposées au JavaScript (groupe "js") */
    public static function forJs(): array
    {
        return self::group('js');
    }
}
