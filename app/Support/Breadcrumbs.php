<?php

namespace App\Support;

use App\Models\ContentItem;
use App\Models\Page;
use Illuminate\Database\Eloquent\Model;

/**
 * Ziyaretçinin sitede nerede olduğunu gösteren yol izi.
 */
class Breadcrumbs
{
    /** @return array<int, array{label: string, url: ?string}> */
    public static function for(?Model $subject, string $locale, string $homeLabel = 'Ana Sayfa'): array
    {
        $prefix = Urls::prefix($locale);
        $trail = [['label' => $homeLabel, 'url' => $prefix ?: '/']];

        if ($subject instanceof Page) {
            // Sayfa zincirini kökten aşağıya doğru kur.
            $chain = [];
            $node = $subject;
            $guard = 0;

            while ($node && $guard++ < 10) {
                array_unshift($chain, $node);
                $node = $node->parent;
            }

            foreach ($chain as $page) {
                if ($page->is_home) {
                    continue;
                }

                $trail[] = [
                    'label' => (string) $page->t('title', $locale),
                    'url' => $prefix.rtrim($page->path($locale), '/'),
                ];
            }
        } elseif ($subject instanceof ContentItem) {
            $type = $subject->type;

            if ($type?->has_archive) {
                $trail[] = [
                    'label' => $type->pluralLabel($locale),
                    'url' => $prefix.'/'.$type->prefix($locale),
                ];
            }

            $trail[] = [
                'label' => (string) $subject->t('title', $locale),
                'url' => null,
            ];
        }

        return $trail;
    }

    /** Arama motorlarının okuduğu yapısal veri. */
    public static function schema(array $trail): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($trail)->values()->map(fn ($crumb, $index) => array_filter([
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['label'],
                'item' => $crumb['url'] ? url($crumb['url']) : null,
            ]))->all(),
        ];
    }
}
