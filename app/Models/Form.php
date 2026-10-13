<?php

namespace App\Models;

use App\Support\Locales;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    protected $fillable = [
        'key', 'name', 'fields', 'submit_label', 'success_message', 'redirect_url',
        'notify_emails', 'store_submissions', 'send_autoreply', 'autoreply_subject',
        'autoreply_body', 'spam_protection',
    ];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'submit_label' => 'array',
            'success_message' => 'array',
            'autoreply_subject' => 'array',
            'store_submissions' => 'boolean',
            'send_autoreply' => 'boolean',
            'spam_protection' => 'boolean',
        ];
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class)->latest();
    }

    public function unreadCount(): int
    {
        return $this->submissions()->where('is_read', false)->where('is_spam', false)->count();
    }

    /** @return array<int, string> */
    public function notifyList(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->notify_emails))));
    }

    /**
     * Gönderideki bir anahtarın okunur adı. Formun kendi alanlarının yanında
     * sistemin eklediği değerler de vardır (teklif sepetindeki ürünler gibi).
     */
    public function fieldLabel(string $key, string $locale = 'tr'): string
    {
        $field = collect($this->fields ?? [])->firstWhere('key', $key);

        if ($field) {
            return $field['label'][$locale] ?? $field['label'][Locales::default()] ?? $key;
        }

        return [
            'urunler' => ['tr' => 'Talep edilen ürünler', 'en' => 'Requested products'],
        ][$key][$locale] ?? $key;
    }

    public function localized(string $attribute, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $values = $this->{$attribute} ?? [];

        return $values[$locale] ?? $values[Locales::default()] ?? null;
    }
}
