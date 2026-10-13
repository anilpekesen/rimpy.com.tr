<div class="space-y-4">

    <div class="flex flex-wrap items-center gap-2">
        <div class="flex rounded-lg border border-ink-200 bg-white p-0.5 text-sm">
            @foreach ([
                'inbox' => ['Gelen kutusu', $counts['inbox']],
                'unread' => ['Okunmamış', $counts['unread']],
                'spam' => ['İstenmeyen', $counts['spam']],
            ] as $key => [$label, $count])
                <button wire:click="$set('filter', '{{ $key }}')"
                        class="rounded-md px-3 py-1.5 font-medium transition
                               {{ $filter === $key ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-ink-100' }}">
                    {{ $label }}
                    <span class="{{ $filter === $key ? 'text-ink-300' : 'text-ink-400' }}">{{ $count }}</span>
                </button>
            @endforeach
        </div>

        @if ($forms->count() > 1)
            <select wire:model.live="formId" class="field w-auto" aria-label="Forma göre süz">
                <option value="">Tüm formlar</option>
                @foreach ($forms as $f)
                    <option value="{{ $f->id }}">{{ $f->name }}</option>
                @endforeach
            </select>
        @endif

        <div class="ml-auto flex gap-2">
            @if ($counts['unread'] > 0)
                <button wire:click="markAllRead" class="btn-secondary btn-sm">Tümünü okundu say</button>
            @endif
            <button wire:click="export" class="btn-secondary btn-sm">
                <x-icon name="download" class="size-3.5"/> Excel'e aktar
            </button>
        </div>
    </div>

    @if ($submissions->isEmpty())
        <div class="card flex flex-col items-center justify-center py-16 text-center">
            <x-icon name="inbox" class="size-10 text-ink-200"/>
            <p class="mt-3 text-sm font-medium text-ink-700">
                {{ $filter === 'spam' ? 'İstenmeyen mesaj yok.' : 'Henüz mesaj gelmedi.' }}
            </p>
            <p class="mt-1 max-w-sm text-sm text-ink-400">
                Sitedeki formlar doldurulduğunda mesajlar burada birikir.
            </p>
        </div>
    @else
        <ul class="card divide-y divide-ink-100">
            @foreach ($submissions as $submission)
                <li>
                    <button wire:click="open({{ $submission->id }})"
                            class="flex w-full items-start gap-3 px-4 py-3 text-left transition hover:bg-ink-50">
                        <span class="mt-1.5 size-2 shrink-0 rounded-full {{ $submission->is_read ? 'bg-transparent' : 'bg-brand-500' }}"
                              @if (! $submission->is_read) aria-label="Okunmadı" @endif></span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm {{ $submission->is_read ? 'text-ink-600' : 'font-medium text-ink-900' }}">
                                {{ $submission->preview() }}
                            </span>
                            <span class="mt-0.5 block text-xs text-ink-400">
                                {{ $submission->form?->name }} · {{ $submission->created_at->format('d.m.Y H:i') }}
                                @if ($submission->is_spam) · <span class="text-amber-600">istenmeyen</span> @endif
                            </span>
                        </span>
                        <x-icon name="chevron-right" class="mt-1 size-4 shrink-0 text-ink-300"/>
                    </button>
                </li>
            @endforeach
        </ul>

        {{ $submissions->links() }}
    @endif

    @if ($open)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-ink-950/50 p-4"
             wire:click.self="$set('openId', null)" role="dialog" aria-modal="true" aria-label="Mesaj">
            <div class="max-h-full w-full max-w-lg overflow-y-auto rounded-xl bg-white shadow-xl">
                <div class="flex items-start justify-between gap-3 border-b border-ink-100 px-5 py-3.5">
                    <div class="min-w-0">
                        <h2 class="text-sm font-semibold text-ink-900">{{ $open->form?->name }}</h2>
                        <p class="mt-0.5 text-xs text-ink-500">{{ $open->created_at->format('d F Y, H:i') }}</p>
                    </div>
                    <button wire:click="$set('openId', null)" class="btn-ghost shrink-0 p-1.5" aria-label="Kapat">
                        <x-icon name="chevron-right" class="size-4 rotate-45"/>
                    </button>
                </div>

                <dl class="divide-y divide-ink-100">
                    @foreach ($open->data as $key => $value)
                        @php
                            $label = $open->form?->fieldLabel($key) ?? $key;
                        @endphp
                        <div class="px-5 py-2.5">
                            <dt class="text-xs font-medium text-ink-500">{{ $label }}</dt>
                            <dd class="mt-0.5 text-sm break-words whitespace-pre-line text-ink-800">
                                @if (is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL))
                                    <a href="mailto:{{ $value }}" class="text-brand-600 hover:underline">{{ $value }}</a>
                                @elseif (is_string($value) && str_starts_with($value, 'http'))
                                    <a href="{{ $value }}" target="_blank" rel="noopener" class="text-brand-600 hover:underline">{{ $value }}</a>
                                @elseif (is_bool($value) || $value === '1')
                                    {{ $value ? 'Evet' : 'Hayır' }}
                                @else
                                    {{ $value }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>

                <div class="border-t border-ink-100 px-5 py-2.5 text-xs text-ink-400">
                    @if ($open->page_url)
                        Gönderildiği sayfa: <span class="break-all">{{ $open->page_url }}</span><br>
                    @endif
                    IP: <span class="font-mono">{{ $open->ip_address }}</span>
                </div>

                <div class="flex justify-between gap-2 border-t border-ink-100 px-5 py-3">
                    <button wire:click="markSpam({{ $open->id }})" class="btn-ghost btn-sm">
                        {{ $open->is_spam ? 'Gelen kutusuna taşı' : 'İstenmeyen olarak işaretle' }}
                    </button>
                    <button wire:click="delete({{ $open->id }})"
                            wire:confirm="Bu mesaj kalıcı olarak silinecek. Devam edilsin mi?"
                            class="btn-danger btn-sm">Sil</button>
                </div>
            </div>
        </div>
    @endif
</div>
