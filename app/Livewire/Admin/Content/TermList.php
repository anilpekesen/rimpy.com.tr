<?php

namespace App\Livewire\Admin\Content;

use App\Models\ContentType;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Models\TermTranslation;
use App\Services\ActivityLogger;
use App\Support\Locales;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class TermList extends Component
{
    public ContentType $type;

    public Taxonomy $taxonomy;

    public array $names = [];

    public ?int $parentId = null;

    public ?int $editingId = null;

    public function mount(ContentType $type, Taxonomy $taxonomy): void
    {
        abort_unless($taxonomy->content_type_id === $type->id, 404);

        $this->type = $type;
        $this->taxonomy = $taxonomy;
        $this->names = Locales::map('');
    }

    public function save(): void
    {
        $rules = [];

        foreach (Locales::codes() as $code) {
            $rules["names.{$code}"] = $code === Locales::default() ? 'required|string|max:120' : 'nullable|string|max:120';
        }

        $this->validate($rules, [
            'names.'.Locales::default().'.required' => 'Bir ad girin.',
        ]);

        $term = $this->editingId
            ? Term::findOrFail($this->editingId)
            : Term::create([
                'taxonomy_id' => $this->taxonomy->id,
                'parent_id' => $this->parentId,
                'sort' => (Term::where('taxonomy_id', $this->taxonomy->id)->max('sort') ?? 0) + 1,
            ]);

        if ($this->editingId) {
            $term->update(['parent_id' => $this->parentId]);
        }

        foreach ($this->names as $code => $name) {
            if (blank($name)) {
                continue;
            }

            $term->fillTranslation($code, [
                'name' => $name,
                'slug' => $this->uniqueSlug(Str::slug($name) ?: 'terim', $code, $term->id),
            ]);
        }

        ActivityLogger::log($this->editingId ? 'updated' : 'created', $term,
            ($this->editingId ? 'Güncellendi: ' : 'Eklendi: ').reset($this->names));

        $this->reset('names', 'parentId', 'editingId');
        $this->names = Locales::map('');

        $this->dispatch('notify', message: 'Kaydedildi.');
    }

    public function edit(int $id): void
    {
        $term = Term::with('translations')->findOrFail($id);

        $this->editingId = $id;
        $this->parentId = $term->parent_id;
        $this->names = Locales::map('');

        foreach ($term->translations as $translation) {
            $this->names[$translation->locale] = $translation->name;
        }
    }

    public function delete(int $id): void
    {
        $term = Term::findOrFail($id);
        ActivityLogger::deleted($term, "Silindi: {$term->t('name')}");
        $term->delete();

        $this->dispatch('notify', message: 'Silindi. Bu kategoriye bağlı içerikler silinmedi.');
    }

    private function uniqueSlug(string $base, string $locale, int $ignoreId): string
    {
        $slug = $base;
        $suffix = 1;

        while (TermTranslation::where('locale', $locale)->where('slug', $slug)
            ->where('term_id', '!=', $ignoreId)->exists()) {
            $slug = $base.'-'.(++$suffix);
        }

        return $slug;
    }

    public function render()
    {
        return view('livewire.admin.content.term-list', [
            'terms' => Term::withTranslations()->with('children.translations')
                ->where('taxonomy_id', $this->taxonomy->id)
                ->whereNull('parent_id')
                ->withCount('items')
                ->orderBy('sort')->get(),
            'allTerms' => Term::withTranslations()->where('taxonomy_id', $this->taxonomy->id)->get(),
            'locales' => Locales::all(),
        ])->title($this->taxonomy->pluralLabel('tr'));
    }
}
