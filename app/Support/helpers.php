<?php

use App\Support\I18n;
use Illuminate\Support\Arr;

if (! function_exists('site')) {
    /** Identité de la plateforme (config/site.php). */
    function site(string $key, mixed $default = null): mixed
    {
        return config("site.{$key}", $default);
    }
}

if (! function_exists('t')) {
    /** Traduction par clé JSON (lang/{locale}/*.json). */
    function t(string $key, array $replace = []): string
    {
        return I18n::get($key, $replace);
    }
}

if (! function_exists('page_image')) {
    /** URL d'une image de page : surcharge admin (Content → Shop pages) ou image par défaut. */
    function page_image(string $slot, string $default, ?string $page = null): string
    {
        $page ??= \App\Support\PageEditor::currentKey();

        return asset($page ? \App\Support\PageEditor::image($page, $slot, $default) : $default);
    }
}

if (! function_exists('tg')) {
    /** Sous-arbre de traductions (listes, tableaux). */
    function tg(string $key): array
    {
        return I18n::group($key);
    }
}

if (! function_exists('current_currency')) {
    function current_currency(): string
    {
        $code = session('currency');
        if ($code && isset(config('site.currency.rates')[$code])) {
            return $code;
        }

        return config('site.locales.'.app()->getLocale().'.currency', config('site.currency.base'));
    }
}

if (! function_exists('money')) {
    /** Formate un montant exprimé dans la devise de base (USD) vers la devise d'affichage. */
    function money(float|int|string|null $base, ?string $currency = null): string
    {
        $currency ??= current_currency();
        $rate = config("site.currency.rates.{$currency}", 1);
        $decimals = config("site.currency.decimals.{$currency}", 2);
        $symbol = config("site.currency.symbols.{$currency}", $currency.' ');
        $value = (float) $base * $rate;

        if ($currency === 'KRW') {
            $value = round($value / 100) * 100;
        }

        $number = number_format($value, $decimals, app()->getLocale() === 'fr' ? ',' : '.', app()->getLocale() === 'fr' ? ' ' : ',');

        return $currency === 'EUR' && app()->getLocale() === 'fr' ? "{$number} {$symbol}" : "{$symbol}{$number}";
    }
}

if (! function_exists('loc')) {
    /** Valeur localisée d'un champ JSON de traductions { "en": ..., "fr": ... }. */
    function loc(?array $translations, string $field, mixed $default = ''): mixed
    {
        if (! $translations) {
            return $default;
        }

        return Arr::get($translations, app()->getLocale().'.'.$field)
            ?? Arr::get($translations, config('site.default_locale', 'en').'.'.$field, $default);
    }
}
