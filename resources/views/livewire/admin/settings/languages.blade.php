<div class="max-w-3xl space-y-4">

    <div class="card-pad">
        <p class="text-sm text-ink-600">
            Eklediğiniz her dil için içerikleri ayrı ayrı doldurmanız gerekir. Bir dilde boş bırakılan alanlar,
            ziyaretçiye varsayılan dildeki karşılığıyla gösterilir; site hiçbir zaman boş görünmez.
        </p>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <caption class="sr-only">Site dilleri</caption>
            <thead>
                <tr class="border-b border-ink-100 bg-ink-50/60 text-left text-xs font-medium text-ink-500">
                    <th scope="col" class="px-4 py-2.5">Dil</th>
                    <th scope="col" class="px-4 py-2.5">Kod</th>
                    <th scope="col" class="px-4 py-2.5">Durum</th>
                    <th scope="col" class="px-4 py-2.5"><span class="sr-only">İşlemler</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @foreach ($languages as $language)
                    <tr class="group">
                        <td class="px-4 py-3">
                            <span class="font-medium text-ink-900">{{ $language->native_name }}</span>
                            <span class="text-ink-400">· {{ $language->name }}</span>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-ink-500">{{ $language->code }}</td>
                        <td class="px-4 py-3">
                            @if ($language->is_default)
                                <span class="badge-green">Varsayılan</span>
                            @elseif ($language->is_active)
                                <span class="badge-gray">Yayında</span>
                            @else
                                <span class="badge-amber">Gizli</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1 opacity-0 transition group-hover:opacity-100 focus-within:opacity-100">
                                @unless ($language->is_default)
                                    <button wire:click="setDefault({{ $language->id }})" class="btn-ghost btn-sm">Varsayılan yap</button>
                                    <button wire:click="toggleActive({{ $language->id }})" class="btn-ghost btn-sm">
                                        {{ $language->is_active ? 'Gizle' : 'Göster' }}
                                    </button>
                                    <button wire:click="delete({{ $language->id }})"
                                            wire:confirm="“{{ $language->native_name }}” dili kaldırılacak. Bu dildeki içerikler veritabanında kalır ama sitede görünmez. Devam edilsin mi?"
                                            class="btn-ghost btn-sm text-red-600 hover:bg-red-50">
                                        <x-icon name="trash" class="size-4"/><span class="sr-only">Sil</span>
                                    </button>
                                @endunless
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($adding)
        <form wire:submit="add" class="card-pad space-y-4">
            <h2 class="text-sm font-semibold text-ink-900">Yeni dil ekle</h2>

            <div>
                <label for="lang-code" class="label">Dil</label>
                <select id="lang-code" wire:model.live="code" class="field @error('code') field-error @enderror">
                    <option value="">— Seçin —</option>
                    @foreach ($common as $c => [$n, $native])
                        <option value="{{ $c }}">{{ $n }} ({{ $native }})</option>
                    @endforeach
                </select>
                @error('code') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="lang-name" class="label">Türkçe adı</label>
                    <input id="lang-name" type="text" wire:model="name" class="field @error('name') field-error @enderror">
                    @error('name') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="lang-native" class="label">Kendi dilindeki adı</label>
                    <input id="lang-native" type="text" wire:model="nativeName" class="field @error('nativeName') field-error @enderror">
                    @error('nativeName') <p class="error">{{ $message }}</p> @enderror
                    <p class="hint">Dil seçicide bu yazı görünür.</p>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary">Dili ekle</button>
                <button type="button" wire:click="$set('adding', false)" class="btn-ghost">Vazgeç</button>
            </div>
        </form>
    @else
        <button wire:click="$set('adding', true)" class="btn-secondary">
            <x-icon name="plus" class="size-4"/> Dil ekle
        </button>
    @endif
</div>
