<?php

namespace App\Livewire\Admin;

use App\Models\Media;
use App\Models\MediaFolder;
use App\Services\ActivityLogger;
use App\Services\MediaUploader;
use App\Support\Locales;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class MediaLibrary extends Component
{
    use WithFileUploads, WithPagination;

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $uploads = [];

    #[Url(as: 'klasor')]
    public ?int $folderId = null;

    #[Url(as: 'ara')]
    public string $search = '';

    #[Url(as: 'tur')]
    public string $type = '';

    public ?int $editingId = null;

    public array $editing = ['alt' => [], 'title' => [], 'description' => ''];

    public bool $creatingFolder = false;

    public string $newFolderName = '';

    /** @var array<int, int> */
    public array $selected = [];

    public array $uploadErrors = [];

    public function updatedUploads(MediaUploader $uploader): void
    {
        $this->uploadErrors = [];

        foreach ($this->uploads as $file) {
            try {
                $uploader->store($file, $this->folderId);
            } catch (\RuntimeException $e) {
                // Kullanıcıya hangi dosyanın neden kabul edilmediği söylenir.
                $this->uploadErrors[] = $file->getClientOriginalName().': '.$e->getMessage();
            } catch (\Throwable $e) {
                report($e);
                $this->uploadErrors[] = $file->getClientOriginalName().': Dosya işlenirken bir sorun oluştu.';
            }
        }

        $this->reset('uploads');
        $this->resetPage();

        if (! $this->uploadErrors) {
            $this->dispatch('notify', message: 'Dosyalar yüklendi.');
        }
    }

    public function createFolder(): void
    {
        $this->validate(
            ['newFolderName' => 'required|string|max:80'],
            ['newFolderName.required' => 'Klasöre bir ad verin.']
        );

        MediaFolder::create([
            'parent_id' => $this->folderId,
            'name' => $this->newFolderName,
            'slug' => Str::slug($this->newFolderName) ?: Str::random(6),
        ]);

        $this->reset('newFolderName', 'creatingFolder');
        $this->dispatch('notify', message: 'Klasör oluşturuldu.');
    }

    public function openFolder(?int $id): void
    {
        $this->folderId = $id;
        $this->selected = [];
        $this->resetPage();
    }

    public function edit(int $id): void
    {
        $media = Media::findOrFail($id);

        $this->editingId = $id;
        $this->editing = [
            'alt' => array_replace(Locales::map(''), $media->alt ?? []),
            'title' => array_replace(Locales::map(''), $media->title ?? []),
            'description' => $media->description ?? '',
        ];
    }

    public function saveEdit(): void
    {
        $media = Media::findOrFail($this->editingId);

        $media->update([
            'alt' => $this->editing['alt'],
            'title' => $this->editing['title'],
            'description' => $this->editing['description'],
        ]);

        ActivityLogger::updated($media, "Medya bilgileri güncellendi: {$media->original_name}");

        $this->reset('editingId');
        $this->dispatch('notify', message: 'Kaydedildi.');
    }

    public function deleteSelected(): void
    {
        $items = Media::whereIn('id', $this->selected)->get();

        foreach ($items as $media) {
            ActivityLogger::deleted($media, "Medya silindi: {$media->original_name}");
            $media->delete();
        }

        $count = $items->count();
        $this->selected = [];
        $this->resetPage();
        $this->dispatch('notify', message: "{$count} dosya silindi.");
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Media::query()->latest();

        // Arama yapılırken klasör sınırı kalkar; kullanıcı dosyayı nerede
        // olduğunu hatırlamadan da bulabilsin.
        if (filled($this->search)) {
            $query->where('original_name', 'like', '%'.$this->search.'%');
        } else {
            $query->where('folder_id', $this->folderId);
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        return view('livewire.admin.media-library', [
            'items' => $query->paginate(48),
            'folders' => MediaFolder::where('parent_id', $this->folderId)->orderBy('name')->get(),
            'currentFolder' => $this->folderId ? MediaFolder::find($this->folderId) : null,
            'locales' => Locales::all(),
            'usage' => [
                'count' => Media::count(),
                'size' => Media::sum('size'),
            ],
        ])->title('Medya');
    }
}
