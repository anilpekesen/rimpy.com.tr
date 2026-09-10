<?php

namespace App\Models\Concerns;

use App\Support\Locales;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Çeviri satırlarını ayrı bir tabloda tutan modeller için ortak davranış.
 *
 * Modelin kendisi dilden bağımsız verileri (durum, sıra, ilişkiler), çeviri
 * satırı ise dile bağlı olanları (başlık, adres, içerik) taşır. `t()` istenen
 * dilde değer bulamazsa varsayılan dile düşer; böylece müşteri İngilizce
 * içeriği henüz girmemişken site boş görünmez.
 */
trait HasTranslations
{
    /** Çeviri satırlarını tutan model sınıfı. */
    abstract public static function translationModel(): string;

    /** Çeviri tablosundaki yabancı anahtar. */
    public static function translationForeignKey(): string
    {
        return str(class_basename(static::class))->snake()->toString().'_id';
    }

    public function translations(): HasMany
    {
        return $this->hasMany(static::translationModel(), static::translationForeignKey());
    }

    /**
     * Belirtilen dildeki çeviri satırı. Yoksa varsayılan dile, o da yoksa
     * eldeki ilk çeviriye düşer.
     */
    public function translation(?string $locale = null): ?Model
    {
        $locale ??= app()->getLocale();

        $loaded = $this->relationLoaded('translations')
            ? $this->translations
            : $this->translations()->get()->tap(fn ($rows) => $this->setRelation('translations', $rows));

        return $loaded->firstWhere('locale', $locale)
            ?? $loaded->firstWhere('locale', Locales::default())
            ?? $loaded->first();
    }

    /** Çeviri satırından tek bir alanı okur. */
    public function t(string $field, ?string $locale = null): mixed
    {
        $row = $this->translation($locale);

        if ($row === null) {
            return null;
        }

        // İstenen dilde alan boşsa varsayılan dildeki dolu değeri kullan.
        $value = $row->{$field};

        if (blank($value) && $row->locale !== Locales::default()) {
            $fallback = $this->translations->firstWhere('locale', Locales::default());
            $value = $fallback?->{$field} ?? $value;
        }

        return $value;
    }

    /** Verilen dilde bir çeviri satırı var mı? */
    public function hasTranslation(string $locale): bool
    {
        return $this->translations->contains('locale', $locale);
    }

    /** Çeviri satırını oluşturur ya da günceller. */
    public function fillTranslation(string $locale, array $attributes): Model
    {
        $row = $this->translations()->updateOrCreate(['locale' => $locale], $attributes);

        $this->unsetRelation('translations');

        return $row;
    }

    /** Sorguları çeviri satırıyla birlikte yükler. */
    public function scopeWithTranslations($query, ?string $locale = null)
    {
        return $query->with(['translations' => function ($q) use ($locale) {
            if ($locale) {
                $q->whereIn('locale', array_unique([$locale, Locales::default()]));
            }
        }]);
    }

    /** Adrese (slug) göre kayıt bulur. */
    public function scopeWhereSlug($query, string $slug, ?string $locale = null)
    {
        $locale ??= app()->getLocale();

        return $query->whereHas('translations', fn ($q) => $q->where('slug', $slug)->where('locale', $locale));
    }
}
