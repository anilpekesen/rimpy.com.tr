<?php

namespace App\Livewire\Admin\Pages;

use App\Models\Page;
use App\Models\PageTranslation;
use App\Services\ActivityLogger;
use App\Support\Locales;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class PageSettings extends Component
{
    public Page $page;

    public array $form = [];

    /** Dile göre başlık, adres ve SEO alanları */
    public array $translations = [];

    public string $activeLocale = 'tr';

    public function mount(Page $page): void
    {
        $this->page = $page->load('translations');
        $this->activeLocale = Locales::default();

        $this->form = [
            'parent_id' => $page->parent_id,
            'status' => $page->status,
            'published_at' => $page->published_at?->format('Y-m-d\TH:i'),
            'show_in_search' => $page->show_in_search,
            'sort' => $page->sort,
        ];

        foreach (Locales::codes() as $code) {
            $t = $page->translation($code);

            $this->translations[$code] = [
                'title' => $t?->title ?? '',
                'slug' => $t?->slug ?? '',
                'excerpt' => $t?->excerpt ?? '',
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
            'form.parent_id' => 'nullable|exists:pages,id',
            'form.sort' => 'nullable|integer|min:0',
        ];

        foreach (Locales::codes() as $code) {
            $rules["translations.{$code}.title"] = 'required|string|max:180';
            $rules["translations.{$code}.seo_description"] = 'nullable|string|max:320';
        }

        $this->validate($rules, array_merge(
            ...array_map(fn ($c) => ["translations.{$c}.title.required" => 'Başlık zorunludur ('.Locales::name($c).').'], Locales::codes())
        ));

        // Sayfa kendi alt sayfasının altına taşınamaz.
        if ($this->form['parent_id'] && $this->isDescendant((int) $this->form['parent_id'])) {
            $this->addError('form.parent_id', 'Bir sayfa kendi alt sayfasının altına taşınamaz.');

            return;
        }

        $this->page->update([
            'parent_id' => $this->form['parent_id'] ?: null,
            'status' => $this->form['status'],
            'published_at' => $this->form['published_at'] ?: ($this->form['status'] === 'published' ? now() : null),
            'show_in_search' => (bool) ($this->form['show_in_search'] ?? true),
            'sort' => (int) ($this->form['sort'] ?? 0),
        ]);

        foreach ($this->translations as $code => $values) {
            $slug = Str::slug($values['slug'] ?: $values['title']) ?: 'sayfa';

            $this->page->fillTranslation($code, [
                'title' => $values['title'],
                'slug' => $this->uniqueSlug($slug, $code),
                'excerpt' => $values['excerpt'] ?: null,
                'seo_title' => $values['seo_title'] ?: null,
                'seo_description' => $values['seo_description'] ?: null,
                'noindex' => (bool) $values['noindex'],
            ]);
        }

        ActivityLogger::updated($this->page, 'Sayfa ayarları güncellendi: '.$this->page->t('title'));

        $this->dispatch('notify', message: 'Ayarlar kaydedildi.');

        return $this->redirect(route('admin.pages.index'), navigate: true);
    }

    private function isDescendant(int $candidateParentId): bool
    {
        $node = Page::find($candidateParentId);
        $guard = 0;

        while ($node && $guard++ < 12) {
            if ($node->id === $this->page->id) {
                return true;
            }

            $node = $node->parent;
        }

        return false;
    }

    private function uniqueSlug(string $base, string $locale): string
    {
        $slug = $base;
        $suffix = 1;

        while (PageTranslation::where('locale', $locale)->where('slug', $slug)
            ->where('page_id', '!=', $this->page->id)->exists()) {
            $slug = $base.'-'.(++$suffix);
        }

        return $slug;
    }

    public function render()
    {
        return view('livewire.admin.pages.page-settings', [
            'locales' => Locales::all(),
            'parents' => Page::withTranslations()->where('id', '!=', $this->page->id)->orderBy('sort')->get(),
        ])->title('Sayfa ayarları');
    }
}
