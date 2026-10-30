<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** Détermine la langue : session > cookie > langue par défaut (anglais). */
class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $available = array_keys(config('site.locales'));
        $locale = session('locale') ?: $request->cookie('locale');

        if (! in_array($locale, $available, true)) {
            $locale = config('site.default_locale');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
