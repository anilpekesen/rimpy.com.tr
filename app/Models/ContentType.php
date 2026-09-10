<?php

namespace App\Models;

use App\Support\Locales;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Kullanıcının panelden tanımladığı içerik türü: Ürünler, Haberler,
 * Referanslar, Projeler... Yeni bir tür açmak kod değişikliği gerektirmez;
 * liste ekranı, düzenleme formu ve ön yüz adresleri buradaki tanımdan üretilir.
 */
class ContentType extends Model
{
    protected $fillable = [
        'key', 'name', 'plural', 'icon', 'is_hierarchical', 'has_archive',
        'route_prefix', 'supports', 'fields', 'sort', 'is_active', 'is_system',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'plural' => 'array',
            'route_prefix' => 'array',
            'supports' => 'array',
            'fields' => 'array',
            'is_hierarchical' => 'boolean',
            'has_archive' => 'boolean',
            'is_active' => 'boolean',
            'is_system' => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(ContentItem::class);
    }

    public function taxonomies(): HasMany
    {
        return $this->hasMany(Taxonomy::class)->orderBy('sort');
    }

    public function label(?string $locale = null): string
    {
        return $this->localized($this->name, $locale) ?? $this->key;
    }

    public function pluralLabel(?string $locale = null): string
    {
        return $this->localized($this->plural, $locale) ?? $this->label($locale);
    }

    public function prefix(?string $locale = null): string
    {
        return $this->localized($this->route_prefix, $locale) ?? $this->key;
    }

    public function supports(string $feature): bool
    {
        return in_array($feature, $this->supports ?? [], true);
    }

    /** Tanımlı ek alanlardan birini anahtarına göre döndürür. */
    public function field(string $key): ?array
    {
        foreach ($this->fields ?? [] as $field) {
            if (($field['key'] ?? null) === $key) {
                return $field;
            }
        }

        return null;
    }

    private function localized(?array $values, ?string $locale): ?string
    {
        $locale ??= app()->getLocale();

        return $values[$locale] ?? $values[Locales::default()] ?? null;
    }
}
