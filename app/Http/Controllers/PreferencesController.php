<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Langue, devise et newsletter. */
class PreferencesController extends Controller
{
    public function locale(string $locale): RedirectResponse
    {
        abort_unless(array_key_exists($locale, config('site.locales')), 404);
        session(['locale' => $locale]);
        session()->forget('currency');   // la devise suit la langue sauf choix explicite ensuite

        return back()->withCookie(cookie('locale', $locale, 60 * 24 * 365));
    }

    public function currency(string $code): RedirectResponse
    {
        abort_unless(isset(config('site.currency.rates')[$code]), 404);
        session(['currency' => $code]);

        return back();
    }

    public function newsletter(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:190']]);
        NewsletterSubscriber::firstOrCreate(['email' => strtolower($data['email'])], ['locale' => app()->getLocale()]);

        return response()->json(['success' => true, 'message' => t('home.newsletter.thanks')]);
    }
}
