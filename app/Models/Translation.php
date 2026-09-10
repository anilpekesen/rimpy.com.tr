<?php

namespace App\Models;

use App\Support\Locales;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/** Ön yüzdeki sabit arayüz metinleri. Müşteri bunları da panelden değiştirir. */
class Translation extends Model
{
    private const CACHE_KEY = 'cms.translations';

    protected $fillable = ['group', 'key', 'values'];

    protected function casts(): array
    {
        return ['values' => 'array'];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    public static function map(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            try {
                return static::query()->get()->keyBy(fn ($r) => $r->group.'.'.$r->key)
                    ->map->values->all();
            } catch (\Throwable) {
                return [];
            }
        });
    }

    public static function get(string $key, ?string $fallback = null, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $key = str_contains($key, '.') ? $key : "site.{$key}";
        $values = static::map()[$key] ?? [];

        return $values[$locale] ?? $values[Locales::default()] ?? $fallback ?? $key;
    }
}
