<div class="max-w-4xl space-y-4">

    <div class="flex rounded-lg border border-ink-200 bg-white p-0.5 text-sm">
        @foreach (['overview' => 'Durum', 'attempts' => 'Giriş denemeleri', 'activity' => 'Hareket kaydı'] as $key => $label)
            <button wire:click="$set('tab', '{{ $key }}')"
                    class="rounded-md px-3 py-1.5 font-medium transition
                           {{ $tab === $key ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-ink-100' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    @if ($tab === 'overview')
        @if ($recentFailures >= 10)
            <div class="card flex items-start gap-3 border-ink-300 p-4">
                <x-icon name="shield" class="size-5 shrink-0 text-ink-400"/>
                <div class="text-sm">
                    <p class="font-medium text-ink-900">Son 24 saatte {{ $recentFailures }} başarısız giriş denemesi</p>
                    <p class="mt-0.5 text-ink-600">
                        Bu genellikle internette gezinen otomatik botların işidir ve endişelenmenizi gerektirmez —
                        sistem denemeleri sayıyor ve engelliyor. Yine de tüm hesaplarda iki adımlı doğrulamanın
                        açık olduğundan emin olun.
                    </p>
                </div>
            </div>
        @endif

        <ul class="space-y-2">
            @foreach ($checks as $check)
                <li class="card flex items-start gap-3 p-4">
                    <span class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full
                                 {{ $check['ok'] ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                        @if ($check['ok'])
                            <x-icon name="check" class="size-3.5"/>
                        @else
                            <x-icon name="alert" class="size-3.5"/>
                        @endif
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-ink-900">{{ $check['title'] }}</p>
                        <p class="mt-0.5 text-sm text-ink-600">{{ $check['text'] }}</p>
                    </div>
                    @if (! $check['ok'] && isset($check['action']))
                        <a href="{{ $check['action']['href'] }}" wire:navigate class="btn-secondary btn-sm shrink-0">
                            {{ $check['action']['label'] }}
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>

    @elseif ($tab === 'attempts')
        <div class="card overflow-hidden">
            <div class="border-b border-ink-100 px-4 py-3">
                <p class="text-sm text-ink-600">
                    Art arda {{ config('cms.security.max_login_attempts') }} hatalı denemeden sonra o hesap ve IP adresi
                    {{ config('cms.security.lockout_minutes') }} dakika kilitlenir.
                </p>
            </div>
            <table class="w-full text-sm">
                <caption class="sr-only">Başarısız giriş denemeleri</caption>
                <thead>
                    <tr class="border-b border-ink-100 bg-ink-50/60 text-left text-xs font-medium text-ink-500">
                        <th scope="col" class="px-4 py-2.5">Zaman</th>
                        <th scope="col" class="px-4 py-2.5">Denenen e-posta</th>
                        <th scope="col" class="px-4 py-2.5">IP adresi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @forelse ($failedAttempts as $attempt)
                        <tr>
                            <td class="px-4 py-2.5 text-xs text-ink-500">{{ $attempt->created_at?->format('d.m.Y H:i') }}</td>
                            <td class="px-4 py-2.5 text-ink-700">{{ $attempt->email }}</td>
                            <td class="px-4 py-2.5 font-mono text-xs text-ink-500">{{ $attempt->ip_address }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-10 text-center text-sm text-ink-400">Başarısız deneme kaydı yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $failedAttempts->links() }}

    @else
        <div class="card overflow-hidden">
            <div class="border-b border-ink-100 px-4 py-3">
                <p class="text-sm text-ink-600">
                    Panelde yapılan her kalıcı değişiklik burada saklanır. Kayıtlar
                    {{ config('cms.security.activity_log_days') }} gün tutulur.
                </p>
            </div>
            <ul class="divide-y divide-ink-100">
                @forelse ($activity as $entry)
                    <li class="flex flex-wrap items-baseline gap-x-3 gap-y-1 px-4 py-2.5 text-sm">
                        <span class="w-32 shrink-0 text-xs text-ink-400">{{ $entry->created_at?->format('d.m.Y H:i') }}</span>
                        <span class="min-w-0 flex-1 text-ink-700">{{ $entry->description ?? $entry->event }}</span>
                        <span class="shrink-0 text-xs text-ink-500">{{ $entry->user?->name ?? 'Sistem' }}</span>
                        <span class="shrink-0 font-mono text-[11px] text-ink-400">{{ $entry->ip_address }}</span>
                    </li>
                @empty
                    <li class="px-4 py-10 text-center text-sm text-ink-400">Kayıt yok.</li>
                @endforelse
            </ul>
        </div>
        {{ $activity->links() }}
    @endif
</div>
