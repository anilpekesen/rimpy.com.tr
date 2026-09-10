<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Kurucuda hazırlanmış, tekrar kullanılabilir yapı. Üst başlık, alt bilgi,
 * kaydedilmiş bir bölüm ya da hazır sayfa şablonu olabilir.
 */
class Template extends Model
{
    protected $fillable = [
        'name', 'type', 'builder_data', 'compiled_css', 'thumbnail',
        'category', 'is_global', 'is_default', 'is_starter', 'author_id',
    ];

    protected function casts(): array
    {
        return [
            'builder_data' => 'array',
            'is_global' => 'boolean',
            'is_default' => 'boolean',
            'is_starter' => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
