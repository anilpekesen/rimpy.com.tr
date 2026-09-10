<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageRevision extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['page_id', 'locale', 'user_id', 'builder_data', 'note'];

    protected function casts(): array
    {
        return ['builder_data' => 'array', 'created_at' => 'datetime'];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
