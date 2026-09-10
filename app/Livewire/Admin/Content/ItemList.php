<?php

namespace App\Livewire\Admin\Content;

use App\Models\ContentItem;
use App\Models\ContentType;
use App\Models\Term;
use App\Services\ActivityLogger;
use App\Support\Locales;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class ItemList extends Component
{
    use WithPagination;

    public ContentType $type;

    #[Url(as: 'ara')]
    public string $search = '';

    #[Url(as: 'durum')]
    public string $status = '';

    #[Url(as: 'kategori')]
    public ?int $termId = null;

    public bool $creating = false;

    public string $newTitle = '';

    public function mount(ContentType $type): void
    {
        $this->type = $type->load('taxonomies');
    }

    public function create()
    {
        $this->validate(
            ['newTitle' => 'required|string|max:180'],
            ['newTitle.required' => 'Bir başlık girin.']
        );

        $item = ContentItem::create([
            'content_type_id' => $this->type->id,
            'status' => 'draft',
            'author_id' => auth()->id(),
            'sort' => (ContentItem::where('content_type_id', $this->type->id)->max('sort') ?? 0) + 1,
        ]);

        foreach (Locales::codes() as $code) {
            $item->fillTranslation($code, [
                'title' => $this->newTitle,
                'slug' => $this->uniqueSlug(Str::slug($this->newTitle) ?: 'kayit', $code),
            ]);
        }

        ActivityLogger::created($item, "{$this->type->label('tr')} eklendi: {$this->newTitle}");

        $this->reset('newTitle', 'creating');

        return $this->redirect(route('admin.content.edit', [$this->type->key, $item->id]), navigate: true);
    }

    private function uniqueSlug(string $base, string $locale): string
    {
        $slug = $base;
        $suffix = 1;

        while (\App\Models\ContentItemTranslation::where('locale', $locale)->where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$suffix);
        }

        return $slug;
    }

    public function togglePublish(int $id): void
    {
        $item = ContentItem::findOrFail($id);
        $publishing = $item->status !== 'published';

        $item->update([
            'status' => $publishing ? 'published' : 'draft',
            'published_at' => $publishing ? ($item->published_at ?? now()) : $item->published_at,
        ]);

        ActivityLogger::updated($item, ($publishing ? 'Yayınlandı: ' : 'Yayından kaldırıldı: ').$item->t('title'));
    }

    public function toggleFeatured(int $id): void
    {
        $item = ContentItem::findOrFail($id);
        $item->update(['is_featured' => ! $item->is_featured]);
    }

    public function delete(int $id): void
    {
        $item = ContentItem::findOrFail($id);
        ActivityLogger::deleted($item, "Silindi: {$item->t('title')}");
        $item->delete();

        $this->dispatch('notify', message: 'Kayıt silindi.');
    }

    public function render()
    {
        $items = ContentItem::query()
            ->where('content_type_id', $this->type->id)
            ->withTranslations()
            ->with('featuredImage', 'terms.translations')
            ->when(filled($this->search), fn ($q) => $q->whereHas(
                'translations', fn ($t) => $t->where('title', 'like', '%'.$this->search.'%')
            ))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->termId, fn ($q) => $q->whereHas('terms', fn ($t) => $t->where('terms.id', $this->termId)))
            ->orderBy('sort')
            ->orderByDesc('published_at')
            ->paginate(25);

        return view('livewire.admin.content.item-list', [
            'items' => $items,
            'terms' => Term::withTranslations()
                ->whereHas('taxonomy', fn ($q) => $q->where('content_type_id', $this->type->id))
                ->get(),
        ])->title($this->type->pluralLabel('tr'));
    }
}
