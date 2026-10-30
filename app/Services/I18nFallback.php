<?php

namespace App\Services;

use App\Support\I18n;

/** Intentions du chat dans la langue par défaut (utilisées en complément quand l'utilisateur écrit en anglais). */
class I18nFallback
{
    public static function intents(): array
    {
        $default = config('site.default_locale', 'en');

        return app()->getLocale() === $default ? [] : (I18n::tree($default)['chat']['intents'] ?? []);
    }
}
