<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TermTranslation extends Model
{
    protected $fillable = [
        'term_id', 'locale', 'name', 'slug', 'description', 'seo_title', 'seo_description',
    ];

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
