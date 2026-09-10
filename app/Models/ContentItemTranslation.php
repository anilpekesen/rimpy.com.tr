<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentItemTranslation extends Model
{
    protected $fillable = [
        'content_item_id', 'locale', 'title', 'slug', 'excerpt', 'body',
        'builder_data', 'compiled_css', 'custom', 'seo_title', 'seo_description', 'noindex',
    ];

    protected function casts(): array
    {
        return ['builder_data' => 'array', 'custom' => 'array', 'noindex' => 'boolean'];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ContentItem::class, 'content_item_id');
    }
}
