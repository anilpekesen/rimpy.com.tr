<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    private const CACHE_KEY = 'cms.settings';

    protected $fillable = ['group', 'key', 'value'];

    protected function casts(): array
    {
        return ['value' => 'json'];
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::flushCaches());
        static::deleted(fn () => self::flushCaches());
    }

    private static function flushCaches(): void
    {
        Cache::forget(self::CACHE_KEY);

        // Tema jetonları ayarlardan türetilir; onlar da tazelenmeli.
        if (app()->resolved(\App\Support\Theme::class)) {
            app(\App\Support\Theme::class)->flush();
        }
    }

    /** Tüm ayarlar: ['general' => ['site_name' => '...'], ...] */
    public static function all(mixed $columns = ['*']): mixed
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            try {
                return static::query()->get()->groupBy('group')
                    ->map(fn ($rows) => $rows->pluck('value', 'key')->all())
                    ->all();
            } catch (\Throwable) {
                return [];
            }
        });
    }

    public static function get(string $path, mixed $default = null): mixed
    {
        return data_get(static::all(), $path, $default);
    }

    public static function put(string $group, string $key, mixed $value): void
    {
        static::updateOrCreate(compact('group', 'key'), compact('value'));
    }

    /** Bir grubun tamamını tek seferde yazar. */
    public static function putGroup(string $group, array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(compact('group', 'key'), ['value' => $value]);
        }

        self::flushCaches();
    }
}
