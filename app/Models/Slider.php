<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Slider extends Model
{
    protected $fillable = ['name', 'key', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function slides(): HasMany
    {
        return $this->hasMany(Slide::class)->orderBy('sort');
    }

    public function activeSlides(): HasMany
    {
        return $this->slides()->where('is_active', true)->with('image');
    }
}
