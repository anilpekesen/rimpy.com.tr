<?php

namespace App\Livewire\Admin\Content;

use App\Models\ContentItem;
use App\Models\ContentItemTranslation;
use App\Models\ContentType;
use App\Models\Term;
use App\Services\ActivityLogger;
use App\Support\Locales;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class ItemEdit extends Component
{
    public ContentType $type;

    public ContentItem $item;

    public array $form = [];

    public array $translations = [];

    /** @var array<int, int> */
    public array $selectedTerms = [];

    public string $activeLocale = 'tr';

    public function mount(ContentType $type, ContentItem $item): void
    {
        abort_unless($item->content_type_id === $type->id, 404);

        $this->type = $type->load('taxonomies.terms.translations');
        $this->item = $item->load('translations', 'terms');
        $this->activeLocale = Locales::default();

        $this->form = [
            'status' => $item->status,
            'published_at' => $item->published_at?->format('Y-m-d\TH:i'),
            'featured_image_id' => $item->featured_image_id,
            'gallery' => $item->gallery ?? [],
            'is_featured' => $item->is_featured,
            'sort' => $item->sort,
        ];

        $this->selectedTerms = $item->terms->pluck('id')->all();

        foreach (Locales::codes() as $code) {
            $t = $item->translation($code);

            $this->translations[$code] = [
                'title' => $t?->title ?? '',
                'slug' => $t?->slug ?? '',
                'excerpt' => $t?->excerpt ?? '',
                'body' => $t?->body ?? '',
                'custom' => $t?->custom ?? [],
                'seo_title' => $t?->seo_title ?? '',
                'seo_description' => $t?->seo_description ?? '',
                'noindex' => (bool) ($t?->noindex ?? false),
            ];
        }
    }

    public function save()
    {
        $rules = [
            'form.status' => 'required|in:draft,published,scheduled',
            'form.published_at' => 'nullable|date',
        ];

        foreach (Locales::codes() as $code) {
            $rules["translations.{$code}.title"] = 'required|string|max:190';
            $rules["translations.{$code}.seo_description"] = 'nullable|string|max:320';

            // İçerik türünde tanımlanmış zorunlu ek alanlar
            foreach ($this->type->fields ?? [] as $field) {
                if (! empty($field['required'])) {
                    $rules["translations.{$code}.custom.{$field['key']}"] = 'required';
                }
            }
        }

        $this->validate($rules);

        $this->item->update([
            'status' => $this->form['status'],
            'published_at' => $this->form['published_at'] ?: ($this->form['status'] === 'published' ? now() : null),
            'featured_image_id' => $this->form['featured_image_id'] ?: null,
            'gallery' => $this->form['gallery'] ?: null,
            'is_featured' => (bool) ($this->form['is_featured'] ?? false),
            'sort' => (int) ($this->form['sort'] ?? 0),
        ]);

        foreach ($this->translations as $code => $values) {
            $slug = Str::slug($values['slug'] ?: $values['title']) ?: 'kayit';

            $this->item->fillTranslation($code, [
                'title' => $values['title'],
                'slug' => $this->uniqueSlug($slug, $code),
                'excerpt' => $values['excerpt'] ?: null,
                'body' => \App\Support\Html::sanitize($values['body']) ?: null,
                'custom' => $values['custom'] ?: null,
                'seo_title' => $values['seo_title'] ?: null,
                'seo_description' => $values['seo_description'] ?: null,
                'noindex' => (bool) $values['noindex'],
            ]);
        }

        $this->item->terms()->sync($this->selectedTerms);

        ActivityLogger::updated($this->item, "Güncellendi: {$this->item->t('title')}");

        $this->dispatch('notify', message: 'Kaydedildi.');
    }

    private function uniqueSlug(string $base, string $locale): string
    {
        $slug = $base;
        $suffix = 1;

        while (ContentItemTranslation::where('locale', $locale)->where('slug', $slug)
            ->where('content_item_id', '!=', $this->item->id)->exists()) {
            $slug = $base.'-'.(++$suffix);
        }

        return $slug;
    }

    public function render()
    {
        return view('livewire.admin.content.item-edit', [
            'locales' => Locales::all(),
        ])->title($this->item->t('title') ?: $this->type->label('tr'));
    }
}
