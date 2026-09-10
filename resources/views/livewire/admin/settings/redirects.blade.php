<div class="max-w-4xl space-y-4">

    <div class="card-pad">
        <h2 class="text-sm font-semibold text-ink-900">Ne işe yarar?</h2>
        <p class="mt-1 text-sm text-ink-600">
            Eski siteden taşınırken ya da bir sayfanın adresi değiştiğinde, eski adrese gelen ziyaretçiler
            ve arama motorları buradan yeni adrese yönlendirilir. Bu, Google'daki mevcut sıralamanızı korur.
        </p>
    </div>

    <form wire:submit="add" class="card-pad">
        <div class="grid gap-3 sm:grid-cols-[1fr_1fr_auto_auto] sm:items-start">
            <div>
                <label for="r-from" class="label">Eski adres</label>
                <input id="r-from" type="text" wire:model="from" class="field @error('from') field-error @enderror"
                       placeholder="/eski-sayfa.html">
                @error('from') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="r-to" class="label">Yeni adres</label>
                <input id="r-to" type="text" wire:model="to" class="field @error('to') field-error @enderror"
                       placeholder="/tr/hakkimizda">
                @error('to') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="r-code" class="label">Tür</label>
                <select id="r-code" wire:model="statusCode" class="field w-32">
                    <option value="301">Kalıcı (301)</option>
                    <option value="302">Geçici (302)</option>
                </select>
            </div>
            <button type="submit" class="btn-primary mt-6.5">Ekle</button>
        </div>
        <p class="hint mt-2">Kalıcı yönlendirmede Google sıralamayı yeni adrese taşır. Emin değilseniz kalıcıyı seçin.</p>
    </form>

    @if ($redirects->isEmpty())
        <div class="card flex flex-col items-center justify-center py-14 text-center">
            <x-icon name="link" class="size-9 text-ink-200"/>
            <p class="mt-3 text-sm font-medium text-ink-700">Henüz yönlendirme yok.</p>
        </div>
    @else
        <div class="card overflow-hidden">
            <table class="w-full text-sm">
                <caption class="sr-only">Adres yönlendirmeleri</caption>
                <thead>
                    <tr class="border-b border-ink-100 bg-ink-50/60 text-left text-xs font-medium text-ink-500">
                        <th scope="col" class="px-4 py-2.5">Eski adres</th>
                        <th scope="col" class="px-4 py-2.5">Yeni adres</th>
                        <th scope="col" class="px-4 py-2.5">Tür</th>
                        <th scope="col" class="px-4 py-2.5">Kullanım</th>
                        <th scope="col" class="px-4 py-2.5"><span class="sr-only">İşlem</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @foreach ($redirects as $redirect)
                        <tr class="group">
                            <td class="px-4 py-2.5 font-mono text-xs text-ink-700">{{ $redirect->from }}</td>
                            <td class="px-4 py-2.5 font-mono text-xs text-ink-700">{{ $redirect->to }}</td>
                            <td class="px-4 py-2.5 text-xs text-ink-500">{{ $redirect->status_code === 301 ? 'Kalıcı' : 'Geçici' }}</td>
                            <td class="px-4 py-2.5 text-xs text-ink-500">
                                {{ $redirect->hits }} kez
                                @if ($redirect->last_hit_at)
                                    <span class="text-ink-400">· {{ $redirect->last_hit_at->diffForHumans() }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <button wire:click="delete({{ $redirect->id }})"
                                        class="btn-ghost btn-sm text-red-600 opacity-0 group-hover:opacity-100">
                                    <x-icon name="trash" class="size-4"/><span class="sr-only">Sil</span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $redirects->links() }}
    @endif
</div>
