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
 * Ürünün seçenekleri varsa (lokma ölçüsü gibi) her seçenek ayrı satırdır;
 * satır anahtarı "kimlik|seçenek" biçimindedir, seçeneksiz üründe yalnızca
 * kimlik.
 *
 * Oturumda yalnızca satır anahtarı ve adet saklanır. Başlık, kod ve görsel
 * her okumada veritabanından gelir; böylece panelde düzeltilen bir ürün adı
 * sepette eski hâliyle kalmaz, yayından kaldırılan ürün de kendiliğinden düşer.
 */
class QuoteCart
{
    private const KEY = 'quote_cart';

    public function __construct(private Session $session) {}

    /** @return array<string, int>  satır anahtarı => adet */
    public function raw(): array
    {
        return $this->session->get(self::KEY, []);
    }

    public static function key(int $itemId, ?string $option = null): string
    {
        return filled($option) ? $itemId.'|'.$option : (string) $itemId;
    }

    /** @return array{0: int, 1: ?string} */
    public static function parse(string|int $key): array
    {
        [$id, $option] = array_pad(explode('|', (string) $key, 2), 2, null);

        return [(int) $id, $option];
    }

    public function add(int $itemId, int $quantity = 1, ?string $option = null): void
    {
        $items = $this->raw();
        $key = self::key($itemId, $option);

        // Sınırsız satır, oturum tablosunu şişirmenin kolay bir yolu olurdu.
        if (! isset($items[$key]) && count($items) >= $this->maxLines()) {
            return;
        }

        $items[$key] = $this->clamp(($items[$key] ?? 0) + $quantity);

        $this->session->put(self::KEY, $items);
    }

    public function set(string $key, int $quantity): void
    {
        $items = $this->raw();

        if ($quantity < 1) {
            unset($items[$key]);
        } elseif (isset($items[$key])) {
            $items[$key] = $this->clamp($quantity);
        }

        $this->session->put(self::KEY, $items);
    }

    public function remove(string $key): void
    {
        $this->set($key, 0);
    }

    /** Ürünün panelde tanımlı seçenekleri (her satıra bir tane). */
    public static function options(ContentItem $item, ?string $locale = null): array
    {
        return array_values(array_filter(array_map(
            fn ($line) => trim(str_replace('|', '/', $line)),
            preg_split('/\R/u', (string) $item->custom('secenekler', $locale))
        )));
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
     * @return Collection<int, array{key: string, id: int, option: ?string, title: string, code: ?string, url: string, image: ?string, quantity: int}>
     */
    public function lines(?string $locale = null): Collection
    {
        $raw = $this->raw();

        if ($raw === []) {
            return collect();
        }

        $items = ContentItem::withTranslations()
            ->with('type', 'featuredImage')
            ->whereKey(array_map(fn ($key) => self::parse($key)[0], array_keys($raw)))
            ->live()
            ->get()
            ->keyBy('id');

        // Artık bulunmayan kayıtlar oturumdan da temizlenir.
        $kept = array_filter($raw, fn ($quantity, $key) => $items->has(self::parse($key)[0]), ARRAY_FILTER_USE_BOTH);

        if (count($kept) !== count($raw)) {
            $this->session->put(self::KEY, $kept);
        }

        $prefix = \App\Support\Urls::prefix($locale ?? app()->getLocale());

        return collect($kept)
            ->map(function ($quantity, $key) use ($items, $locale, $prefix) {
                [$id, $option] = self::parse($key);

                return [
                    'key' => (string) $key,
                    'id' => $id,
                    'option' => $option,
                    'title' => $items[$id]->t('title', $locale),
                    'code' => $items[$id]->custom('model_kodu', $locale),
                    'url' => $prefix.$items[$id]->path($locale),
                    'image' => $items[$id]->featuredImage?->url('thumb'),
                    'quantity' => (int) $quantity,
                ];
            })
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
                '%d × %s%s%s',
                $line['quantity'],
                $line['code'] ? $line['code'].' — ' : '',
                $line['title'],
                $line['option'] ? ' ('.$line['option'].')' : '',
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
