<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class PageSetting extends Model
{
    protected $fillable = ['page_key', 'texts', 'images', 'styles'];

    protected const CACHE_KEY = 'page_settings.all';

    protected function casts(): array
    {
        return ['texts' => 'array', 'images' => 'array', 'styles' => 'array'];
    }

    /** Toutes les surcharges, indexées par page (une requête, mise en cache jusqu'à la prochaine sauvegarde). */
    public static function allCached(): Collection
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, function () {
                return Schema::hasTable('page_settings') ? static::query()->get()->keyBy('page_key') : collect();
            });
        } catch (\Throwable) {
            return collect();
        }
    }

    public static function forPage(string $key): ?self
    {
        return static::allCached()->get($key);
    }

    protected static function booted(): void
    {
        $flush = function () {
            Cache::forget(self::CACHE_KEY);
            \App\Support\I18n::flush();
        };
        static::saved($flush);
        static::deleted($flush);
    }
}
