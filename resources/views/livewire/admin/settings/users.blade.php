<div class="max-w-4xl space-y-4">

    @if ($generatedPassword)
        <div class="card border-emerald-300 bg-emerald-50 p-4">
            <h2 class="text-sm font-semibold text-emerald-900">Kullanıcı oluşturuldu</h2>
            <p class="mt-1 text-sm text-emerald-800">
                Şifreyi kullanıcıya güvenli bir yoldan iletin. Bu şifre bir daha gösterilmeyecek.
            </p>
            <div class="mt-3 flex items-center gap-2">
                <input type="text" readonly value="{{ $generatedPassword }}"
                       class="field bg-white font-mono text-sm" onfocus="this.select()">
                <button wire:click="$set('generatedPassword', null)" class="btn-secondary shrink-0">Tamam</button>
            </div>
        </div>
    @endif

    <div class="flex items-center justify-between">
        <p class="text-sm text-ink-600">
            Her kullanıcıya işini yapmasına yetecek en düşük yetkiyi verin.
        </p>
        <button wire:click="$set('inviting', true)" class="btn-primary">
            <x-icon name="plus" class="size-4"/> Kullanıcı ekle
        </button>
    </div>

    @if ($inviting)
        <form wire:submit="invite" class="card-pad space-y-4">
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label for="u-name" class="label">Ad soyad</label>
                    <input id="u-name" type="text" wire:model="name" class="field @error('name') field-error @enderror">
                    @error('name') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="u-email" class="label">E-posta</label>
                    <input id="u-email" type="email" wire:model="email" class="field @error('email') field-error @enderror">
                    @error('email') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="u-role" class="label">Rol</label>
                    <select id="u-role" wire:model="role" class="field @error('role') field-error @enderror">
                        @foreach ($roles as $key => $role)
                            @if (auth()->user()->atLeast($key))
                                <option value="{{ $key }}">{{ $role['label'] }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('role') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <dl class="grid gap-2 rounded-lg bg-ink-50 p-3 text-xs sm:grid-cols-2">
                <div><dt class="font-medium text-ink-700">Site Sahibi</dt><dd class="text-ink-500">Her şeyi yapabilir, kullanıcı yönetir.</dd></div>
                <div><dt class="font-medium text-ink-700">Yönetici</dt><dd class="text-ink-500">İçerik, tasarım ve ayarlar.</dd></div>
                <div><dt class="font-medium text-ink-700">Editör</dt><dd class="text-ink-500">Tüm içeriği düzenler, ayarlara giremez.</dd></div>
                <div><dt class="font-medium text-ink-700">Yazar</dt><dd class="text-ink-500">Yalnızca içerik ekler ve düzenler.</dd></div>
            </dl>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary">Oluştur</button>
                <button type="button" wire:click="$set('inviting', false)" class="btn-ghost">Vazgeç</button>
            </div>
        </form>
    @endif

    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <caption class="sr-only">Panel kullanıcıları</caption>
            <thead>
                <tr class="border-b border-ink-100 bg-ink-50/60 text-left text-xs font-medium text-ink-500">
                    <th scope="col" class="px-4 py-2.5">Kullanıcı</th>
                    <th scope="col" class="px-4 py-2.5">Rol</th>
                    <th scope="col" class="hidden px-4 py-2.5 sm:table-cell">İki adımlı</th>
                    <th scope="col" class="hidden px-4 py-2.5 lg:table-cell">Son giriş</th>
                    <th scope="col" class="px-4 py-2.5"><span class="sr-only">İşlemler</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @foreach ($users as $user)
                    <tr class="group {{ $user->is_active ? '' : 'opacity-60' }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-ink-100 text-xs font-semibold text-ink-600">
                                    {{ $user->initials() }}
                                </span>
                                <span class="min-w-0">
                                    <span class="block truncate font-medium text-ink-900">
                                        {{ $user->name }}
                                        @if ($user->id === auth()->id()) <span class="text-xs font-normal text-ink-400">(siz)</span> @endif
                                    </span>
                                    <span class="block truncate text-xs text-ink-500">{{ $user->email }}</span>
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if ($user->id === auth()->id() || ! auth()->user()->atLeast($user->role))
                                <span class="badge-gray">{{ $user->roleLabel() }}</span>
                            @else
                                <select wire:change="changeRole({{ $user->id }}, $event.target.value)"
                                        class="field w-auto px-2 py-1 text-xs" aria-label="{{ $user->name }} rolü">
                                    @foreach ($roles as $key => $role)
                                        <option value="{{ $key }}" @selected($user->role === $key)
                                                @disabled(! auth()->user()->atLeast($key))>{{ $role['label'] }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </td>
                        <td class="hidden px-4 py-3 sm:table-cell">
                            @if ($user->hasTwoFactor())
                                <span class="badge-green">Açık</span>
                            @else
                                <span class="badge-amber">Kapalı</span>
                            @endif
                        </td>
                        <td class="hidden px-4 py-3 text-xs text-ink-500 lg:table-cell">
                            {{ $user->last_login_at?->diffForHumans() ?? 'Hiç girmedi' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1 opacity-0 transition group-hover:opacity-100 focus-within:opacity-100">
                                @if ($user->id !== auth()->id())
                                    <button wire:click="toggleActive({{ $user->id }})" class="btn-ghost btn-sm">
                                        {{ $user->is_active ? 'Kapat' : 'Aç' }}
                                    </button>
                                    <button wire:click="delete({{ $user->id }})"
                                            wire:confirm="“{{ $user->name }}” hesabı silinecek. Devam edilsin mi?"
                                            class="btn-ghost btn-sm text-red-600 hover:bg-red-50">
                                        <x-icon name="trash" class="size-4"/><span class="sr-only">Sil</span>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
