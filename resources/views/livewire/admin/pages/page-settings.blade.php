<form wire:submit="save" class="max-w-3xl space-y-5">

    @if ($locales->count() > 1)
        <div class="flex rounded-lg border border-ink-200 bg-white p-0.5 text-sm">
            @foreach ($locales as $locale)
                <button type="button" wire:click="$set('activeLocale', '{{ $locale->code }}')"
                        class="rounded-md px-3 py-1.5 font-medium transition
                               {{ $activeLocale === $locale->code ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-ink-100' }}">
                    {{ $locale->native_name }}
                </button>
            @endforeach
        </div>
    @endif

    @foreach ($locales as $locale)
        <div @class(['space-y-4', 'hidden' => $activeLocale !== $locale->code])>
            <section class="card-pad space-y-4">
                <h2 class="text-sm font-semibold text-ink-900">Sayfa bilgileri</h2>

                <div>
                    <label for="t-{{ $locale->code }}" class="label">Başlık</label>
                    <input id="t-{{ $locale->code }}" type="text" wire:model="translations.{{ $locale->code }}.title"
                           class="field @error('translations.'.$locale->code.'.title') field-error @enderror">
                    @error('translations.'.$locale->code.'.title') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="s-{{ $locale->code }}" class="label">Adres</label>
                    <div class="flex items-center gap-1">
                        <span class="shrink-0 font-mono text-xs text-ink-400">
                            {{ $locales->count() > 1 ? '/'.$locale->code : '' }}/{{ $page->parent?->t('slug', $locale->code) ? $page->parent->t('slug', $locale->code).'/' : '' }}
                        </span>
                        <input id="s-{{ $locale->code }}" type="text" wire:model="translations.{{ $locale->code }}.slug"
                               class="field font-mono text-xs" placeholder="otomatik">
                    </div>
                    <p class="hint">Boş bırakırsanız başlıktan otomatik üretilir. Yayındaki bir sayfanın adresini
                        değiştirirseniz eski adres için bir yönlendirme eklemeyi unutmayın.</p>
                </div>

                <div>
                    <label for="e-{{ $locale->code }}" class="label">Kısa açıklama</label>
                    <textarea id="e-{{ $locale->code }}" rows="2" wire:model="translations.{{ $locale->code }}.excerpt" class="field"></textarea>
                    <p class="hint">Sayfa listelerinde ve paylaşımlarda kullanılır.</p>
                </div>
            </section>

            <section class="card-pad space-y-4">
                <h2 class="text-sm font-semibold text-ink-900">Arama motoru</h2>

                <div>
                    <label for="st-{{ $locale->code }}" class="label">SEO başlığı</label>
                    <input id="st-{{ $locale->code }}" type="text" wire:model="translations.{{ $locale->code }}.seo_title"
                           class="field" maxlength="70" placeholder="Boşsa sayfa başlığı kullanılır">
                </div>

                <div>
                    <label for="sd-{{ $locale->code }}" class="label">SEO açıklaması</label>
                    <textarea id="sd-{{ $locale->code }}" rows="2" wire:model.live="translations.{{ $locale->code }}.seo_description"
                              class="field @error('translations.'.$locale->code.'.seo_description') field-error @enderror" maxlength="320"></textarea>
                    <p class="hint">{{ mb_strlen($translations[$locale->code]['seo_description'] ?? '') }} / 160 karakter</p>
                    @error('translations.'.$locale->code.'.seo_description') <p class="error">{{ $message }}</p> @enderror
                </div>

                <label class="flex items-start gap-2.5">
                    <input type="checkbox" wire:model="translations.{{ $locale->code }}.noindex"
                           class="mt-0.5 size-4 rounded border-ink-300 text-brand-600">
                    <span class="text-sm text-ink-700">
                        Bu sayfayı arama motorlarına gösterme
                        <span class="block text-xs text-ink-500">Teşekkür sayfası, iç duyuru gibi sayfalar için.</span>
                    </span>
                </label>
            </section>
        </div>
    @endforeach

    <section class="card-pad space-y-4">
        <h2 class="text-sm font-semibold text-ink-900">Yayın</h2>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="p-status" class="label">Durum</label>
                <select id="p-status" wire:model.live="form.status" class="field">
                    <option value="draft">Taslak — yalnızca siz görürsünüz</option>
                    <option value="published">Yayında — herkes görür</option>
                    <option value="scheduled">Zamanlanmış</option>
                </select>
            </div>

            <div>
                <label for="p-date" class="label">Yayın tarihi</label>
                <input id="p-date" type="datetime-local" wire:model="form.published_at" class="field">
                <p class="hint">İleri bir tarih girerseniz sayfa o zamana kadar görünmez.</p>
            </div>

            <div>
                <label for="p-parent" class="label">Üst sayfa</label>
                <select id="p-parent" wire:model="form.parent_id"
                        class="field @error('form.parent_id') field-error @enderror">
                    <option value="">— Yok (en üst seviye) —</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->t('title') }}</option>
                    @endforeach
                </select>
                @error('form.parent_id')
                    <p class="error">{{ $message }}</p>
                @else
                    <p class="hint">Seçerseniz adres üst sayfanın altına iner: /kurumsal/tarihce</p>
                @enderror
            </div>

            <div>
                <label for="p-sort" class="label">Sıra</label>
                <input id="p-sort" type="number" min="0" wire:model="form.sort" class="field">
            </div>
        </div>

        <label class="flex items-start gap-2.5">
            <input type="checkbox" wire:model="form.show_in_search"
                   class="mt-0.5 size-4 rounded border-ink-300 text-brand-600">
            <span class="text-sm text-ink-700">Site haritasına dahil et</span>
        </label>
    </section>

    <div class="flex justify-between">
        <a href="{{ route('admin.builder.page', $page) }}" class="btn-secondary">
            <x-icon name="edit" class="size-4"/> Tasarımı düzenle
        </a>
        <div class="flex gap-2">
            <a href="{{ route('admin.pages.index') }}" wire:navigate class="btn-ghost">Vazgeç</a>
            <button type="submit" class="btn-primary">Kaydet</button>
        </div>
    </div>
</form>
