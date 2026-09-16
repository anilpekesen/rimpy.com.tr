<form wire:submit="save" class="max-w-3xl space-y-5">

    <section class="card-pad space-y-4">
        <div>
            <h2 class="text-sm font-semibold text-ink-900">Site kimliği</h2>
            <p class="mt-0.5 text-xs text-ink-500">Tarayıcı sekmesinde, arama sonuçlarında ve panelde görünen bilgiler.</p>
        </div>

        <div>
            <label for="site-name" class="label">Site adı</label>
            <input id="site-name" type="text" wire:model="general.site_name"
                   class="field @error('general.site_name') field-error @enderror">
            @error('general.site_name') <p class="error">{{ $message }}</p> @enderror
        </div>

        @foreach ($locales as $locale)
            <div>
                <label for="tagline-{{ $locale->code }}" class="label">
                    Slogan @if ($locales->count() > 1) <span class="font-normal text-ink-400">({{ $locale->native_name }})</span> @endif
                </label>
                <input id="tagline-{{ $locale->code }}" type="text" wire:model="general.tagline.{{ $locale->code }}" class="field">
            </div>
        @endforeach
        <p class="hint">Slogan, site adının yanında kısa bir açıklama olarak kullanılır.</p>

        <div class="grid gap-4 sm:grid-cols-3">
            @foreach ([
                'logo_id' => ['Logo', 'Açık zeminde kullanılan ana logo.'],
                'logo_light_id' => ['Açık renkli logo', 'Koyu zeminlerde kullanılır.'],
                'favicon_id' => ['Sekme simgesi', 'Kare, en az 64×64 piksel.'],
            ] as $field => [$label, $hint])
                @php $media = \App\Models\Media::find($general[$field] ?? null); @endphp
                <div>
                    <span class="label">{{ $label }}</span>
                    <div class="rounded-lg border border-ink-200 p-2">
                        @if ($media)
                            <img src="{{ $media->url('small') }}" alt="" class="mx-auto mb-2 h-12 object-contain">
                            <button type="button" wire:click="$set('general.{{ $field }}', null)"
                                    class="btn-ghost btn-sm w-full text-red-600">Kaldır</button>
                        @else
                            <button type="button" class="btn-secondary btn-sm w-full"
                                    @click="$dispatch('open-media-picker', { target: 'general.{{ $field }}' })"
                                    @media-applied.stop="$wire.set('general.{{ $field }}', $event.detail.mediaId)">
                                Seç
                            </button>
                        @endif
                    </div>
                    <p class="hint">{{ $hint }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="card-pad space-y-4">
        <div>
            <h2 class="text-sm font-semibold text-ink-900">İletişim bilgileri</h2>
            <p class="mt-0.5 text-xs text-ink-500">Sitenin her yerindeki numaralar ve e-posta buradan gelir; burada değiştirmek hepsini birden günceller.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="c-email" class="label">E-posta</label>
                <input id="c-email" type="email" wire:model="contact.email"
                       class="field @error('contact.email') field-error @enderror">
                @error('contact.email') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="c-phone" class="label">Telefon (santral)</label>
                <input id="c-phone" type="tel" wire:model="contact.phone" class="field" placeholder="0312 000 00 00">
                <p class="hint">Sitede öne çıkan ana numara.</p>
            </div>
            <div>
                <label for="c-mobile" class="label">Cep telefonu</label>
                <input id="c-mobile" type="tel" wire:model="contact.mobile" class="field" placeholder="0500 000 00 00">
                <p class="hint">Telefonla aynıysa sitede bir kez yazılır.</p>
            </div>
            <div>
                <label for="c-wa" class="label">WhatsApp numarası</label>
                <input id="c-wa" type="tel" wire:model="contact.whatsapp" class="field" placeholder="905550000000">
                <p class="hint">Başında ülke kodu, boşluksuz.</p>
            </div>
            <div>
                <label for="c-fax" class="label">Faks</label>
                <input id="c-fax" type="tel" wire:model="contact.fax" class="field">
            </div>
        </div>

        <p class="text-xs text-ink-500">Boş bıraktığınız bilgi sitede hiç görünmez.</p>

        @foreach ($locales as $locale)
            <div>
                <label for="addr-{{ $locale->code }}" class="label">
                    Adres @if ($locales->count() > 1) <span class="font-normal text-ink-400">({{ $locale->native_name }})</span> @endif
                </label>
                <textarea id="addr-{{ $locale->code }}" rows="2" wire:model="contact.address.{{ $locale->code }}" class="field"></textarea>
            </div>
        @endforeach
    </section>

    <section class="card-pad space-y-4">
        <div>
            <h2 class="text-sm font-semibold text-ink-900">Sosyal medya</h2>
            <p class="mt-0.5 text-xs text-ink-500">Boş bıraktığınız hesaplar sitede hiç görünmez.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'x' => 'X (Twitter)', 'youtube' => 'YouTube'] as $key => $label)
                <div>
                    <label for="s-{{ $key }}" class="label">{{ $label }}</label>
                    <input id="s-{{ $key }}" type="url" wire:model="social.{{ $key }}"
                           class="field @error('social.'.$key) field-error @enderror" placeholder="https://">
                    @error('social.'.$key) <p class="error">{{ $message }}</p> @enderror
                </div>
            @endforeach
        </div>
    </section>

    <div class="flex justify-end gap-2">
        <button type="submit" class="btn-primary">
            <span wire:loading.remove wire:target="save">Kaydet</span>
            <span wire:loading wire:target="save">Kaydediliyor…</span>
        </button>
    </div>
</form>
