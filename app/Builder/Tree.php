<?php

namespace App\Builder;

/**
 * Bir sayfanın kurucu ağacının tamamı. Veritabanında JSON olarak saklanır,
 * burada nesneye dönüşür.
 */
class Tree
{
    /** @param array<int, Element> $elements */
    public function __construct(public array $elements = []) {}

    public static function fromArray(array|object|null $data): self
    {
        if (empty($data)) {
            return new self;
        }

        $data = (array) $data;

        // Hem {"elements": [...]} hem de doğrudan [...] biçimini kabul et.
        $elements = (array) ($data['elements'] ?? (array_is_list($data) ? $data : []));

        return new self(array_map(Element::fromArray(...), $elements));
    }

    public function toArray(): array
    {
        return [
            'version' => 1,
            'elements' => array_map(fn (Element $e) => $e->toArray(), $this->elements),
        ];
    }

    public function isEmpty(): bool
    {
        return $this->elements === [];
    }

    /** @return array<int, Element> */
    public function flatten(): array
    {
        return array_merge(...array_map(fn (Element $e) => $e->flatten(), $this->elements)) ?: [];
    }

    public function find(string $id): ?Element
    {
        foreach ($this->flatten() as $element) {
            if ($element->id === $id) {
                return $element;
            }
        }

        return null;
    }

    /** Ağaçta kullanılan widget anahtarları — hangi varlıkların yükleneceğini bilmek için. */
    public function widgetKeys(): array
    {
        return array_values(array_unique(array_filter(
            array_map(fn (Element $e) => $e->widget, $this->flatten())
        )));
    }
}
