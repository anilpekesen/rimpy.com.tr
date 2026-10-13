<?php

namespace App\Services;

use App\Models\ContentItem;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

/**
 * Teklif sepeti.
 *
 * Fiyatı sitede gösterilmeyen kataloglarda ziyaretçi ürünleri bir listeye
 * toplar ve tek seferde teklif ister. Sepet yalnızca oturumda tutulur:
 * üyelik, sipariş ya da ödeme yoktur; listedeki ürünler teklif formu
 * gönderildiğinde mesaja eklenir ve sepet boşalır.
 *
 * Oturumda yalnızca kayıt kimliği ve adet saklanır. Başlık, kod ve görsel
 * her okumada veritabanından gelir; böylece panelde düzeltilen bir ürün adı
 * sepette eski hâliyle kalmaz, yayından kaldırılan ürün de kendiliğinden düşer.
 */
class QuoteCart
{
    private const KEY = 'quote_cart';

    public function __construct(private Session $session) {}

    /** @return array<int, int>  kayıt kimliği => adet */
    public function raw(): array
    {
        return $this->session->get(self::KEY, []);
    }

    public function add(int $itemId, int $quantity = 1): void
    {
        $items = $this->raw();

        // Sınırsız satır, oturum tablosunu şişirmenin kolay bir yolu olurdu.
        if (! isset($items[$itemId]) && count($items) >= $this->maxLines()) {
            return;
        }

        $items[$itemId] = $this->clamp(($items[$itemId] ?? 0) + $quantity);

        $this->session->put(self::KEY, $items);
    }

    public function set(int $itemId, int $quantity): void
    {
        $items = $this->raw();

        if ($quantity < 1) {
            unset($items[$itemId]);
        } elseif (isset($items[$itemId])) {
            $items[$itemId] = $this->clamp($quantity);
        }

        $this->session->put(self::KEY, $items);
    }

    public function remove(int $itemId): void
    {
        $this->set($itemId, 0);
    }

    public function clear(): void
    {
        $this->session->forget(self::KEY);
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    public function isEmpty(): bool
    {
        return $this->raw() === [];
    }

    /** Sepete eklenebilecek bir kayıt mı: yayında ve teklif verilen türden. */
    public function accepts(ContentItem $item): bool
    {
        return $item->status === 'published'
            && in_array($item->type?->key, config('cms.quote.content_types', []), true);
    }

    /**
     * Sepetteki ürünler, çizime hazır hâliyle.
     *
     * @return Collection<int, array{id: int, title: string, code: ?string, url: string, image: ?string, quantity: int}>
     */
    public function lines(?string $locale = null): Collection
    {
        $raw = $this->raw();

        if ($raw === []) {
            return collect();
        }

        $items = ContentItem::withTranslations()
            ->with('type', 'featuredImage')
            ->whereKey(array_keys($raw))
            ->live()
            ->get()
            ->keyBy('id');

        // Artık bulunmayan kayıtlar oturumdan da temizlenir.
        if ($items->count() !== count($raw)) {
            $this->session->put(self::KEY, array_intersect_key($raw, $items->all()));
        }

        $prefix = \App\Support\Urls::prefix($locale ?? app()->getLocale());

        return collect($raw)
            ->filter(fn ($quantity, $id) => $items->has($id))
            ->map(fn ($quantity, $id) => [
                'id' => (int) $id,
                'title' => $items[$id]->t('title', $locale),
                'code' => $items[$id]->custom('model_kodu', $locale),
                'url' => $prefix.$items[$id]->path($locale),
                'image' => $items[$id]->featuredImage?->url('thumb'),
                'quantity' => (int) $quantity,
            ])
            ->values();
    }

    /**
     * Form mesajına eklenecek düz metin.
     *
     * E-postada ve paneldeki mesaj ekranında olduğu gibi okunur; ayrıca
     * dışa aktarılan tabloda tek hücreye sığar.
     */
    public function summary(?string $locale = null): string
    {
        return $this->lines($locale)
            ->map(fn (array $line) => sprintf(
                '%d × %s%s',
                $line['quantity'],
                $line['code'] ? $line['code'].' — ' : '',
                $line['title'],
            ))
            ->implode("\n");
    }

    /** Ön yüzdeki betiğin beklediği biçim. */
    public function toArray(?string $locale = null): array
    {
        $lines = $this->lines($locale);

        return [
            'count' => $lines->sum('quantity'),
            'lines' => $lines->all(),
        ];
    }

    private function clamp(int $quantity): int
    {
        return max(1, min($quantity, (int) config('cms.quote.max_quantity', 999)));
    }

    private function maxLines(): int
    {
        return (int) config('cms.quote.max_lines', 50);
    }
}
