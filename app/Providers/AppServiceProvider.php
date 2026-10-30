<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Limites par adresse IP contre le spam d'emails / commandes et l'énumération de commandes.
        $m = config('shop.throttle_multiplier');
        $tooMany = fn (Request $request, array $headers) => response()->json([
            'success' => false,
            'message' => t('js.too_many'),
        ], 429, $headers);

        foreach (['diagnostic' => 6, 'checkout' => 10, 'tracking' => 15, 'cart' => 120, 'contact' => 8, 'chat' => 40] as $name => $perMinute) {
            RateLimiter::for($name, fn (Request $request) => Limit::perMinute($perMinute * $m)
                ->by($request->ip())
                ->response($tooMany));
        }
    }
}
