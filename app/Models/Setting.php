<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected const CACHE_KEY = 'settings.all';

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::allCached()->get($key, $default);
    }

    public static function getBool(string $key, bool $default = true): bool
    {
        $value = static::get($key);

        return $value === null ? $default : (bool) $value;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(static::CACHE_KEY);
    }

    protected static function allCached()
    {
        return Cache::rememberForever(static::CACHE_KEY, fn () => static::query()->pluck('value', 'key'));
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(static::CACHE_KEY));
        static::deleted(fn () => Cache::forget(static::CACHE_KEY));
    }
}
