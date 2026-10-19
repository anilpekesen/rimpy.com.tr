<?php

namespace App\Models;

use App\Support\Locales;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id', 'parent_id', 'type', 'target_id', 'url', 'label',
        'target', 'icon', 'css_class', 'is_mega', 'sort', 'is_active',
    ];

    protected function casts(): array
    {
        return ['label' => 'array', 'is_active' => 'boolean', 'is_mega' => 'boolean'];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort');
    }

    public function activeChildren(): HasMany
    {
        return $this->children()->where('is_active', true)->with('activeChildren');
    }

    public function title(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $this->label[$locale] ?? $this->label[Locales::default()] ?? '';
    }

    /** Kategoriye bağlı öğenin görseli (mega menü sütunları için). */
    public function image(): ?Media
    {
        return $this->type === 'term'
            ? Term::with('image')->find($this->target_id)?->image
            : null;
    }

    /**
     * Öğenin gerçek adresi. Bağlı olduğu sayfa/içerik silinmiş ya da yayından
     * kaldırılmışsa null döner ve menüde hiç gösterilmez — müşteri menüde
     * kırık bağlantı bırakamaz.
     */
    public function href(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $prefix = Locales::isMultilingual() ? '/'.$locale : '';

        return match ($this->type) {
            'home' => $prefix ?: '/',
            // "{locale}" dilin ön ekine dönüşür: "{locale}/#hizmetler" → "/en/#hizmetler".
            // Aynı sayfa içi bağlantı her dilde kendi ana sayfasına gider.
            'url' => str_replace('{locale}', $prefix, (string) $this->url) ?: null,
            'page' => ($page = Page::with('translations', 'parent.translations')->find($this->target_id))
                ? $prefix.rtrim($page->path($locale), '/')
                : null,
            'content' => ($item = ContentItem::with('translations', 'type')->find($this->target_id))
                ? $prefix.$item->path($locale)
                : null,
            'term' => ($term = Term::with('translations', 'taxonomy.contentType')->find($this->target_id))
                ? $prefix.$term->path($locale)
                : null,
            'archive' => ($type = ContentType::find($this->target_id))
                ? $prefix.'/'.$type->prefix($locale)
                : null,
            default => null,
        };
    }
}
