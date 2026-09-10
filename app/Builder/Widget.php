<?php

namespace App\Builder;

/**
 * Kurucuya eklenebilen her içerik parçasının temeli.
 *
 * Bir widget iki şey tanımlar: hangi ayarları aldığı (`controls`) ve o
 * ayarlarla ön yüzde ne çizdiği (`view` + `data`). Stil üretimi denetim
 * tanımlarındaki `selector` bilgisinden otomatik yapıldığı için widget'ın
 * CSS ile ilgilenmesi gerekmez.
 */
abstract class Widget
{
    abstract public function key(): string;

    abstract public function label(): string;

    /** Panelde hangi grupta listeleneceği. */
    public function category(): string
    {
        return 'basic';
    }

    public function icon(): string
    {
        return 'square';
    }

    /**
     * Sekmelere ayrılmış denetimler:
     * ['content' => ['label' => 'İçerik', 'controls' => [...]], 'style' => [...]]
     */
    abstract public function controls(): array;

    /** Ön yüzde kullanılacak Blade şablonu. */
    public function view(): string
    {
        return 'builder.widgets.'.$this->key();
    }

    /**
     * Şablona gönderilecek veri. Varsayılan olarak ayarlar olduğu gibi
     * geçirilir; içerik çeken widget'lar bunu genişletir.
     */
    public function data(Element $element, RenderContext $context): array
    {
        return ['settings' => $this->resolved($element, $context)];
    }

    /** Denetimleri sekmelerden çıkarıp tek düz diziye indirger. */
    public function flatControls(): array
    {
        $flat = [];

        foreach ($this->controls() as $tab) {
            foreach ($tab['controls'] ?? [] as $key => $control) {
                $flat[$key] = $control;
            }
        }

        return $flat;
    }

    /** Denetim tanımlarındaki varsayılanlardan boş bir ayar seti üretir. */
    public function defaults(): array
    {
        $defaults = [];

        foreach ($this->flatControls() as $key => $control) {
            if (array_key_exists('default', $control)) {
                $defaults[$key] = $control['default'];
            }
        }

        return $defaults;
    }

    /**
     * Ayarları çizime hazır hale getirir: varsayılanları uygular, çok dilli
     * alanlardan geçerli dildeki değeri seçer ve medya kimliklerini gerçek
     * adreslere çevirir.
     */
    protected function resolved(Element $element, RenderContext $context): array
    {
        $settings = array_replace($this->defaults(), $element->settings);
        $controls = $this->flatControls();
        $out = [];

        foreach ($settings as $key => $value) {
            $control = $controls[$key] ?? null;

            $out[$key] = match ($control['type'] ?? null) {
                'media' => $context->media($value),
                'gallery' => $context->mediaList($value),
                'link' => $context->link($value),
                default => $context->localize($value, ! empty($control['translatable'])),
            };
        }

        return $out;
    }

    /** Panelde yeni eklenen widget'ın örnek içerikle gelmesi için. */
    public function starterSettings(): array
    {
        return $this->defaults();
    }
}
