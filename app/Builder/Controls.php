<?php

namespace App\Builder;

/**
 * Denetim tanımlarının fabrikası.
 *
 * Bir denetim iki işi birden yapar: panelde hangi arayüzün çizileceğini ve
 * seçilen değerin hangi CSS'e dönüşeceğini söyler. İkisi tek yerde
 * durduğu için bir widget'a yeni bir ayar eklemek tek satırlık iştir.
 */
class Controls
{
    // ── Denetim kurucuları ───────────────────────────────────────────────

    public static function text(string $label, array $options = []): array
    {
        return array_merge(['type' => 'text', 'label' => $label, 'translatable' => true], $options);
    }

    public static function textarea(string $label, array $options = []): array
    {
        return array_merge(['type' => 'textarea', 'label' => $label, 'rows' => 3, 'translatable' => true], $options);
    }

    public static function rich(string $label, array $options = []): array
    {
        return array_merge(['type' => 'rich', 'label' => $label, 'translatable' => true], $options);
    }

    public static function number(string $label, array $options = []): array
    {
        return array_merge(['type' => 'number', 'label' => $label], $options);
    }

    public static function slider(string $label, array $options = []): array
    {
        return array_merge([
            'type' => 'slider', 'label' => $label,
            'min' => 0, 'max' => 200, 'step' => 1, 'unit' => 'px', 'units' => ['px'],
            'responsive' => true,
        ], $options);
    }

    public static function select(string $label, array $choices, array $options = []): array
    {
        return array_merge(['type' => 'select', 'label' => $label, 'options' => $choices], $options);
    }

    /** Yan yana ikon düğmeler — hizalama gibi az seçenekli ayarlar için. */
    public static function choose(string $label, array $choices, array $options = []): array
    {
        return array_merge(['type' => 'choose', 'label' => $label, 'options' => $choices, 'responsive' => true], $options);
    }

    public static function toggle(string $label, array $options = []): array
    {
        return array_merge(['type' => 'toggle', 'label' => $label, 'default' => false], $options);
    }

    public static function color(string $label, array $options = []): array
    {
        return array_merge(['type' => 'color', 'label' => $label, 'property' => 'color'], $options);
    }

    public static function media(string $label, array $options = []): array
    {
        return array_merge(['type' => 'media', 'label' => $label, 'accept' => 'image'], $options);
    }

    public static function gallery(string $label, array $options = []): array
    {
        return array_merge(['type' => 'gallery', 'label' => $label], $options);
    }

    public static function link(string $label, array $options = []): array
    {
        return array_merge(['type' => 'link', 'label' => $label, 'translatable' => true], $options);
    }

    public static function icon(string $label, array $options = []): array
    {
        return array_merge(['type' => 'icon', 'label' => $label], $options);
    }

    /** Kullanıcının satır ekleyip çıkarabildiği liste (özellikler, SSS, ekip...). */
    public static function repeater(string $label, array $fields, array $options = []): array
    {
        return array_merge([
            'type' => 'repeater', 'label' => $label, 'fields' => $fields,
            'title_field' => array_key_first($fields), 'add_label' => 'Satır ekle',
        ], $options);
    }

    public static function dimensions(string $label, string $property, array $options = []): array
    {
        return array_merge([
            'type' => 'dimensions', 'label' => $label, 'property' => $property,
            'responsive' => true, 'units' => ['px', 'em', '%', 'rem'],
        ], $options);
    }

    public static function typography(string $label = 'Yazı biçimi', array $options = []): array
    {
        return array_merge(['type' => 'typography', 'label' => $label, 'responsive' => true], $options);
    }

    public static function background(string $label = 'Arka plan', array $options = []): array
    {
        return array_merge(['type' => 'background', 'label' => $label], $options);
    }

    public static function border(string $label = 'Kenarlık', array $options = []): array
    {
        return array_merge(['type' => 'border', 'label' => $label], $options);
    }

    public static function shadow(string $label = 'Gölge', array $options = []): array
    {
        return array_merge(['type' => 'shadow', 'label' => $label, 'property' => 'box-shadow'], $options);
    }

    /** İçerik listesi çeken widget'lar için kaynak seçici. */
    public static function query(string $label = 'Kaynak', array $options = []): array
    {
        return array_merge(['type' => 'query', 'label' => $label], $options);
    }

    // ── Hazır denetim kümeleri ───────────────────────────────────────────

    public static array $alignments = [
        'left' => ['label' => 'Sola', 'icon' => 'align-left'],
        'center' => ['label' => 'Ortaya', 'icon' => 'align-center'],
        'right' => ['label' => 'Sağa', 'icon' => 'align-right'],
    ];

