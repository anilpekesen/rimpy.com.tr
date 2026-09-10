<form wire:submit="save" class="grid gap-5 lg:grid-cols-[1fr_20rem]">

    <div class="min-w-0 space-y-5">
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
            <div @class(['space-y-5', 'hidden' => $activeLocale !== $locale->code])>
                <section class="card-pad space-y-4">
                    <div>
                        <label for="i-title-{{ $locale->code }}" class="label">Başlık</label>
                        <input id="i-title-{{ $locale->code }}" type="text" wire:model="translations.{{ $locale->code }}.title"
                               class="field @error('translations.'.$locale->code.'.title') field-error @enderror">
                        @error('translations.'.$locale->code.'.title') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="i-slug-{{ $locale->code }}" class="label">Adres</label>
                        <div class="flex items-center gap-1">
                            <span class="shrink-0 font-mono text-xs text-ink-400">
                                {{ $locales->count() > 1 ? '/'.$locale->code : '' }}/{{ $type->prefix($locale->code) }}/
                            </span>
                            <input id="i-slug-{{ $locale->code }}" type="text" wire:model="translations.{{ $locale->code }}.slug"
                                   class="field font-mono text-xs" placeholder="otomatik">
                        </div>
                    </div>

                    @if ($type->supports('excerpt'))
                        <div>
                            <label for="i-exc-{{ $locale->code }}" class="label">Kısa açıklama</label>
                            <textarea id="i-exc-{{ $locale->code }}" rows="2" wire:model="translations.{{ $locale->code }}.excerpt" class="field"></textarea>
                            <p class="hint">Listelerde başlığın altında görünen özet.</p>
                        </div>
                    @endif

                    @if ($type->supports('body'))
                        <div wire:ignore>
                            <span class="label">İçerik</span>
                            <div class="overflow-hidden rounded-lg border border-ink-300"
                                 x-data="{ cmd(c, v = null) { document.execCommand(c, false, v); $refs.ed.focus(); } }">
                                <div class="flex flex-wrap gap-0.5 border-b border-ink-200 bg-ink-50 p-1">
                                    <button type="button" @click="cmd('bold')" class="btn-ghost btn-sm font-bold">B</button>
                                    <button type="button" @click="cmd('italic')" class="btn-ghost btn-sm italic">I</button>
                                    <button type="button" @click="cmd('formatBlock','<h2>')" class="btn-ghost btn-sm">H2</button>
                                    <button type="button" @click="cmd('formatBlock','<h3>')" class="btn-ghost btn-sm">H3</button>
                                    <button type="button" @click="cmd('formatBlock','<p>')" class="btn-ghost btn-sm">P</button>
                                    <button type="button" @click="cmd('insertUnorderedList')" class="btn-ghost btn-sm">•</button>
                                    <button type="button" @click="cmd('insertOrderedList')" class="btn-ghost btn-sm">1.</button>
                                    <button type="button" @click="const u = prompt('Bağlantı adresi:'); if (u) cmd('createLink', u)" class="btn-ghost btn-sm">
                                        <x-icon name="link" class="size-3.5"/>
                                    </button>
                                    <button type="button" @click="cmd('removeFormat')" class="btn-ghost btn-sm ml-auto">⌫</button>
                                </div>
                                <div x-ref="ed" contenteditable="true"
                                     class="max-w-none px-4 py-3 text-sm focus:outline-none [&_h2]:mt-4 [&_h2]:mb-2 [&_h2]:text-lg [&_h2]:font-semibold [&_h3]:mt-3 [&_h3]:mb-1.5 [&_h3]:font-semibold [&_p]:mb-3 [&_ul]:mb-3 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:mb-3 [&_ol]:list-decimal [&_ol]:pl-5 [&_a]:text-brand-600 [&_a]:underline"
                                     style="min-height: 280px"
                                     x-on:input.debounce.600ms="$wire.set('translations.{{ $locale->code }}.body', $refs.ed.innerHTML, false)"
                                >{!! $translations[$locale->code]['body'] ?? '' !!}</div>
                            </div>
                        </div>
                    @endif
                </section>

                @if ($type->fields)
                    <section class="card-pad space-y-4">
                        <h2 class="text-sm font-semibold text-ink-900">{{ $type->label('tr') }} bilgileri</h2>

                        @foreach ($type->fields as $field)
                            @php $fieldLabel = $field['label'][$locale->code] ?? $field['label'][$locales->first()->code] ?? $field['key']; @endphp
                            <div>
                                <label for="cf-{{ $locale->code }}-{{ $field['key'] }}" class="label">
                                    {{ $fieldLabel }}
                                    @if (! empty($field['required'])) <span class="text-red-600" aria-hidden="true">*</span> @endif
                                </label>
                                @if ($field['type'] === 'textarea')
                                    <textarea id="cf-{{ $locale->code }}-{{ $field['key'] }}" rows="3"
                                              wire:model="translations.{{ $locale->code }}.custom.{{ $field['key'] }}"
                                              class="field @error('translations.'.$locale->code.'.custom.'.$field['key']) field-error @enderror"></textarea>
                                @else
                                    <input id="cf-{{ $locale->code }}-{{ $field['key'] }}" type="{{ $field['type'] }}"
                                           wire:model="translations.{{ $locale->code }}.custom.{{ $field['key'] }}"
                                           class="field @error('translations.'.$locale->code.'.custom.'.$field['key']) field-error @enderror">
                                @endif
                                @error('translations.'.$locale->code.'.custom.'.$field['key'])
                                    <p class="error">{{ $fieldLabel }} zorunludur.</p>
                                @enderror
                            </div>
                        @endforeach
                    </section>
                @endif

                @if ($type->supports('seo'))
                    <section class="card-pad space-y-4">
                        <h2 class="text-sm font-semibold text-ink-900">Arama motoru</h2>
                        <div>
                            <label for="i-st-{{ $locale->code }}" class="label">SEO başlığı</label>
                            <input id="i-st-{{ $locale->code }}" type="text" wire:model="translations.{{ $locale->code }}.seo_title"
                                   class="field" maxlength="70" placeholder="Boşsa başlık kullanılır">
                        </div>
                        <div>
                            <label for="i-sd-{{ $locale->code }}" class="label">SEO açıklaması</label>
                            <textarea id="i-sd-{{ $locale->code }}" rows="2" wire:model.live="translations.{{ $locale->code }}.seo_description"
                                      class="field" maxlength="320"></textarea>
                            <p class="hint">{{ mb_strlen($translations[$locale->code]['seo_description'] ?? '') }} / 160 karakter</p>
                        </div>
                    </section>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Yan sütun --}}
    <div class="space-y-5">
        <section class="card-pad space-y-4">
            <h2 class="text-sm font-semibold text-ink-900">Yayın</h2>

            <div>
                <label for="i-status" class="label">Durum</label>
                <select id="i-status" wire:model="form.status" class="field">
                    <option value="draft">Taslak</option>
                    <option value="published">Yayında</option>
                    <option value="scheduled">Zamanlanmış</option>
                </select>
            </div>

            <div>
                <label for="i-date" class="label">Yayın tarihi</label>
                <input id="i-date" type="datetime-local" wire:model="form.published_at" class="field">
            </div>

            <label class="flex items-center gap-2 text-sm text-ink-700">
                <input type="checkbox" wire:model="form.is_featured" class="size-4 rounded border-ink-300 text-brand-600">
                Öne çıkar
            </label>

            <div class="flex gap-2 border-t border-ink-100 pt-4">
                <button type="submit" class="btn-primary flex-1">Kaydet</button>
                @if ($type->supports('builder'))
                    <a href="{{ route('admin.builder.content', $item->id) }}" class="btn-secondary" title="Serbest tasarım">
                        <x-icon name="blocks" class="size-4"/>
                    </a>
                @endif
            </div>
        </section>

        @if ($type->supports('image'))
            <section class="card-pad">
                <h2 class="mb-3 text-sm font-semibold text-ink-900">Kapak görseli</h2>
                @php $cover = \App\Models\Media::find($form['featured_image_id'] ?? null); @endphp
                @if ($cover)
                    <img src="{{ $cover->url('small') }}" alt="" class="mb-2 w-full rounded-lg object-cover">
                    <button type="button" wire:click="$set('form.featured_image_id', null)"
                            class="btn-ghost btn-sm w-full text-red-600">Kaldır</button>
                @else
                    <button type="button" class="btn-secondary btn-sm w-full"
                            @click="$dispatch('open-media-picker', { target: 'form.featured_image_id' })">
                        <x-icon name="image" class="size-3.5"/> Görsel seç
                    </button>
                @endif
            </section>
        @endif

        @if ($type->supports('taxonomies') && $type->taxonomies->isNotEmpty())
            @foreach ($type->taxonomies as $taxonomy)
                <section class="card-pad">
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-ink-900">{{ $taxonomy->pluralLabel('tr') }}</h2>
                        <a href="{{ route('admin.terms.index', [$type->key, $taxonomy->key]) }}" wire:navigate
                           class="text-xs font-medium text-brand-600 hover:underline">Yönet</a>
                    </div>

                    @if ($taxonomy->terms->isEmpty())
                        <p class="text-xs text-ink-400">Henüz tanımlı değil.</p>
                    @else
                        <div class="max-h-52 space-y-1.5 overflow-y-auto">
                            @foreach ($taxonomy->terms as $term)
                                <label class="flex items-center gap-2 text-sm text-ink-700">
                                    <input type="checkbox" wire:model="selectedTerms" value="{{ $term->id }}"
                                           class="size-4 rounded border-ink-300 text-brand-600 focus:ring-brand-500">
                                    {{ $term->t('name') }}
                                </label>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endforeach
        @endif
    </div>
</form>
