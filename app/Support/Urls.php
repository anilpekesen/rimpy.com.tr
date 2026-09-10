<?php

namespace App\Support;

use App\Models\ContentItem;
use App\Models\ContentType;
use App\Models\Page;

/**
 * Adres üretimi ve diller arası eşleme.
 */
class Urls
{
    /** Çok dilli sitede adreslerin başına gelen dil ön eki. */
    public static function prefix(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return Locales::isMultilingual() ? '/'.$locale : '';
    }

    /**
     * Bir adresin başka dildeki karşılığını bulur.
     *
     * Ziyaretçi "Hakkımızda" sayfasındayken dil değiştirdiğinde İngilizce
     * "About" sayfasına gitmeli; ana sayfaya atılmamalı. Karşılığı olmayan
     * bir içerik için o dilin ana sayfasına düşülür.
     */
    public static function translate(string $path, string $from, string $to): string
    {
        $path = trim($path, '/');

        // Mevcut dil ön ekini ayıkla
        if (Locales::isMultilingual() && str_starts_with($path.'/', $from.'/')) {
            $path = trim(substr($path, strlen($from)), '/');
        }

        $target = self::prefix($to);

        if ($path === '') {
            return $target ?: '/';
        }

        $segments = explode('/', $path);

        // Sayfa mı?
        if ($page = self::findPageByPath($segments, $from)) {
            $translated = rtrim($page->path($to), '/');

            return ($target.$translated) ?: ($target ?: '/');
        }

        // İçerik detayı mı? (/urunler/bir-urun)
        if (count($segments) >= 2) {
            $item = ContentItem::withTranslations()->with('type')
                ->whereHas('translations', fn ($q) => $q->where('locale', $from)->where('slug', end($segments)))
                ->first();

            if ($item && $item->hasTranslation($to)) {
                return $target.$item->path($to);
            }
        }

        // Arşiv mi? (/urunler)
        $type = ContentType::all()->first(
            fn (ContentType $t) => $t->prefix($from) === $segments[0]
        );

        if ($type) {
            return $target.'/'.$type->prefix($to);
        }

        return $target ?: '/';
    }

    /** Adres parçalarından hiyerarşik sayfayı bulur. */
    public static function findPageByPath(array $segments, ?string $locale = null): ?Page
    {
        $locale ??= app()->getLocale();
        $parentId = null;
        $page = null;

        foreach ($segments as $segment) {
            $page = Page::withTranslations()
                ->with('parent.translations')
                ->where('parent_id', $parentId)
                ->whereHas('translations', fn ($q) => $q->where('locale', $locale)->where('slug', $segment))
                ->first();

            if (! $page) {
                return null;
            }

            $parentId = $page->id;
        }

        return $page;
    }

    /** Arama motorlarına dil karşılıklarını bildiren etiketler. */
    public static function alternates(string $path): array
    {
        if (! Locales::isMultilingual()) {
            return [];
        }

        $current = app()->getLocale();
        $out = [];

        foreach (Locales::codes() as $code) {
            $out[$code] = url(self::translate($path, $current, $code));
        }

        return $out;
    }
}