    /**
     * Her elemanda bulunan ayarlar: dış/iç boşluk, arka plan, kenarlık,
     * gölge, animasyon ve cihaz bazlı gizleme.
     */
    public static function common(string $elementType): array
    {
        return [
            '_margin' => self::dimensions('Dış boşluk', 'margin', ['selector' => '{{WRAPPER}}']),
            '_padding' => self::dimensions('İç boşluk', 'padding', ['selector' => '{{WRAPPER}}']),
            '_background' => self::background(options: ['selector' => '{{WRAPPER}}']),
            '_border' => self::border(options: ['selector' => '{{WRAPPER}}']),
            '_shadow' => self::shadow(options: ['selector' => '{{WRAPPER}}']),
            '_zindex' => self::number('Katman sırası', ['selector' => '{{WRAPPER}}', 'property' => 'z-index']),
            '_animation' => self::select('Giriş animasyonu', [
                '' => 'Yok',
                'fade' => 'Belirerek',
                'fade-up' => 'Aşağıdan belirerek',
                'fade-down' => 'Yukarıdan belirerek',
                'fade-left' => 'Soldan belirerek',
                'fade-right' => 'Sağdan belirerek',
                'zoom' => 'Yakınlaşarak',
            ]),
            '_animation_delay' => self::slider('Animasyon gecikmesi', [
                'max' => 2000, 'step' => 50, 'unit' => 'ms', 'responsive' => false,
            ]),
            '_hide' => [
                'type' => 'device_visibility',
                'label' => 'Cihazlarda gizle',
            ],
            '_css_id' => self::text('CSS kimliği', ['translatable' => false]),
            '_css_class' => self::text('CSS sınıfı', ['translatable' => false]),
        ];
    }

    /** Yalnızca bölümlere (section) uygulanan ayarlar. */
    public static function section(): array
    {
        return [
            'width' => self::select('Genişlik', [
                'boxed' => 'Kapsayıcı içinde',
                'full' => 'Tam genişlik',
                'full-content' => 'Tam genişlik, içerik kapsayıcıda',
            ], ['default' => 'boxed']),
            'height' => self::select('Yükseklik', [
                'auto' => 'İçeriğe göre',
                'screen' => 'Ekran yüksekliği',
                'custom' => 'Özel',
            ], ['default' => 'auto']),
            'min_height' => self::slider('En az yükseklik', [
                'max' => 1200, 'units' => ['px', 'vh'],
                'selector' => '{{WRAPPER}}', 'property' => 'min-height',
                'condition' => ['height' => 'custom'],
            ]),
            'align_items' => self::choose('Dikey hizalama', [
                'flex-start' => ['label' => 'Üst', 'icon' => 'align-start-vertical'],
                'center' => ['label' => 'Orta', 'icon' => 'align-center-vertical'],
                'flex-end' => ['label' => 'Alt', 'icon' => 'align-end-vertical'],
                'stretch' => ['label' => 'Uzat', 'icon' => 'stretch-vertical'],
            ], ['selector' => '{{WRAPPER}} > .sec-inner', 'property' => 'align-items', 'default' => 'stretch']),
            'gap' => self::slider('Sütun aralığı', [
                'max' => 120, 'selector' => '{{WRAPPER}} > .sec-inner', 'property' => 'gap',
            ]),
            'reverse_mobile' => self::toggle('Mobilde sütun sırasını ters çevir'),
            'overlay' => self::background('Üst katman rengi', ['selector' => '{{WRAPPER}} > .sec-overlay']),
            'divider_top' => self::select('Üst kenar şekli', [
                '' => 'Yok', 'wave' => 'Dalga', 'tilt' => 'Eğim', 'curve' => 'Kavis', 'triangle' => 'Üçgen',
            ]),
            'divider_bottom' => self::select('Alt kenar şekli', [
                '' => 'Yok', 'wave' => 'Dalga', 'tilt' => 'Eğim', 'curve' => 'Kavis', 'triangle' => 'Üçgen',
            ]),
        ];
    }

    /** Yalnızca sütunlara uygulanan ayarlar. */
    public static function column(): array
    {
        return [
            'width' => [
                'type' => 'column_width',
                'label' => 'Genişlik',
                'responsive' => true,
                'default' => ['d' => 100, 't' => 100, 'm' => 100],
            ],
            'align_content' => self::choose('İçerik hizası', [
                'flex-start' => ['label' => 'Üst', 'icon' => 'align-start-vertical'],
                'center' => ['label' => 'Orta', 'icon' => 'align-center-vertical'],
                'flex-end' => ['label' => 'Alt', 'icon' => 'align-end-vertical'],
            ], ['selector' => '{{WRAPPER}}', 'property' => 'justify-content']),
            'gap' => self::slider('Widget aralığı', [
                'max' => 80, 'selector' => '{{WRAPPER}}', 'property' => 'gap',
            ]),
            'sticky' => self::toggle('Kaydırırken sabit kalsın'),
        ];
    }
}
