<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageTranslation extends Model
{
    protected $fillable = [
        'page_id', 'locale', 'title', 'slug', 'excerpt', 'builder_data', 'compiled_css',
        'seo_title', 'seo_description', 'og_image_id', 'canonical_url', 'noindex',
    ];

    protected function casts(): array
    {
        return ['builder_data' => 'array', 'noindex' => 'boolean'];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
