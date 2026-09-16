<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slide extends Model
{
    protected $fillable = [
        'slider_id', 'image_id', 'eyebrow', 'title', 'text', 'button_label',
        'link', 'align', 'sort', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'eyebrow' => 'array', 'title' => 'array', 'text' => 'array',
            'button_label' => 'array', 'link' => 'array', 'is_active' => 'boolean',
        ];
    }

    public function slider(): BelongsTo
    {
        return $this->belongsTo(Slider::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
    }
}
