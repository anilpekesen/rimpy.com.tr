<?php

namespace App\Builder;

use App\Models\Media;
use App\Support\Locales;
use Illuminate\Support\Collection;

/**
 * Çizim sırasında elemanların ihtiyaç duyduğu ortak bilgi ve yardımcılar.
 *
 * Medya kayıtları ve bağlantı hedefleri tek seferde toplu olarak yüklenir;
 * yüz widget'lı bir sayfa yüz ayrı sorgu açmaz.
 */
class RenderContext
{
    /** @var Collection<int, Media> */
    private Collection $mediaCache;

    public function __construct(
        public string $locale,
        public bool $editing = false,
        public mixed $subject = null,
    ) {
        $this->mediaCache = collect();
    }

    /** Ağaçtaki tüm medya kimliklerini tek sorguda önden yükler. */
    public function preloadMedia(Tree $tree): void
    {
        $ids = [];

        foreach ($tree->flatten() as $element) {
            foreach ($element->settings as $value) {
                if (is_int($value) || (is_string($value) && ctype_digit($value))) {
                    $ids[] = (int) $value;
                } elseif (is_array($value)) {
                    foreach ($value as $inner) {
                        if (is_int($inner)) {
                            $ids[] = $inner;
                        }
                    }
                }
            }
        }

        if ($ids) {
            $this->mediaCache = Media::whereIn('id', array_unique($ids))->get()->keyBy('id');
        }
    }

    public function media(mixed $id): ?Media
    {
        if (blank($id) || ! is_numeric($id)) {
            return null;
        }

        return $this->mediaCache->get((int) $id)
            ?? $this->mediaCache->put((int) $id, Media::find((int) $id))->get((int) $id);
    }

    /** @return Collection<int, Media> */
    public function mediaList(mixed $ids): Collection
    {
        if (! is_array($ids)) {
            return collect();
        }

        return collect($ids)->map(fn ($id) => $this->media($id))->filter()->values();
    }

    /**
     * Bağlantı ayarını ön yüzde kullanılabilir bir yapıya çevirir.
     * Kullanıcı ya bir sayfa seçer ya da elle adres girer.
     */
    public function link(mixed $value): array
    {
        $empty = ['url' => null, 'target' => '_self', 'rel' => null];

        if (! is_array($value)) {
            return $empty;
        }

        $url = match ($value['type'] ?? 'url') {
            'page' => $this->pageUrl($value['id'] ?? null),
            'content' => $this->contentUrl($value['id'] ?? null),
            'anchor' => '#'.ltrim((string) ($value['anchor'] ?? ''), '#'),
            'email' => 'mailto:'.($value['email'] ?? ''),
            'phone' => 'tel:'.preg_replace('/[^\d+]/', '', (string) ($value['phone'] ?? '')),
            'file' => $this->media($value['id'] ?? null)?->url(),
            default => $this->localize($value['url'] ?? null, true),
        };

        return [
            'url' => $url ?: null,
            'target' => ($value['target'] ?? '_self') === '_blank' ? '_blank' : '_self',
            // Yeni sekmede açılan dış bağlantılarda sekme kaçırma açığını kapatır.
            'rel' => ($value['target'] ?? null) === '_blank' ? 'noopener noreferrer' : (! empty($value['nofollow']) ? 'nofollow' : null),
        ];
    }

    /**
     * Çok dilli bir değerden geçerli dildeki karşılığı seçer.
     *
     * Sayfa kurucusunda ağaç zaten dile göre ayrı tutulduğu için değerler
     * düz gelir. Üst başlık/alt bilgi gibi tüm dillerde ortak şablonlarda
     * ise değer bir dil haritasıdır ve burada çözülür.
     */
    public function localize(mixed $value, bool $translatable = false): mixed
    {
        if (! $translatable || ! is_array($value)) {
            return $value;
        }

        // Dil haritası mı, yoksa sıradan bir dizi mi?
        $codes = Locales::codes();
        $keys = array_keys($value);

        if (! array_intersect($keys, $codes)) {
            return $value;
        }

        return $value[$this->locale] ?? $value[Locales::default()] ?? reset($value) ?: null;
    }

    /** Çok dilli sitelerde adreslerin başına dil ön eki koyar. */
    public function prefix(): string
    {
        return Locales::isMultilingual() ? '/'.$this->locale : '';
    }

    private function pageUrl(mixed $id): ?string
    {
        if (! $id) {
            return null;
        }

        $page = \App\Models\Page::with('translations', 'parent.translations')->find($id);

        return $page ? $this->prefix().rtrim($page->path($this->locale), '/') ?: $this->prefix().'/' : null;
    }

    private function contentUrl(mixed $id): ?string
    {
        if (! $id) {
            return null;
        }

        $item = \App\Models\ContentItem::with('translations', 'type')->find($id);

        return $item ? $this->prefix().$item->path($this->locale) : null;
    }
}
