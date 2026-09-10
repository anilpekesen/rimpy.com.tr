<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmission extends Model
{
    protected $fillable = [
        'form_id', 'data', 'page_url', 'ip_address', 'user_agent', 'locale', 'is_read', 'is_spam',
    ];

    protected function casts(): array
    {
        return ['data' => 'array', 'is_read' => 'boolean', 'is_spam' => 'boolean'];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /** Listede özet olarak gösterilecek ilk anlamlı değer. */
    public function preview(): string
    {
        foreach ($this->data as $value) {
            if (is_string($value) && filled($value)) {
                return str($value)->limit(80)->toString();
            }
        }

        return '—';
    }
}
