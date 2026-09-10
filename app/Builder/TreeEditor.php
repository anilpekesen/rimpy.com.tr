<?php

namespace App\Builder;

/**
 * Kurucu ağacı üzerinde ekleme, taşıma, kopyalama ve silme işlemleri.
 *
 * Ağaç düz bir dizi olarak (veritabanındaki haliyle) gelir ve öyle döner;
 * böylece Livewire'ın durum taşıması ucuz kalır. Her işlem ağacı yerinde
 * değil, kopyalayarak değiştirir — geri alma yığını bu yüzden güvenilirdir.
 */
class TreeEditor
{
    /** Bir elemanı verilen kabın içine, istenen sıraya yerleştirir. */
    public static function insert(array $tree, array $element, ?string $parentId, ?int $index = null): array
    {
        if ($parentId === null) {
            $index ??= count($tree);
            array_splice($tree, $index, 0, [$element]);

            return $tree;
        }

        return self::mapNodes($tree, function (array $node) use ($element, $parentId, $index) {
            if (($node['id'] ?? null) !== $parentId) {
                return $node;
            }

            $children = $node['elements'] ?? [];
            $at = $index ?? count($children);
            array_splice($children, $at, 0, [$element]);
            $node['elements'] = $children;

            return $node;
        });
    }

    public static function remove(array $tree, string $id): array
    {
        $out = [];

        foreach ($tree as $node) {
            if (($node['id'] ?? null) === $id) {
                continue;
            }

            if (! empty($node['elements'])) {
                $node['elements'] = self::remove($node['elements'], $id);
            }

            $out[] = $node;
        }

        return $out;
    }

    public static function find(array $tree, string $id): ?array
    {
        foreach ($tree as $node) {
            if (($node['id'] ?? null) === $id) {
                return $node;
            }

            if (! empty($node['elements']) && $found = self::find($node['elements'], $id)) {
                return $found;
            }
        }

        return null;
    }

    /** Bir elemanın hangi kabın içinde ve kaçıncı sırada olduğunu bulur. */
    public static function locate(array $tree, string $id, ?string $parentId = null): ?array
    {
        foreach ($tree as $index => $node) {
            if (($node['id'] ?? null) === $id) {
                return ['parent' => $parentId, 'index' => $index];
            }

            if (! empty($node['elements'])
                && $found = self::locate($node['elements'], $id, $node['id'] ?? null)) {
                return $found;
            }
        }

        return null;
    }

    /** Elemanı yeni bir kaba taşır. Sürükle-bırak bunu çağırır. */
    public static function move(array $tree, string $id, ?string $newParentId, int $index): array
    {
        $element = self::find($tree, $id);

        if ($element === null) {
            return $tree;
        }

        // Bir kabı kendi içine sürüklemek ağacı bozardı.
        if ($newParentId !== null && self::contains($element, $newParentId)) {
            return $tree;
        }

        return self::insert(self::remove($tree, $id), $element, $newParentId, $index);
    }

    private static function contains(array $node, string $id): bool
    {
        foreach ($node['elements'] ?? [] as $child) {
            if (($child['id'] ?? null) === $id || self::contains($child, $id)) {
                return true;
            }
        }

        return false;
    }

    /** Elemanı hemen altına, yeni kimliklerle kopyalar. */
    public static function duplicate(array $tree, string $id): array
    {
        $element = self::find($tree, $id);
        $position = self::locate($tree, $id);

        if ($element === null || $position === null) {
            return $tree;
        }

        $copy = Element::fromArray($element)->withFreshIds()->toArray();

        return self::insert($tree, $copy, $position['parent'], $position['index'] + 1);
    }

    /** Tek bir elemanın ayarını değiştirir. */
    public static function updateSettings(array $tree, string $id, array $settings): array
    {
        return self::mapNodes($tree, function (array $node) use ($id, $settings) {
            if (($node['id'] ?? null) === $id) {
                $node['settings'] = $settings;
            }

            return $node;
        });
    }

    /** Ağacın her düğümüne, çocuklarıyla birlikte bir dönüşüm uygular. */
    private static function mapNodes(array $tree, callable $callback): array
    {
        return array_map(function (array $node) use ($callback) {
            $node = $callback($node);

            if (! empty($node['elements'])) {
                $node['elements'] = self::mapNodes($node['elements'], $callback);
            }

            return $node;
        }, $tree);
    }

    // ── Hazır düzenler ───────────────────────────────────────────────────

    /** Belirtilen sütun genişlikleriyle boş bir bölüm üretir. */
    public static function makeSection(array $widths = [100]): array
    {
        return [
            'id' => Element::newId(),
            'type' => 'section',
            'settings' => [
                'width' => 'boxed',
                '_padding' => ['d' => ['t' => 64, 'b' => 64, 'unit' => 'px'], 'm' => ['t' => 40, 'b' => 40, 'unit' => 'px']],
                'gap' => ['d' => ['size' => 32, 'unit' => 'px']],
            ],
            'elements' => array_map(fn ($width) => self::makeColumn($width), $widths),
        ];
    }

    public static function makeColumn(float $width = 100): array
    {
        return [
            'id' => Element::newId(),
            'type' => 'column',
            'settings' => ['width' => ['d' => $width, 't' => $width < 50 ? 50 : 100, 'm' => 100]],
            'elements' => [],
        ];
    }

    public static function makeWidget(string $key, array $settings = []): array
    {
        return [
            'id' => Element::newId(),
            'type' => 'widget',
            'widget' => $key,
            'settings' => $settings,
        ];
    }

    /** Kurucudaki "bölüm ekle" menüsünde sunulan düzenler. */
    public const LAYOUTS = [
        ['widths' => [100], 'label' => 'Tek sütun'],
        ['widths' => [50, 50], 'label' => 'İki eşit sütun'],
        ['widths' => [33.33, 33.33, 33.34], 'label' => 'Üç eşit sütun'],
        ['widths' => [25, 25, 25, 25], 'label' => 'Dört eşit sütun'],
        ['widths' => [66.66, 33.34], 'label' => 'Geniş sol, dar sağ'],
        ['widths' => [33.34, 66.66], 'label' => 'Dar sol, geniş sağ'],
        ['widths' => [25, 50, 25], 'label' => 'Ortası geniş'],
    ];
}
