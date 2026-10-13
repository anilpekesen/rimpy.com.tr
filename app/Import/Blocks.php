<?php

namespace App\Import;

use App\Builder\Element;
use App\Builder\StyleCompiler;
use App\Builder\Tree;

/**
 * Kurucu ağacını elle kurmak için kısa yollar.
 *
 * Panelde sürükleyerek yapılan iş burada PHP ile yapılır. Üretilen yapı
 * kurucunun kendi kaydettiğiyle birebir aynıdır: müşteri panele girip her
 * bölümü, sütunu ve widget'ı normal şekilde düzenleyebilir.
 */
class Blocks
{
    /** Bölüm. */
    public static function section(array $settings, array $children): array
    {
        return [
            'id' => Element::newId(),
            'type' => 'section',
            'settings' => $settings,
            'elements' => $children,
        ];
    }

    /**
     * Sütun. $width yüzde: 100, 50, 33.33...
     *
     * @param  array{t?: int|float, m?: int|float}|null  $devices  Tablet ve
     *   telefonda farklı genişlik gerekiyorsa (logo ile menünün mobilde de
     *   yan yana durması gibi) burada verilir.
     */
    public static function column(int|float $width, array $children, array $settings = [], ?array $devices = null): array
    {
        $responsive = [
            'd' => $width,
            't' => $devices['t'] ?? ($width >= 50 ? 100 : 50),
            'm' => $devices['m'] ?? 100,
        ];

        return [
            'id' => Element::newId(),
            'type' => 'column',
            'settings' => ['width' => $responsive] + $settings,
            'elements' => $children,
        ];
    }

    /** Widget. */
    public static function widget(string $key, array $settings = []): array
    {
        return [
            'id' => Element::newId(),
            'type' => 'widget',
            'widget' => $key,
            'settings' => $settings,
        ];
    }

    // ── Sık kullanılan ayar biçimleri ────────────────────────────────────

    /** Dikey iç boşluk. */
    public static function padY(int $top, int $bottom, ?array $mobile = null): array
    {
        $value = ['d' => ['t' => $top, 'b' => $bottom, 'unit' => 'px']];

        if ($mobile) {
            $value['m'] = ['t' => $mobile[0], 'b' => $mobile[1], 'unit' => 'px'];
        }

        return $value;
    }

    /** Dört yönlü iç/dış boşluk. */
    public static function box(int $t, int $r, int $b, int $l, string $unit = 'px'): array
    {
        return ['d' => ['t' => $t, 'r' => $r, 'b' => $b, 'l' => $l, 'unit' => $unit]];
    }

    public static function bgColor(string $color): array
    {
        return ['type' => 'color', 'color' => $color];
    }

    public static function bgImage(string $url, string $size = 'cover', string $position = 'center center'): array
    {
        return ['type' => 'image', 'url' => $url, 'size' => $size, 'position' => $position];
    }

    /** Tipografi ayarı; ölçüler cihazlara göre verilebilir. */
    public static function type(array $desktop, ?array $mobile = null): array
    {
        return $mobile ? ['d' => $desktop, 'm' => $mobile] : ['d' => $desktop];
    }

    /** Kaydırıcı (slider tipi) denetim değeri. */
    public static function size(int|float $size, string $unit = 'px', ?array $devices = null): array
    {
        $value = ['d' => ['size' => $size, 'unit' => $unit]];

        foreach ($devices ?? [] as $device => $deviceSize) {
            $value[$device] = ['size' => $deviceSize, 'unit' => $unit];
        }

        return $value;
    }

    /** Cihaza göre değişen basit değer (hizalama gibi). */
    public static function responsive(mixed $desktop, mixed $mobile = null): array
    {
        return $mobile === null ? ['d' => $desktop] : ['d' => $desktop, 'm' => $mobile];
    }

    /** Bağlantı denetimi değeri. */
    public static function link(string $url, bool $newTab = false): array
    {
        return ['url' => $url, 'target' => $newTab ? '_blank' : '_self'];
    }

    // ── Ağacı kaydedilebilir hale getirme ────────────────────────────────

    /**
     * Eleman dizisini veritabanına yazılacak biçime çevirir ve CSS'ini
     * derler. Kurucunun kaydetme yolunda yaptığının aynısı.
     *
     * @return array{0: array, 1: string}  [builder_data, compiled_css]
     */
    public static function compile(array $elements): array
    {
        $tree = Tree::fromArray($elements);

        return [$tree->toArray(), app(StyleCompiler::class)->compile($tree)];
    }
}
