<?php

namespace App\Livewire\Admin\Design;

use App\Models\Slide;
use App\Models\Slider;
use App\Services\ActivityLogger;
use App\Support\Locales;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class SliderManager extends Component
{
    public ?int $sliderId = null;

    public bool $creatingSlider = false;

    public string $newSliderName = '';

    public string $newSliderKey = '';

    public bool $editingSlide = false;

    public ?int $editingSlideId = null;

    public array $slideForm = [];

    public function mount(): void
    {
        $this->sliderId = Slider::orderBy('id')->value('id');
        $this->resetSlideForm();
    }

    private function resetSlideForm(): void
    {
        $this->slideForm = [
            'image_id' => null,
            'eyebrow' => Locales::map(''),
            'title' => Locales::map(''),
            'text' => Locales::map(''),
            'button_label' => Locales::map(''),
            'link_url' => '',
            'new_tab' => false,
            'align' => 'left',
            'is_active' => true,
        ];
    }

    public function createSlider(): void
    {
        $this->validate([
            'newSliderName' => 'required|string|max:100',
            'newSliderKey' => 'required|alpha_dash|max:60|unique:sliders,key',
        ], [
            'newSliderName.required' => 'Slider adını girin.',
            'newSliderKey.required' => 'Slider anahtarını girin.',
            'newSliderKey.unique' => 'Bu anahtar zaten kullanılıyor.',
        ]);

        $slider = Slider::create([
            'name' => $this->newSliderName,
            'key' => $this->newSliderKey,
        ]);

        ActivityLogger::created($slider, "Slider oluşturuldu: {$slider->name}");
        $this->sliderId = $slider->id;
        $this->reset('creatingSlider', 'newSliderName', 'newSliderKey');
        $this->dispatch('notify', message: 'Slider oluşturuldu.');
    }

    public function addSlide(): void
    {
        if (! $this->sliderId) {
            return;
        }

        $this->editingSlideId = null;
        $this->resetSlideForm();
        $this->editingSlide = true;
    }

    public function editSlide(int $id): void
    {
        $slide = Slide::where('slider_id', $this->sliderId)->findOrFail($id);

        $this->editingSlideId = $slide->id;
        $this->slideForm = [
            'image_id' => $slide->image_id,
            'eyebrow' => array_replace(Locales::map(''), $slide->eyebrow ?? []),
            'title' => array_replace(Locales::map(''), $slide->title ?? []),
            'text' => array_replace(Locales::map(''), $slide->text ?? []),
            'button_label' => array_replace(Locales::map(''), $slide->button_label ?? []),
            'link_url' => $slide->link['url'] ?? '',
            'new_tab' => ($slide->link['target'] ?? '_self') === '_blank',
            'align' => $slide->align,
            'is_active' => $slide->is_active,
        ];
        $this->editingSlide = true;
    }

    public function saveSlide(): void
    {
        $this->validate([
            'slideForm.image_id' => 'required|integer|exists:media,id',
            'slideForm.title.*' => 'nullable|string|max:180',
            'slideForm.text.*' => 'nullable|string|max:500',
            'slideForm.link_url' => 'nullable|string|max:500',
            'slideForm.align' => 'required|in:left,center,right',
        ], ['slideForm.image_id.required' => 'Slayt için bir görsel seçin.']);

        $attributes = [
            'image_id' => (int) $this->slideForm['image_id'],
            'eyebrow' => $this->slideForm['eyebrow'],
            'title' => $this->slideForm['title'],
            'text' => $this->slideForm['text'],
            'button_label' => $this->slideForm['button_label'],
            'link' => [
                'type' => 'url',
                'url' => $this->slideForm['link_url'],
                'target' => $this->slideForm['new_tab'] ? '_blank' : '_self',
            ],
            'align' => $this->slideForm['align'],
            'is_active' => (bool) $this->slideForm['is_active'],
        ];

        if ($this->editingSlideId) {
            $slide = Slide::where('slider_id', $this->sliderId)->findOrFail($this->editingSlideId);
            $slide->update($attributes);
            ActivityLogger::updated($slide, 'Slider slaytı güncellendi.');
        } else {
            $slide = Slide::create($attributes + [
                'slider_id' => $this->sliderId,
                'sort' => (Slide::where('slider_id', $this->sliderId)->max('sort') ?? -1) + 1,
            ]);
            ActivityLogger::created($slide, 'Slider slaytı oluşturuldu.');
        }

        $this->reset('editingSlide', 'editingSlideId');
        $this->resetSlideForm();
        $this->dispatch('notify', message: 'Slayt kaydedildi.');
    }

    public function deleteSlide(int $id): void
    {
        $slide = Slide::where('slider_id', $this->sliderId)->findOrFail($id);
        ActivityLogger::deleted($slide, 'Slider slaytı silindi.');
        $slide->delete();
        $this->dispatch('notify', message: 'Slayt silindi.');
    }

    public function reorderSlides(array $ids): void
    {
        $valid = Slide::where('slider_id', $this->sliderId)->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->all();
        $received = collect($ids)->map(fn ($id) => (int) $id)->sort()->values()->all();

        if ($valid !== $received) {
            return;
        }

        DB::transaction(function () use ($ids): void {
            foreach ($ids as $sort => $id) {
                Slide::where('slider_id', $this->sliderId)->whereKey($id)->update(['sort' => $sort]);
            }
        });

        $this->dispatch('notify', message: 'Slayt sırası kaydedildi.');
    }

    public function deleteSlider(): void
    {
        $slider = Slider::findOrFail($this->sliderId);
        ActivityLogger::deleted($slider, "Slider silindi: {$slider->name}");
        $slider->delete();
        $this->sliderId = Slider::orderBy('id')->value('id');
        $this->dispatch('notify', message: 'Slider silindi.');
    }

    public function render()
    {
        $slider = $this->sliderId ? Slider::with('slides.image')->find($this->sliderId) : null;

        return view('livewire.admin.design.slider-manager', [
            'sliders' => Slider::orderBy('name')->get(),
            'slider' => $slider,
            'locales' => Locales::all(),
        ])->title('Sliderlar');
    }
}
