<?php

namespace App\Builder;

use Illuminate\Support\Str;

/**
 * Kurucu ağacındaki tek bir düğüm.
 *
 * Ağaç üç tür düğümden oluşur: `section` (tam genişlik şerit), `column`
 * (şeridin içindeki dikey sütun) ve `widget` (asıl içerik). Bölümler
 * sütunların içine tekrar girebilir; böylece kullanıcı istediği kadar
 * karmaşık düzen kurabilir.
 */
class Element
{
    public function __construct(
        public string $id,
        public string $type,
        public ?string $widget = null,
        public array $settings = [],
        /** @var array<int, Element> */
        public array $elements = [],
    ) {}

    /**
     * @param  array|object  $data  Ağaç veritabanından JSON olarak da,
     *   düzenleyiciden dizi olarak da gelebilir. Boş ayar kümesi JSON'da
     *   `[]` yerine `{}` olarak yazıldığı için nesne gelme ihtimali vardır.
     */
    public static function fromArray(array|object $data): self
    {
        $data = (array) $data;

        return new self(
            id: $data['id'] ?? self::newId(),
            type: $data['type'] ?? 'widget',
            widget: $data['widget'] ?? null,
            settings: (array) ($data['settings'] ?? []),
            elements: array_map(self::fromArray(...), (array) ($data['elements'] ?? [])),
        );
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'type' => $this->type,
            'settings' => (object) $this->settings,
        ];

        if ($this->widget) {
            $data['widget'] = $this->widget;
        }

        if ($this->type !== 'widget') {
            $data['elements'] = array_map(fn (Element $e) => $e->toArray(), $this->elements);
        }

        return $data;
    }

    public static function newId(): string
    {
        return Str::lower(Str::random(8));
    }

    public function isWidget(): bool
    {
        return $this->type === 'widget';
    }

    /** CSS'te bu elemana özel kural yazmak için kullanılan sınıf. */
    public function cssClass(): string
    {
        return 'e-'.$this->id;
    }

    /** Ağacı düz bir listeye açar; derleyici ve arama bunun üzerinden çalışır. */
    public function flatten(): array
    {
        $all = [$this];

        foreach ($this->elements as $child) {
            $all = array_merge($all, $child->flatten());
        }

        return $all;
    }

    /**
     * Ağacın tamamına yeni kimlikler verir. Bir bölüm kopyalanırken ya da
     * kütüphaneden şablon eklenirken kimliklerin çakışmaması için gerekir.
     */
    public function withFreshIds(): self
    {
        return new self(
            id: self::newId(),
            type: $this->type,
            widget: $this->widget,
            settings: $this->settings,
            elements: array_map(fn (Element $e) => $e->withFreshIds(), $this->elements),
        );
    }

    /** Belirli bir ayarı cihaz kırılımıyla birlikte okur. */
    public function setting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }
}
