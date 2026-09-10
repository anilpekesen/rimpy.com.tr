<form wire:submit="save" class="grid max-w-5xl gap-5 lg:grid-cols-[1fr_20rem]">

    <div class="min-w-0 space-y-4">
        <section class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-ink-100 px-4 py-2.5">
                <h2 class="text-sm font-semibold text-ink-900">Form alanları</h2>
                <div x-data="{ open: false }" class="relative">
                    <button type="button" @click="open = !open" class="btn-secondary btn-sm">
                        <x-icon name="plus" class="size-3.5"/> Alan ekle
                    </button>
                    <div x-show="open" x-cloak @click.outside="open = false"
                         class="absolute right-0 z-20 mt-1 w-44 overflow-hidden rounded-lg border border-ink-200 bg-white py-1 shadow-lg">
                        @foreach ($fieldTypes as $key => $label)
                            <button type="button" wire:click="addField('{{ $key }}')" @click="open = false"
                                    class="block w-full px-3 py-1.5 text-left text-sm text-ink-700 hover:bg-ink-50">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            @if (! $fields)
                <p class="px-4 py-12 text-center text-sm text-ink-400">Henüz alan yok. Sağ üstten ekleyin.</p>
            @else
                <ul class="divide-y divide-ink-100">
                    @foreach ($fields as $index => $field)
                        <li class="space-y-3 p-4">
                            <div class="flex items-center gap-2">
                                <span class="badge-gray shrink-0">{{ $fieldTypes[$field['type']] ?? $field['type'] }}</span>
                                <span class="ml-auto flex shrink-0 gap-0.5">
                                    <button type="button" wire:click="moveField({{ $index }}, 'up')" class="btn-ghost btn-sm" @disabled($index === 0)>↑</button>
                                    <button type="button" wire:click="moveField({{ $index }}, 'down')" class="btn-ghost btn-sm" @disabled($index === count($fields) - 1)>↓</button>
                                    <button type="button" wire:click="removeField({{ $index }})" class="btn-ghost btn-sm text-red-600">
                                        <x-icon name="trash" class="size-3.5"/><span class="sr-only">Kaldır</span>
                                    </button>
                                </span>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                @foreach ($locales as $locale)
                                    <div>
                                        <label for="fl-{{ $index }}-{{ $locale->code }}" class="label">
                                            Etiket
                                            @if ($locales->count() > 1) <span class="font-normal text-ink-400">({{ $locale->native_name }})</span> @endif
                                        </label>
                                        <input id="fl-{{ $index }}-{{ $locale->code }}" type="text"
                                               wire:model="fields.{{ $index }}.label.{{ $locale->code }}"
                                               class="field @error('fields.'.$index.'.label.'.$locale->code) field-error @enderror">
                                        @error('fields.'.$index.'.label.'.$locale->code) <p class="error">{{ $message }}</p> @enderror
                                    </div>
                                @endforeach

                                <div>
                                    <label for="fk-{{ $index }}" class="label">Anahtar</label>
                                    <input id="fk-{{ $index }}" type="text" wire:model="fields.{{ $index }}.key"
                                           class="field font-mono text-xs @error('fields.'.$index.'.key') field-error @enderror">
                                    @error('fields.'.$index.'.key')
                                        <p class="error">{{ $message }}</p>
                                    @else
                                        <p class="hint">Gelen mesajlarda bu adla görünür.</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="fw-{{ $index }}" class="label">Genişlik</label>
                                    <select id="fw-{{ $index }}" wire:model="fields.{{ $index }}.width" class="field">
                                        <option value="50">Yarım satır</option>
                                        <option value="100">Tam satır</option>
                                    </select>
                                </div>
                            </div>

                            <label class="flex items-center gap-2 text-sm text-ink-700">
                                <input type="checkbox" wire:model="fields.{{ $index }}.required"
                                       class="size-4 rounded border-ink-300 text-brand-600">
                                Doldurulması zorunlu
                            </label>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

    <div class="space-y-4">
        <section class="card-pad space-y-4">
            <h2 class="text-sm font-semibold text-ink-900">Form ayarları</h2>

            <div>
                <label for="fs-name" class="label">Form adı</label>
                <input id="fs-name" type="text" wire:model="settings.name"
                       class="field @error('settings.name') field-error @enderror">
                @error('settings.name') <p class="error">{{ $message }}</p> @enderror
            </div>

            @foreach ($locales as $locale)
                <div>
                    <label for="fs-btn-{{ $locale->code }}" class="label">
                        Buton yazısı
                        @if ($locales->count() > 1) <span class="font-normal text-ink-400">({{ $locale->code }})</span> @endif
                    </label>
                    <input id="fs-btn-{{ $locale->code }}" type="text" wire:model="settings.submit_label.{{ $locale->code }}" class="field">
                </div>
            @endforeach

            @foreach ($locales as $locale)
                <div>
                    <label for="fs-ok-{{ $locale->code }}" class="label">
                        Gönderim sonrası mesaj
                        @if ($locales->count() > 1) <span class="font-normal text-ink-400">({{ $locale->code }})</span> @endif
                    </label>
                    <textarea id="fs-ok-{{ $locale->code }}" rows="2" wire:model="settings.success_message.{{ $locale->code }}" class="field"></textarea>
                </div>
            @endforeach
        </section>

        <section class="card-pad space-y-4">
            <h2 class="text-sm font-semibold text-ink-900">Bildirim</h2>

            <div>
                <label for="fs-emails" class="label">Bildirim e-postaları</label>
                <input id="fs-emails" type="text" wire:model="settings.notify_emails"
                       class="field @error('settings.notify_emails') field-error @enderror"
                       placeholder="info@site.com, satis@site.com">
                @error('settings.notify_emails')
                    <p class="error">{{ $message }}</p>
                @else
                    <p class="hint">Birden fazla adresi virgülle ayırın. Boş bırakırsanız e-posta gönderilmez; mesajlar yine de panelde birikir.</p>
                @enderror
            </div>

            <label class="flex items-start gap-2.5">
                <input type="checkbox" wire:model="settings.store_submissions" class="mt-0.5 size-4 rounded border-ink-300 text-brand-600">
                <span class="text-sm text-ink-700">
                    Mesajları panelde sakla
                    <span class="block text-xs text-ink-500">Kapatırsanız yalnızca e-posta gönderilir, kayıt tutulmaz.</span>
                </span>
            </label>

            <label class="flex items-start gap-2.5">
                <input type="checkbox" wire:model="settings.spam_protection" class="mt-0.5 size-4 rounded border-ink-300 text-brand-600">
                <span class="text-sm text-ink-700">
                    İstenmeyen gönderim koruması
                    <span class="block text-xs text-ink-500">Ziyaretçiye görünmeyen bir tuzak alanla botları eler. Açık kalmalı.</span>
                </span>
            </label>

            <div>
                <label for="fs-redirect" class="label">Gönderim sonrası yönlendirme</label>
                <input id="fs-redirect" type="text" wire:model="settings.redirect_url" class="field" placeholder="/tesekkurler">
                <p class="hint">Boş bırakırsanız aynı sayfada teşekkür mesajı gösterilir.</p>
            </div>
        </section>

        <div class="flex gap-2">
            <a href="{{ route('admin.forms.index') }}" wire:navigate class="btn-ghost flex-1">Geri</a>
            <button type="submit" class="btn-primary flex-1">Kaydet</button>
        </div>
    </div>
</form>
