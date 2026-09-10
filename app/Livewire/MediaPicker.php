<?php

namespace App\Livewire;

use App\Models\Media;
use App\Services\MediaUploader;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

/** Paneldeki bütün medya alanlarının kullandığı ortak seçici. */
class MediaPicker extends Component
{
    use WithFileUploads;

    public bool $isOpen = false;

    public string $search = '';

    public string $accept = 'image';

    public bool $multiple = false;

    /** @var array<int, int> */
    public array $selected = [];

    /** Seçimi açan denetime geri gönderilecek bağlam. */
    public array $context = [];

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $uploads = [];

    public array $uploadErrors = [];

    #[On('open-media-picker')]
    public function showPicker(
        ?string $target = null,
        string $accept = 'image',
        bool $multiple = false,
        array $existing = [],
        ?string $mode = null,
        ?string $repeater = null,
        ?int $row = null,
        ?string $field = null,
    ): void {
        $this->reset('search', 'uploads', 'uploadErrors');
        $this->isOpen = true;
        $this->accept = in_array($accept, ['image', 'document', 'video'], true) ? $accept : 'image';
        $this->multiple = $multiple;
        $this->selected = array_values(array_unique(array_map('intval', $existing)));
        $this->context = array_filter([
            'target' => $target,
            'mode' => $mode,
            'repeater' => $repeater,
            'row' => $row,
            'field' => $field,
        ], static fn ($value) => $value !== null);
    }

    public function closePicker(): void
    {
        $this->reset('isOpen', 'search', 'selected', 'context', 'uploads', 'uploadErrors');
    }

    public function updatedUploads(MediaUploader $uploader): void
    {
        $this->uploadErrors = [];
        $lastUploaded = null;

        foreach ($this->uploads as $file) {
            try {
                $media = $uploader->store($file);

                if ($media->type !== $this->accept) {
                    $this->uploadErrors[] = $file->getClientOriginalName().': Bu alan için uygun dosya türü değil.';
                    continue;
                }

                $lastUploaded = $media;
                $this->selected[] = $media->id;
            } catch (\RuntimeException $e) {
                $this->uploadErrors[] = $file->getClientOriginalName().': '.$e->getMessage();
            } catch (\Throwable $e) {
                report($e);
                $this->uploadErrors[] = $file->getClientOriginalName().': Dosya işlenirken bir sorun oluştu.';
            }
        }

        $this->selected = array_values(array_unique($this->selected));
        $this->reset('uploads');

        if (! $this->multiple && $lastUploaded && ! $this->uploadErrors) {
            $this->choose($lastUploaded->id);
        }
    }

    public function toggle(int $id): void
    {
        $this->findAllowed($id);

        $this->selected = in_array($id, $this->selected, true)
            ? array_values(array_diff($this->selected, [$id]))
            : [...$this->selected, $id];
    }

    public function choose(int $id): void
    {
        $media = $this->findAllowed($id);
        $this->sendSelection(collect([$media]));
    }

    public function applySelection(): void
    {
        $items = Media::whereIn('id', $this->selected)
            ->where('type', $this->accept)
            ->get()
            ->sortBy(fn (Media $media) => array_search($media->id, $this->selected, true));

        if ($items->isNotEmpty()) {
            $this->sendSelection($items);
        }
    }

    private function findAllowed(int $id): Media
    {
        return Media::where('type', $this->accept)->findOrFail($id);
    }

    private function sendSelection($items): void
    {
        /** @var Media $first */
        $first = $items->first();

        $this->dispatch('media-selected',
            mediaId: $first->id,
            mediaIds: $items->pluck('id')->values()->all(),
            url: $first->url(),
            context: $this->context,
        );

        $this->closePicker();
    }

    public function render()
    {
        $items = collect();

        if ($this->isOpen) {
            $items = Media::query()
                ->where('type', $this->accept)
                ->when(filled($this->search), fn ($query) => $query->where('original_name', 'like', '%'.$this->search.'%'))
                ->latest()
                ->limit(60)
                ->get();
        }

        return view('livewire.media-picker', ['items' => $items]);
    }
}
