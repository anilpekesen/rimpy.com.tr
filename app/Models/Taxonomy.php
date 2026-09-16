<?php

namespace App\Models;

use App\Support\Locales;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Bir içerik türüne bağlı sınıflandırma: Kategori, Marka, Etiket... */
class Taxonomy extends Model
{
    protected $table = 'taxonomies';

    protected $fillable = [
        'content_type_id', 'key', 'name', 'plural', 'is_hierarchical', 'route_prefix', 'sort',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'plural' => 'array',
            'route_prefix' => 'array',
            'is_hierarchical' => 'boolean',
        ];
    }

    public function contentType(): BelongsTo
    {
        return $this->belongsTo(ContentType::class);
    }

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class)->orderBy('sort');
    }

    public function label(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $this->name[$locale] ?? $this->name[Locales::default()] ?? $this->key;
    }

    public function pluralLabel(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $this->plural[$locale] ?? $this->plural[Locales::default()] ?? $this->label($locale);
    }

    /**
     * Adresteki taksonomi parçası: /urunler/kategori/mermer içindeki
     * "kategori". Ön yüz çözümleyicisi de adresleri buna göre eşleştirir,
     * bu yüzden bağlantı üreten her yer aynı kaynaktan okumalıdır.
     */
    public function prefix(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $this->route_prefix[$locale] ?? $this->route_prefix[Locales::default()] ?? $this->key;
    }
}
