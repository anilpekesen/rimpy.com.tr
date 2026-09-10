<?php

namespace App\Support;

use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Sitenin dilleri tek yerden okunur ve önbelleğe alınır. Diller nadiren
 * değiştiği için her istekte veritabanına gitmenin anlamı yok.
 */
class Locales
{
    private const CACHE_KEY = 'cms.languages';

    /**
     * @return Collection<int, object>
     *
     * Önbelleğe yalnızca düz diziler yazılır. Eloquent modelleri ya da
     * anonim nesneler saklansaydı, tablo yapısı değiştiğinde önbellekten
     * bozuk nesneler dönerdi.
     */
    public static function all(): Collection
    {
        $rows = Cache::rememberForever(self::CACHE_KEY, function (): array {
            // Tablo henüz kurulmadıysa (ilk kurulum) yapılandırmadan oku.
            try {
                $languages = Language::query()
                    ->where('is_active', true)
                    ->orderBy('sort')
                    ->get(['code', 'name', 'native_name', 'direction', 'flag', 'is_default'])
                    ->map(fn (Language $l) => $l->only(['code', 'name', 'native_name', 'direction', 'flag', 'is_default']))
                    ->all();
            } catch (\Throwable) {
                $languages = [];
            }

            if ($languages === []) {
                $languages = array_map(
                    fn (array $l) => $l + ['direction' => 'ltr', 'flag' => null],
                    config('cms.default_languages', [])
                );
            }

            return $languages;
        });

        return collect($rows)->map(fn (array $row) => (object) $row);
    }

    /** @return array<int, string> */
    public static function codes(): array
    {
        return self::all()->pluck('code')->all();
    }

    public static function default(): string
    {
        return self::all()->firstWhere('is_default', true)?->code
            ?? self::all()->first()?->code
            ?? config('app.locale');
    }

    public static function isMultilingual(): bool
    {
        return self::all()->count() > 1;
    }

    public static function exists(string $code): bool
    {
        return in_array($code, self::codes(), true);
    }

    public static function name(string $code): string
    {
        return self::all()->firstWhere('code', $code)?->native_name ?? strtoupper($code);
    }

    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /** Boş bir dil haritası: ['tr' => null, 'en' => null] */
    public static function map(mixed $value = null): array
    {
        return array_fill_keys(self::codes(), $value);
    }
}
