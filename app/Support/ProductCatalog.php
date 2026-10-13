<?php

namespace App\Support;

use App\Models\ContentItem;
use App\Models\ContentType;
use App\Models\Term;
use Illuminate\Support\Collection;

/**
 * Mühendislik araçlarının (tork hesaplayıcı, silindir seçici) okuduğu ürün
 * verisi. Her ürün yalnızca hesaba giren sayısal alanlarıyla ve sepete
 * eklenebilmesi için kimliği, adresi ve görseliyle döner.
 */
class ProductCatalog
{
    private const NUMERIC = [
        'tork_maks_nm', 'tork_min_nm', 'basinc_bar', 'kapasite_ton', 'strok_mm',
        'yag_hacmi_cm3', 'kullanilabilir_yag_cm3', 'agirlik_kg', 'germe_yuk_kn',
    ];

    /**
     * Bir kategori ve tüm alt kategorilerindeki yayındaki ürünler.
     *
     * @return Collection<int, array>
     */
    public static function inCategory(string $slug, ?string $locale = null): Collection
    {
        $locale ??= app()->getLocale();

        $term = Term::whereHas('translations', fn ($q) => $q->where('locale', Locales::default())->where('slug', $slug))
            ->with('children')
            ->first();

        if (! $term) {
            return collect();
        }

        $ids = $term->children->pluck('id')->push($term->id);
        $type = ContentType::where('key', 'urunler')->first();
        $prefix = Urls::prefix($locale);

        return ContentItem::withTranslations()
            ->with('featuredImage', 'type')
            ->where('content_type_id', $type?->id)
            ->whereHas('terms', fn ($q) => $q->whereIn('terms.id', $ids))
            ->live()
            ->orderBy('sort')
            ->get()
            ->map(function (ContentItem $item) use ($locale, $prefix) {
                $row = [
                    'id' => $item->id,
                    'code' => $item->custom('model_kodu', $locale),
                    'title' => $item->t('title', $locale),
                    'url' => $prefix.$item->path($locale),
                    'image' => $item->featuredImage?->url('thumb'),
                    'drive' => $item->custom('kare_tahrik', $locale),
                    'hex' => self::range($item->custom('altigen_aralik', $locale)),
                    'action' => $item->custom('etki', $locale),
                    'bolts' => self::boltRange($item->custom('civata_aralik', $locale)),
                ];

                foreach (self::NUMERIC as $key) {
                    $value = $item->custom($key, $locale);
                    $row[$key] = is_numeric($value) ? (float) $value : null;
                }

                return $row;
            })
            ->values();
    }

    /** "M42-M52" → [42, 52], "M48" → [48, 48]; cıvata çapı, mm. */
    private static function boltRange(?string $value): ?array
    {
        if (! $value || ! preg_match_all('/M\s*(\d+)/i', $value, $m)) {
            return null;
        }

        $sizes = array_map('intval', $m[1]);

        return [min($sizes), max($sizes)];
    }

    /** "41-95" → [41, 95] */
    private static function range(?string $value): ?array
    {
        if (! $value || ! preg_match('/(\d+)\s*-\s*(\d+)/', $value, $m)) {
            return null;
        }

        return [(int) $m[1], (int) $m[2]];
    }
}
