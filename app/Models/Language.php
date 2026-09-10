<?php

namespace App\Models;

use App\Support\Locales;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'code', 'name', 'native_name', 'direction', 'flag', 'is_default', 'is_active', 'sort',
    ];

    protected function casts(): array
    {
        return ['is_default' => 'boolean', 'is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        // Dil listesi önbellekte tutulduğu için her değişiklikte tazelenmeli.
        static::saved(fn () => Locales::forget());
        static::deleted(fn () => Locales::forget());
    }
}
