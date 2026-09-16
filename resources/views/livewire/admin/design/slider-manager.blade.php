<div class="space-y-4" data-slider-manager x-data x-init="$nextTick(() => window.initSlideSortables($el))">
    <div class="flex flex-wrap items-center gap-2">
        @if ($sliders->isNotEmpty())
            <select wire:model.live="sliderId" class="field w-auto min-w-52" aria-label="Slider seçin">
                @foreach ($sliders as $option)
                    <option value="{{ $option->id }}">{{ $option->name }}</option>
                @endforeach
            </select>
        @endif
        <button wire:click="$set('creatingSlider', true)" class="btn-primary">
            <x-icon name="plus" class="size-4"/> Yeni slider
        </button>
        @if ($slider)
            <button wire:click="deleteSlider" wire:confirm="“{{ $slider->name }}” ve içindeki bütün slaytlar silinecek. Devam edilsin mi?"
                    class="btn-ghost ml-auto text-red-600">Sliderı sil</button>
        @endif
    </div>

    @if ($creatingSlider)
        <form wire:submit="createSlider" class="card flex flex-col gap-3 p-4 sm:flex-row sm:items-start">
            <div class="flex-1">
                <label for="slider-name" class="label">Slider adı</label>
                <input id="slider-name" wire:model="newSliderName" class="field @error('newSliderName') field-error @enderror" placeholder="Ana sayfa sliderı">
                @error('newSliderName') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="flex-1">
                <label for="slider-key" class="label">Anahtar</label>
                <input id="slider-key" wire:model="newSliderKey" class="field font-mono @error('newSliderKey') field-error @enderror" placeholder="ana-sayfa">
                @error('newSliderKey') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-2 sm:pt-6.5">
                <button class="btn-primary">Oluştur</button>
                <button type="button" wire:click="$set('creatingSlider', false)" class="btn-ghost">Vazgeç</button>
            </div>
        </form>
    @endif

    @if (! $slider)
        <section class="card flex flex-col items-center justify-center py-16 text-center">
            <x-icon name="image" class="size-10 text-ink-200"/>
            <p class="mt-3 text-sm font-medium text-ink-700">Henüz slider yok</p>
            <p class="mt-1 max-w-sm text-sm text-ink-500">Bir slider oluşturun, görselleri ekleyin ve sayfa kurucuda seçin.</p>
        </section>
    @else
        <section class="card overflow-hidden">
            <header class="flex items-center gap-3 border-b border-ink-100 px-4 py-3">
                <div class="min-w-0 flex-1">
                    <h2 class="truncate text-sm font-semibold text-ink-900">{{ $slider->name }}</h2>
                    <p class="mt-0.5 text-xs text-ink-500">Noktalı tutamaçtan sürükleyerek sıralayın. Anahtar: <code>{{ $slider->key }}</code></p>
                </div>
                <button wire:click="addSlide" class="btn-primary btn-sm"><x-icon name="plus" class="size-4"/> Slayt ekle</button>
            </header>

            @if ($slider->slides->isEmpty())
                <div class="px-4 py-14 text-center">
                    <p class="text-sm font-medium text-ink-700">Bu slider henüz boş</p>
                    <button wire:click="addSlide" class="btn-secondary btn-sm mt-3">İlk slaytı ekle</button>
                </div>
            @else
                <ol data-slide-sortable class="divide-y divide-ink-100">
                    @foreach ($slider->slides as $slide)
                        <li data-slide-item="{{ $slide->id }}" class="group flex items-center gap-3 px-4 py-3">
                            <button type="button" data-slide-handle class="cursor-grab touch-none rounded p-1.5 text-ink-400 hover:bg-ink-100" aria-label="Slaytı sürükle">
                                <svg class="size-4" viewBox="0 0 16 16" fill="currentColor"><circle cx="5" cy="3" r="1"/><circle cx="11" cy="3" r="1"/><circle cx="5" cy="8" r="1"/><circle cx="11" cy="8" r="1"/><circle cx="5" cy="13" r="1"/><circle cx="11" cy="13" r="1"/></svg>
                            </button>
                            @if ($slide->image)
                                <img src="{{ $slide->image->url('thumb') }}" alt="" class="h-14 w-24 shrink-0 rounded-md object-cover">
                            @else
                                <span class="flex h-14 w-24 shrink-0 items-center justify-center rounded-md bg-ink-100"><x-icon name="image" class="size-5 text-ink-300"/></span>
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-ink-900 {{ $slide->is_active ? '' : 'line-through opacity-50' }}">{{ $slide->title[app()->getLocale()] ?? $slide->title[\App\Support\Locales::default()] ?? 'Başlıksız slayt' }}</p>
                                <p class="truncate text-xs text-ink-500">{{ $slide->text[app()->getLocale()] ?? $slide->text[\App\Support\Locales::default()] ?? '' }}</p>
                            </div>
                            <button wire:click="editSlide({{ $slide->id }})" class="btn-ghost btn-sm"><x-icon name="edit" class="size-4"/><span class="sr-only">Düzenle</span></button>
                            <button wire:click="deleteSlide({{ $slide->id }})" wire:confirm="Slayt silinecek. Devam edilsin mi?" class="btn-ghost btn-sm text-red-600"><x-icon name="trash" class="size-4"/><span class="sr-only">Sil</span></button>
                        </li>
                    @endforeach
                </ol>
            @endif
        </section>
    @endif

    @if ($editingSlide)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-ink-950/55 p-4" role="dialog" aria-modal="true" aria-labelledby="slide-dialog-title">
            <form wire:submit="saveSlide" class="max-h-full w-full max-w-2xl overflow-y-auto rounded-xl bg-white shadow-xl">
                <header class="border-b border-ink-100 px-5 py-3.5"><h2 id="slide-dialog-title" class="text-sm font-semibold text-ink-900">{{ $editingSlideId ? 'Slaytı düzenle' : 'Yeni slayt' }}</h2></header>
                <div class="space-y-4 p-5">
                    @php $image = \App\Models\Media::find($slideForm['image_id'] ?? null); @endphp
                    <div>
                        <span class="label">Görsel</span>
                        @if ($image)<img src="{{ $image->url('small') }}" alt="" class="mb-2 h-36 w-full rounded-lg object-cover">@endif
                        <button type="button" class="btn-secondary btn-sm w-full"
                                @click="$dispatch('open-media-picker', { target: 'slideForm.image_id' })"
                                @media-applied.stop="$wire.set('slideForm.image_id', $event.detail.mediaId)">
                            <x-icon name="image" class="size-4"/> {{ $image ? 'Görseli değiştir' : 'Görsel seç' }}
                        </button>
                        @error('slideForm.image_id') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    @foreach ($locales as $locale)
                        <fieldset class="space-y-3 rounded-lg border border-ink-200 p-3">
                            <legend class="px-1 text-xs font-semibold text-ink-600">{{ $locale->native_name }}</legend>
                            <div><label class="label">Başlık</label><input wire:model="slideForm.title.{{ $locale->code }}" class="field"></div>
                            <div><label class="label">Üst etiket</label><input wire:model="slideForm.eyebrow.{{ $locale->code }}" class="field"></div>
                            <div><label class="label">Açıklama</label><textarea wire:model="slideForm.text.{{ $locale->code }}" rows="2" class="field"></textarea></div>
                            <div><label class="label">Buton yazısı</label><input wire:model="slideForm.button_label.{{ $locale->code }}" class="field"></div>
                        </fieldset>
                    @endforeach

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div><label class="label">Buton bağlantısı</label><input wire:model="slideForm.link_url" class="field" placeholder="https:// veya /sayfa"></div>
                        <div><label class="label">Metin hizası</label><select wire:model="slideForm.align" class="field"><option value="left">Sol</option><option value="center">Orta</option><option value="right">Sağ</option></select></div>
                    </div>
                    <div class="flex flex-wrap gap-5">
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="slideForm.new_tab" class="size-4 rounded"> Bağlantıyı yeni sekmede aç</label>
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="slideForm.is_active" class="size-4 rounded"> Slayt yayında</label>
                    </div>
                </div>
                <footer class="flex justify-end gap-2 border-t border-ink-100 px-5 py-3">
                    <button type="button" wire:click="$set('editingSlide', false)" class="btn-secondary">Vazgeç</button>
                    <button class="btn-primary">Slaytı kaydet</button>
                </footer>
            </form>
        </div>
    @endif
</div>
