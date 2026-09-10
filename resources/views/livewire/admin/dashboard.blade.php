<div class="space-y-6">

    {{-- Dikkat isteyen durumlar --}}
    @if ($warnings)
        <div class="space-y-2">
            @foreach ($warnings as $warning)
                @php $info = ($warning['level'] ?? 'warn') === 'info'; @endphp
                <div class="flex flex-wrap items-center gap-3 rounded-xl border px-4 py-3 text-sm
                            {{ $info ? 'border-ink-200 bg-white text-ink-700' : 'border-amber-200 bg-amber-50 text-amber-900' }}">
                    <x-icon name="alert" class="size-4.5 shrink-0 {{ $info ? 'text-ink-400' : 'text-amber-600' }}"/>
                    <p class="min-w-0 flex-1">{{ $warning['text'] }}</p>
                    <a href="{{ $warning['action']['href'] }}" wire:navigate
                       class="{{ $info ? 'btn-secondary' : 'btn-primary' }} btn-sm shrink-0">
                        {{ $warning['action']['label'] }}
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Özet --}}
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <a href="{{ $stat['href'] }}" wire:navigate
               class="card group flex items-center gap-4 p-4 transition hover:border-ink-300 hover:shadow-sm">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-ink-100 text-ink-500 transition group-hover:bg-brand-50 group-hover:text-brand-600">
                    <x-icon name="{{ $stat['icon'] }}" class="size-5"/>
                </span>
                <span class="min-w-0">
                    <span class="block text-2xl font-semibold tabular-nums text-ink-900">{{ $stat['value'] }}</span>
                    <span class="block truncate text-xs text-ink-500">{{ $stat['label'] }}</span>
                </span>
            </a>
        @endforeach
    </div>

    <div class="grid gap-5 lg:grid-cols-3">

        {{-- Gelen mesajlar --}}
        <section class="card lg:col-span-2">
            <div class="flex items-center justify-between border-b border-ink-100 px-5 py-3.5">
                <h2 class="text-sm font-semibold text-ink-900">Son gelen mesajlar</h2>
                <a href="{{ route('admin.submissions.index') }}" wire:navigate
                   class="text-xs font-medium text-brand-600 hover:underline">Tümü</a>
            </div>

            @forelse ($recentSubmissions as $submission)
                <a href="{{ route('admin.submissions.index') }}" wire:navigate
                   class="flex items-start gap-3 border-b border-ink-100 px-5 py-3 last:border-0 hover:bg-ink-50">
                    @unless ($submission->is_read)
                        <span class="mt-1.5 size-2 shrink-0 rounded-full bg-brand-500" aria-label="Okunmadı"></span>
                    @else
                        <span class="mt-1.5 size-2 shrink-0"></span>
                    @endunless
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm {{ $submission->is_read ? 'text-ink-600' : 'font-medium text-ink-900' }}">
                            {{ $submission->preview() }}
                        </p>
                        <p class="mt-0.5 text-xs text-ink-400">
                            {{ $submission->form?->name }} · {{ $submission->created_at->diffForHumans() }}
                        </p>
                    </div>
                </a>
            @empty
                <p class="px-5 py-8 text-center text-sm text-ink-400">Henüz mesaj gelmedi.</p>
            @endforelse
        </section>

        {{-- Taslaklar --}}
        <section class="card">
            <div class="flex items-center justify-between border-b border-ink-100 px-5 py-3.5">
                <h2 class="text-sm font-semibold text-ink-900">Yarım kalan sayfalar</h2>
            </div>

            @forelse ($drafts as $page)
                <a href="{{ route('admin.builder.page', $page) }}"
                   class="flex items-center gap-2 border-b border-ink-100 px-5 py-3 last:border-0 hover:bg-ink-50">
                    <span class="min-w-0 flex-1 truncate text-sm text-ink-700">{{ $page->t('title') ?: 'Adsız sayfa' }}</span>
                    <x-icon name="chevron-right" class="size-4 shrink-0 text-ink-300"/>
                </a>
            @empty
                <p class="px-5 py-8 text-center text-sm text-ink-400">Taslak sayfa yok.</p>
            @endforelse
        </section>
    </div>

    {{-- Hareketler --}}
    <section class="card">
        <div class="border-b border-ink-100 px-5 py-3.5">
            <h2 class="text-sm font-semibold text-ink-900">Son hareketler</h2>
            <p class="mt-0.5 text-xs text-ink-500">Panelde yapılan her değişiklik burada kayıtlıdır.</p>
        </div>

        <ul class="divide-y divide-ink-100">
            @forelse ($recentActivity as $entry)
                <li class="flex items-baseline gap-3 px-5 py-2.5 text-sm">
                    <span class="w-32 shrink-0 truncate text-xs text-ink-400">
                        {{ $entry->created_at?->diffForHumans() }}
                    </span>
                    <span class="min-w-0 flex-1 text-ink-700">
                        {{ $entry->description ?? $entry->event }}
                    </span>
                    <span class="shrink-0 text-xs text-ink-400">{{ $entry->user?->name ?? 'Sistem' }}</span>
                </li>
            @empty
                <li class="px-5 py-8 text-center text-sm text-ink-400">Henüz hareket yok.</li>
            @endforelse
        </ul>
    </section>
</div>
