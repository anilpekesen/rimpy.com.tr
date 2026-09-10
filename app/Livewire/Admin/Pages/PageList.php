<?php

namespace App\Livewire\Admin\Pages;

use App\Models\Page;
use App\Services\ActivityLogger;
use App\Support\Locales;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.admin')]
class PageList extends Component
{
    #[Url(as: 'ara')]
    public string $search = '';

    #[Url(as: 'durum')]
    public string $status = '';

    public bool $creating = false;

    public string $newTitle = '';

    public function createPage()
    {
        $this->validate(
            ['newTitle' => 'required|string|max:180'],
            ['newTitle.required' => 'Sayfaya bir başlık verin.']
        );

        $page = Page::create([
            'status' => 'draft',
            'author_id' => auth()->id(),
            'sort' => (Page::max('sort') ?? 0) + 1,
        ]);

        // Her dil için bir çeviri satırı açılır; müşteri diğer dile geçtiğinde
        // "önce şunu oluşturun" gibi bir engelle karşılaşmaz.
        foreach (Locales::codes() as $code) {
            $page->fillTranslation($code, [
                'title' => $this->newTitle,
                'slug' => $this->uniqueSlug(Str::slug($this->newTitle) ?: 'sayfa', $code),
            ]);
        }

        ActivityLogger::created($page, "Sayfa oluşturuldu: {$this->newTitle}");

        $this->reset('newTitle', 'creating');

        // Yeni sayfa doğrudan kurucuda açılır — müşteri hemen tasarlamaya başlar.
        return $this->redirect(route('admin.builder.page', $page), navigate: false);
    }

    private function uniqueSlug(string $base, string $locale, ?int $ignoreId = null): string
    {
        $slug = $base;
        $suffix = 1;

        while (\App\Models\PageTranslation::where('locale', $locale)
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('page_id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.(++$suffix);
        }

        return $slug;
    }

    public function duplicate(int $id)
    {
        $original = Page::with('translations')->findOrFail($id);

        $copy = $original->replicate(['is_home', 'view_count']);
        $copy->status = 'draft';
        $copy->is_home = false;
        $copy->author_id = auth()->id();
        $copy->save();

        foreach ($original->translations as $translation) {
            $attributes = $translation->replicate()->toArray();
            $attributes['title'] = $translation->title.' (kopya)';
            $attributes['slug'] = $this->uniqueSlug($translation->slug.'-kopya', $translation->locale);
            unset($attributes['page_id'], $attributes['id']);

            $copy->fillTranslation($translation->locale, $attributes);
        }

        ActivityLogger::created($copy, "Sayfa kopyalandı: {$original->t('title')}");
        $this->dispatch('notify', message: 'Sayfa kopyalandı.');
    }

    public function setHome(int $id): void
    {
        Page::where('is_home', true)->update(['is_home' => false]);

        $page = Page::findOrFail($id);
        $page->update(['is_home' => true, 'status' => 'published', 'published_at' => $page->published_at ?? now()]);

        ActivityLogger::updated($page, "Ana sayfa olarak ayarlandı: {$page->t('title')}");
        $this->dispatch('notify', message: 'Ana sayfa güncellendi.');
    }

    public function togglePublish(int $id): void
    {
        $page = Page::findOrFail($id);

        $publishing = $page->status !== 'published';

        $page->update([
            'status' => $publishing ? 'published' : 'draft',
            'published_at' => $publishing ? ($page->published_at ?? now()) : $page->published_at,
        ]);

        ActivityLogger::updated($page, ($publishing ? 'Yayına alındı: ' : 'Yayından kaldırıldı: ').$page->t('title'));
        $this->dispatch('notify', message: $publishing ? 'Sayfa yayında.' : 'Sayfa yayından kaldırıldı.');
    }

    public function delete(int $id): void
    {
        $page = Page::findOrFail($id);

        if ($page->is_home) {
            $this->dispatch('notify', message: 'Ana sayfa silinemez. Önce başka bir sayfayı ana sayfa yapın.');

            return;
        }

        ActivityLogger::deleted($page, "Sayfa silindi: {$page->t('title')}");
        $page->delete();

        $this->dispatch('notify', message: 'Sayfa çöp kutusuna taşındı.');
    }

    public function render()
    {
        $pages = Page::query()
            ->withTranslations()
            ->with('parent.translations')
            ->when(filled($this->search), fn ($q) => $q->whereHas(
                'translations',
                fn ($t) => $t->where('title', 'like', '%'.$this->search.'%')
            ))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderByDesc('is_home')
            ->orderBy('sort')
            ->paginate(30);

        return view('livewire.admin.pages.page-list', [
            'pages' => $pages,
            'counts' => [
                'all' => Page::count(),
                'published' => Page::where('status', 'published')->count(),
                'draft' => Page::where('status', 'draft')->count(),
            ],
        ])->title('Sayfalar');
    }
}
