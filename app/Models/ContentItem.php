<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentItem extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'content_type_id', 'parent_id', 'status', 'published_at', 'featured_image_id',
        'gallery', 'is_featured', 'sort', 'author_id',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'gallery' => 'array',
        ];
    }

    public static function translationModel(): string
    {
        return ContentItemTranslation::class;
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ContentType::class, 'content_type_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort');
    }

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    public function terms(): BelongsToMany
    {
        return $this->belongsToMany(Term::class, 'content_item_term');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopeLive(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeOfType(Builder $query, string $key): Builder
    {
        return $query->whereHas('type', fn ($q) => $q->where('key', $key));
    }

    /** Kullanıcı tanımlı ek alanın değeri. */
    public function custom(string $key, ?string $locale = null): mixed
    {
        return data_get($this->t('custom', $locale), $key);
    }

    public function path(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        if ($this->type->hasFlatUrls()) {
            return '/'.$this->t('slug', $locale);
        }

        return '/'.trim($this->type->prefix($locale), '/').'/'.$this->t('slug', $locale);
    }
}
