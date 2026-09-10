<div class="max-w-4xl space-y-4">

    <div class="card-pad">
        <h2 class="text-sm font-semibold text-ink-900">İçerik türü nedir?</h2>
        <p class="mt-1 text-sm text-ink-600">
            Sitenizde tekrar eden her içerik grubu bir türdür: Ürünler, Haberler, Referanslar, Projeler, Ekip…
            Burada bir tür tanımladığınızda sol menüde kendi bölümü açılır, ön yüzde kendi adresleri oluşur
            ve sayfa kurucusundaki “İçerik listesi” öğesiyle istediğiniz yerde listeleyebilirsiniz.
        </p>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <caption class="sr-only">Tanımlı içerik türleri</caption>
            <thead>
                <tr class="border-b border-ink-100 bg-ink-50/60 text-left text-xs font-medium text-ink-500">
                    <th scope="col" class="px-4 py-2.5">Tür</th>
                    <th scope="col" class="hidden px-4 py-2.5 sm:table-cell">Adres öneki</th>
                    <th scope="col" class="px-4 py-2.5">Kayıt</th>
                    <th scope="col" class="px-4 py-2.5">Durum</th>
                    <th scope="col" class="px-4 py-2.5"><span class="sr-only">İşlemler</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @foreach ($types as $type)
                    <tr class="group">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <x-icon name="{{ $type->icon }}" class="size-4 shrink-0 text-ink-400"/>
                                <span class="font-medium text-ink-900">{{ $type->pluralLabel('tr') }}</span>
                            </div>
                        </td>
                        <td class="hidden px-4 py-3 font-mono text-xs text-ink-400 sm:table-cell">/{{ $type->prefix('tr') }}</td>
                        <td class="px-4 py-3 text-xs text-ink-500">{{ $type->items_count }}</td>
                        <td class="px-4 py-3">
                            @if ($type->is_active)
                                <span class="badge-green">Etkin</span>
                            @else
                                <span class="badge-gray">Gizli</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1 opacity-0 transition group-hover:opacity-100 focus-within:opacity-100">
                                @foreach ($type->taxonomies as $taxonomy)
                                    <a href="{{ route('admin.terms.index', [$type->key, $taxonomy->key]) }}" wire:navigate
                                       class="btn-ghost btn-sm">{{ $taxonomy->pluralLabel('tr') }}</a>
                                @endforeach
                                <button wire:click="edit({{ $type->id }})" class="btn-ghost btn-sm">
                                    <x-icon name="edit" class="size-4"/><span class="sr-only">Düzenle</span>
                                </button>
                                <button wire:click="toggleActive({{ $type->id }})" class="btn-ghost btn-sm">
                                    {{ $type->is_active ? 'Gizle' : 'Göster' }}
                                </button>
                                <button wire:click="delete({{ $type->id }})"
                                        wire:confirm="“{{ $type->pluralLabel('tr') }}” türü silinecek. Devam edilsin mi?"
                                        class="btn-ghost btn-sm text-red-600 hover:bg-red-50">
                                    <x-icon name="trash" class="size-4"/><span class="sr-only">Sil</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <form wire:submit="save" class="card-pad space-y-4">
        <h2 class="text-sm font-semibold text-ink-900">
            {{ $editingId ? 'Türü düzenle' : 'Yeni içerik türü' }}
        </h2>

        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($locales as $locale)
                <div>
                    <label for="ct-name-{{ $locale->code }}" class="label">
                        Tekil ad
                        @if ($locales->count() > 1) <span class="font-normal text-ink-400">({{ $locale->native_name }})</span> @endif
                    </label>
                    <input id="ct-name-{{ $locale->code }}" type="text" wire:model="form.name.{{ $locale->code }}"
                           class="field @error('form.name.'.$locale->code) field-error @enderror" placeholder="Ürün">
                    @error('form.name.'.$locale->code) <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="ct-plural-{{ $locale->code }}" class="label">
                        Çoğul ad
                        @if ($locales->count() > 1) <span class="font-normal text-ink-400">({{ $locale->native_name }})</span> @endif
                    </label>
                    <input id="ct-plural-{{ $locale->code }}" type="text" wire:model="form.plural.{{ $locale->code }}"
                           class="field @error('form.plural.'.$locale->code) field-error @enderror" placeholder="Ürünler">
                    @error('form.plural.'.$locale->code) <p class="error">{{ $message }}</p> @enderror
                </div>
            @endforeach
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="ct-key" class="label">Anahtar</label>
                <input id="ct-key" type="text" wire:model="form.key"
                       class="field font-mono text-xs @error('form.key') field-error @enderror" placeholder="urunler">
                @error('form.key')
                    <p class="error">{{ $message }}</p>
                @else
                    <p class="hint">Sistemin içinde kullanılır. Sonradan değiştirmek adresleri bozabilir.</p>
                @enderror
            </div>
            <div>
                <label for="ct-icon" class="label">Menü simgesi</label>
                <select id="ct-icon" wire:model="form.icon" class="field">
                    @foreach ($icons as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <fieldset>
            <legend class="label">Bu tür neleri kullansın?</legend>
            <div class="grid gap-1.5 sm:grid-cols-2">
                @foreach ($supportOptions as $key => $label)
                    <label class="flex items-center gap-2 text-sm text-ink-700">
                        <input type="checkbox" wire:model="form.supports" value="{{ $key }}"
                               class="size-4 rounded border-ink-300 text-brand-600 focus:ring-brand-500">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </fieldset>

        <label class="flex items-start gap-2.5">
            <input type="checkbox" wire:model="form.has_archive"
                   class="mt-0.5 size-4 rounded border-ink-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm text-ink-700">
                Liste sayfası olsun
                <span class="block text-xs text-ink-500">
                    Açıkken /{{ $form['key'] ?: 'anahtar' }} adresinde tüm kayıtların listelendiği bir sayfa oluşur.
                </span>
            </span>
        </label>

        {{-- Kullanıcı tanımlı ek alanlar --}}
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="label mb-0">Ek alanlar</span>
                <button type="button" wire:click="addField" class="btn-secondary btn-sm">
                    <x-icon name="plus" class="size-3.5"/> Alan ekle
                </button>
            </div>
            <p class="hint">
                Bu türe özel bilgiler: fiyat, kapasite, model kodu, teslim süresi… Kayıt düzenleme ekranında görünür.
            </p>

            @foreach ($form['fields'] as $index => $field)
                <div class="flex flex-wrap items-start gap-2 rounded-lg border border-ink-200 p-2.5">
                    <div class="min-w-32 flex-1">
                        <input type="text" wire:model="form.fields.{{ $index }}.label.{{ $locales->first()->code }}"
                               class="field px-2 py-1 text-xs" placeholder="Alan adı" aria-label="Alan adı">
                    </div>
                    <div class="w-28">
                        <input type="text" wire:model="form.fields.{{ $index }}.key"
                               class="field px-2 py-1 font-mono text-xs @error('form.fields.'.$index.'.key') field-error @enderror"
                               placeholder="anahtar" aria-label="Alan anahtarı">
                    </div>
                    <div class="w-32">
                        <select wire:model="form.fields.{{ $index }}.type" class="field px-2 py-1 text-xs" aria-label="Alan türü">
                            <option value="text">Kısa metin</option>
                            <option value="textarea">Uzun metin</option>
                            <option value="number">Sayı</option>
                            <option value="date">Tarih</option>
                            <option value="url">Bağlantı</option>
                            <option value="file">Dosya</option>
                        </select>
                    </div>
                    <label class="flex items-center gap-1.5 py-1 text-xs text-ink-600">
                        <input type="checkbox" wire:model="form.fields.{{ $index }}.required"
                               class="size-3.5 rounded border-ink-300 text-brand-600">
                        Zorunlu
                    </label>
                    <button type="button" wire:click="removeField({{ $index }})" class="btn-ghost btn-sm text-red-600">
                        <x-icon name="trash" class="size-3.5"/><span class="sr-only">Kaldır</span>
                    </button>
                </div>
            @endforeach
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn-primary">{{ $editingId ? 'Güncelle' : 'Türü oluştur' }}</button>
            @if ($editingId)
                <button type="button" wire:click="$set('editingId', null)" class="btn-ghost">Vazgeç</button>
            @endif
        </div>
    </form>
</div>
