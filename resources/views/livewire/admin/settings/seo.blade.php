<form wire:submit="save" class="max-w-3xl space-y-5">

    <section class="card-pad space-y-4">
        <div>
            <h2 class="text-sm font-semibold text-ink-900">Varsayılan arama motoru bilgileri</h2>
            <p class="mt-0.5 text-xs text-ink-500">
                Kendi başlığı girilmemiş sayfalarda bu değerler kullanılır.
            </p>
        </div>

        @foreach ($locales as $locale)
            <div class="space-y-3 rounded-lg border border-ink-200 p-3">
                @if ($locales->count() > 1)
                    <p class="text-xs font-semibold tracking-wide text-ink-500 uppercase">{{ $locale->native_name }}</p>
                @endif

                <div>
                    <label for="mt-{{ $locale->code }}" class="label">Başlık</label>
                    <input id="mt-{{ $locale->code }}" type="text" wire:model="seo.meta_title.{{ $locale->code }}"
                           class="field" maxlength="70">
                    <p class="hint">Google genellikle ilk 60 karakteri gösterir.</p>
                </div>

                <div>
                    <label for="md-{{ $locale->code }}" class="label">Açıklama</label>
                    <textarea id="md-{{ $locale->code }}" rows="2" wire:model.live="seo.meta_description.{{ $locale->code }}"
                              class="field @error('seo.meta_description.'.$locale->code) field-error @enderror" maxlength="320"></textarea>
                    <p class="hint">
                        {{ mb_strlen($seo['meta_description'][$locale->code] ?? '') }} / 160 karakter —
                        arama sonucunda başlığın altında görünen metin.
                    </p>
                    @error('seo.meta_description.'.$locale->code) <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>
        @endforeach

        @php $og = \App\Models\Media::find($seo['og_image_id'] ?? null); @endphp
        <div>
            <span class="label">Paylaşım görseli</span>
            <div class="flex items-center gap-3 rounded-lg border border-ink-200 p-3">
                @if ($og)
                    <img src="{{ $og->url('small') }}" alt="" class="h-16 w-28 rounded object-cover">
                    <button type="button" wire:click="$set('seo.og_image_id', null)" class="btn-ghost btn-sm text-red-600">Kaldır</button>
                @else
                    <button type="button" class="btn-secondary btn-sm"
                            @click="$dispatch('open-media-picker', { target: 'seo.og_image_id' })"
                            @media-applied.stop="$wire.set('seo.og_image_id', $event.detail.mediaId)">Görsel seç</button>
                @endif
            </div>
            <p class="hint">Site bağlantısı WhatsApp ya da LinkedIn'de paylaşıldığında görünen görsel. 1200×630 piksel önerilir.</p>
        </div>
    </section>

    <section class="card-pad space-y-4">
        <h2 class="text-sm font-semibold text-ink-900">Araç bağlantıları</h2>

        <div>
            <label for="ga" class="label">Google Analytics ölçüm kimliği</label>
            <input id="ga" type="text" wire:model="seo.google_analytics" class="field font-mono text-xs" placeholder="G-XXXXXXXXXX">
            <p class="hint">Google Analytics hesabınızdaki “Ölçüm Kimliği”. Boş bırakırsanız hiçbir izleme kodu yüklenmez.</p>
        </div>

        <div>
            <label for="gv" class="label">Google Search Console doğrulama kodu</label>
            <input id="gv" type="text" wire:model="seo.google_verification" class="field font-mono text-xs">
            <p class="hint">Search Console'un istediği “content” değerini yapıştırın.</p>
        </div>
    </section>

    <section class="card-pad">
        <label class="flex items-start gap-3">
            <input type="checkbox" wire:model="seo.noindex_site"
                   class="mt-0.5 size-4 rounded border-ink-300 text-red-600 focus:ring-red-500">
            <span>
                <span class="block text-sm font-medium text-ink-900">Siteyi arama motorlarından gizle</span>
                <span class="block text-xs text-ink-500">
                    Site henüz hazır değilken açın. <strong class="text-red-700">Yayına aldıktan sonra kapatmayı unutmayın</strong> —
                    açık kaldığı sürece siteniz Google'da hiç görünmez.
                </span>
            </span>
        </label>
    </section>

    <div class="flex justify-end">
        <button type="submit" class="btn-primary">Kaydet</button>
    </div>
</form>
