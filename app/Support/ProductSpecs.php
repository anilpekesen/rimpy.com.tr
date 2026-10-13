<?php

namespace App\Support;

use App\Models\ContentItem;

/**
 * Ürünün teknik özellik satırlarını okur.
 *
 * Panelde özellikler tek bir metin alanına "Etiket: Değer" satırları olarak
 * girilir: her ürün türünün kendine özgü özellikleri olduğu için sabit
 * sütunlar yerine serbest liste. Sayısal karşılaştırma gereken değerler
 * (tork, ton, strok…) ayrıca kendi alanlarında tutulur.
 */
class ProductSpecs
{
    /** @return array<int, array{label: string, value: string}> */
    public static function rows(ContentItem $item, ?string $locale = null): array
    {
        $text = (string) $item->custom('ozellikler', $locale);
        $rows = [];

        foreach (preg_split('/\R/u', $text) as $line) {
            if (! str_contains($line, ':')) {
                continue;
            }

            [$label, $value] = array_map('trim', explode(':', $line, 2));

            if ($label !== '' && $value !== '') {
                $rows[] = ['label' => $label, 'value' => $value];
            }
        }

        return $rows;
    }

    /** Kartlarda gösterilecek ilk birkaç satır. */
    public static function highlights(ContentItem $item, ?string $locale = null, int $limit = 4): array
    {
        return array_slice(self::rows($item, $locale), 0, $limit);
    }

    /** Muadil modeller, satır satır. */
    public static function equivalents(ContentItem $item, ?string $locale = null): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/u', (string) $item->custom('muadil', $locale)))));
    }

    /** Doldurulmuş belge bağlantıları. */
    public static function documents(ContentItem $item, ?string $locale = null): array
    {
        $labels = [
            'belge_test' => ['tr' => 'Test sertifikası', 'en' => 'Test certificate'],
            'belge_ce' => ['tr' => 'CE uygunluk beyanı', 'en' => 'CE declaration of conformity'],
            'belge_kalibrasyon' => ['tr' => 'Kalibrasyon sertifikası', 'en' => 'Calibration certificate'],
            'belge_katalog' => ['tr' => 'Ürün kataloğu', 'en' => 'Product catalogue'],
        ];

        $locale ??= app()->getLocale();
        $documents = [];

        foreach ($labels as $key => $label) {
            if ($url = $item->custom($key, $locale)) {
                $documents[] = ['label' => $label[$locale] ?? $label['tr'], 'url' => $url];
            }
        }

        // Tork anahtarlarında basınç–tork tablosu her zaman üretilebilir.
        if (TorqueTable::for($item)) {
            $documents[] = [
                'label' => $locale === 'en' ? 'Pressure–torque table (Excel)' : 'Basınç–tork tablosu (Excel)',
                'url' => route('tools.torque-table', $item),
            ];
        }

        return $documents;
    }
}
