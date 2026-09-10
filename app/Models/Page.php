<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'parent_id', 'status', 'published_at', 'is_home', 'show_in_search',
        'visibility', 'password', 'header_template_id', 'footer_template_id',
        'sort', 'author_id',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_home' => 'boolean',
            'show_in_search' => 'boolean',
        ];
    }

    public static function translationModel(): string
    {
        return PageTranslation::class;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(PageRevision::class)->latest('created_at');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /** Ziyaretçinin görebileceği sayfalar. */
    public function scopeLive(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    /**
     * Sayfanın ön yüzdeki adresi. Alt sayfalar üst sayfanın adresini
     * devralır: /kurumsal/tarihce
     */
    public function path(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        if ($this->is_home) {
            return '/';
        }

        $segments = [];
        $node = $this;
        $guard = 0;

        while ($node && $guard++ < 10) {
            $slug = $node->t('slug', $locale);

            if (blank($slug)) {
                break;
            }

            array_unshift($segments, $slug);
            $node = $node->parent;
        }

        return '/'.implode('/', $segments);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'published' => 'Yayında',
            'scheduled' => 'Zamanlanmış',
            default => 'Taslak',
        };
    }
}
