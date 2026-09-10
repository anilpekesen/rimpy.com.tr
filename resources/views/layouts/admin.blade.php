<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Panel' }} — {{ \App\Models\Setting::get('general.site_name', config('app.name')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full">
<div x-data="{ mobileNav: false }" class="min-h-full lg:flex">

    {{-- ── Kenar çubuğu ─────────────────────────────────────────────── --}}
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:m-3 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow-lg">
        İçeriğe geç
    </a>

    <div x-show="mobileNav" x-cloak @click="mobileNav = false"
         class="fixed inset-0 z-30 bg-ink-950/50 lg:hidden"></div>

    <nav aria-label="Panel menüsü"
         :class="mobileNav ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
         class="fixed inset-y-0 left-0 z-40 flex w-64 shrink-0 flex-col bg-ink-900 transition-transform lg:sticky lg:top-0 lg:h-screen">

        <div class="flex h-14 shrink-0 items-center gap-2.5 border-b border-white/8 px-4">
            <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-brand-600 text-sm font-semibold text-white">
                {{ mb_substr(\App\Models\Setting::get('general.site_name', config('app.name')), 0, 1) }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="truncate text-sm font-semibold text-white">
                    {{ \App\Models\Setting::get('general.site_name', config('app.name')) }}
                </div>
            </div>
            <button @click="mobileNav = false" class="rounded-md p-1 text-ink-400 hover:text-white lg:hidden" aria-label="Menüyü kapat">
                <x-icon name="chevron-right" class="size-5 rotate-180"/>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-2.5 pb-4">
            @php
                $types = \App\Models\ContentType::where('is_active', true)->orderBy('sort')->get();
                $unread = \App\Models\FormSubmission::where('is_read', false)->where('is_spam', false)->count();
            @endphp

            <div class="pt-3">
                <a href="{{ route('admin.dashboard') }}" wire:navigate
                   class="{{ request()->routeIs('admin.dashboard') ? 'nav-item-active' : 'nav-item' }}">
                    <x-icon name="gauge" class="size-4.5 shrink-0"/> Panel
                </a>
            </div>

            <div class="nav-section">İçerik</div>
            <a href="{{ route('admin.pages.index') }}" wire:navigate
               class="{{ request()->routeIs('admin.pages.*') ? 'nav-item-active' : 'nav-item' }}">
                <x-icon name="file-text" class="size-4.5 shrink-0"/> Sayfalar
            </a>
            @foreach ($types as $type)
                <a href="{{ route('admin.content.index', $type->key) }}" wire:navigate
                   class="{{ request()->routeIs('admin.content.*') && request()->route('type')?->key === $type->key ? 'nav-item-active' : 'nav-item' }}">
                    <x-icon name="{{ $type->icon }}" class="size-4.5 shrink-0"/> {{ $type->pluralLabel('tr') }}
                </a>
            @endforeach
            <a href="{{ route('admin.media.index') }}" wire:navigate
               class="{{ request()->routeIs('admin.media.*') ? 'nav-item-active' : 'nav-item' }}">
                <x-icon name="image" class="size-4.5 shrink-0"/> Medya
            </a>

            <div class="nav-section">Tasarım</div>
            <a href="{{ route('admin.design.theme') }}" wire:navigate
               class="{{ request()->routeIs('admin.design.*') ? 'nav-item-active' : 'nav-item' }}">
                <x-icon name="palette" class="size-4.5 shrink-0"/> Tema ayarları
            </a>
            <a href="{{ route('admin.menus.index') }}" wire:navigate
               class="{{ request()->routeIs('admin.menus.*') ? 'nav-item-active' : 'nav-item' }}">
                <x-icon name="menu" class="size-4.5 shrink-0"/> Menüler
            </a>
            <a href="{{ route('admin.templates.index') }}" wire:navigate
               class="{{ request()->routeIs('admin.templates.*') ? 'nav-item-active' : 'nav-item' }}">
                <x-icon name="blocks" class="size-4.5 shrink-0"/> Şablonlar
            </a>

            <div class="nav-section">Etkileşim</div>
            <a href="{{ route('admin.forms.index') }}" wire:navigate
               class="{{ request()->routeIs('admin.forms.*') ? 'nav-item-active' : 'nav-item' }}">
                <x-icon name="clipboard" class="size-4.5 shrink-0"/> Formlar
            </a>
            <a href="{{ route('admin.submissions.index') }}" wire:navigate
               class="{{ request()->routeIs('admin.submissions.*') ? 'nav-item-active' : 'nav-item' }}">
                <x-icon name="inbox" class="size-4.5 shrink-0"/>
                <span class="flex-1">Gelen mesajlar</span>
                @if ($unread > 0)
                    <span class="rounded-full bg-brand-500 px-1.5 py-0.5 text-[11px] font-semibold text-white">{{ $unread }}</span>
                @endif
            </a>

            @if (auth()->user()->atLeast('admin'))
                <div class="nav-section">Ayarlar</div>
                <a href="{{ route('admin.settings.general') }}" wire:navigate
                   class="{{ request()->routeIs('admin.settings.general') ? 'nav-item-active' : 'nav-item' }}">
                    <x-icon name="settings" class="size-4.5 shrink-0"/> Genel ayarlar
                </a>
                <a href="{{ route('admin.settings.content-types') }}" wire:navigate
                   class="{{ request()->routeIs('admin.settings.content-types') ? 'nav-item-active' : 'nav-item' }}">
                    <x-icon name="box" class="size-4.5 shrink-0"/> İçerik türleri
                </a>
                <a href="{{ route('admin.settings.languages') }}" wire:navigate
                   class="{{ request()->routeIs('admin.settings.languages') ? 'nav-item-active' : 'nav-item' }}">
                    <x-icon name="languages" class="size-4.5 shrink-0"/> Diller
                </a>
                <a href="{{ route('admin.settings.seo') }}" wire:navigate
                   class="{{ request()->routeIs('admin.settings.seo') ? 'nav-item-active' : 'nav-item' }}">
                    <x-icon name="search" class="size-4.5 shrink-0"/> SEO
                </a>
                <a href="{{ route('admin.users.index') }}" wire:navigate
                   class="{{ request()->routeIs('admin.users.*') ? 'nav-item-active' : 'nav-item' }}">
                    <x-icon name="users" class="size-4.5 shrink-0"/> Kullanıcılar
                </a>
                <a href="{{ route('admin.settings.security') }}" wire:navigate
                   class="{{ request()->routeIs('admin.settings.security') ? 'nav-item-active' : 'nav-item' }}">
                    <x-icon name="shield" class="size-4.5 shrink-0"/> Güvenlik
                </a>
            @endif
        </div>

        {{-- Kullanıcı --}}
        <div x-data="{ open: false }" class="relative shrink-0 border-t border-white/8 p-2.5">
            <button @click="open = !open" :aria-expanded="open"
                    class="flex w-full items-center gap-2.5 rounded-lg px-2 py-2 text-left transition hover:bg-white/8">
                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-white/12 text-xs font-semibold text-white">
                    {{ auth()->user()->initials() }}
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-sm font-medium text-white">{{ auth()->user()->name }}</span>
                    <span class="block truncate text-xs text-ink-400">{{ auth()->user()->roleLabel() }}</span>
                </span>
                <x-icon name="chevron-down" class="size-4 shrink-0 text-ink-400 transition" ::class="open && 'rotate-180'"/>
            </button>

            <div x-show="open" x-cloak @click.outside="open = false" x-transition.opacity.duration.150ms
                 class="absolute inset-x-2.5 bottom-full mb-1 overflow-hidden rounded-lg border border-ink-200 bg-white py-1 shadow-lg">
                <a href="{{ route('admin.account') }}" wire:navigate
                   class="flex items-center gap-2 px-3 py-2 text-sm text-ink-700 hover:bg-ink-50">
                    <x-icon name="settings" class="size-4"/> Hesabım
                </a>
                <a href="{{ url('/') }}" target="_blank" rel="noopener"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-ink-700 hover:bg-ink-50">
                    <x-icon name="external" class="size-4"/> Siteyi görüntüle
                </a>
                <form method="POST" action="{{ route('admin.logout') }}" class="border-t border-ink-100">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                        <x-icon name="logout" class="size-4"/> Çıkış yap
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- ── İçerik ───────────────────────────────────────────────────── --}}
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 flex h-14 shrink-0 items-center gap-3 border-b border-ink-200 bg-white/85 px-4 backdrop-blur-sm lg:px-6">
            <button @click="mobileNav = true" class="btn-ghost -ml-1.5 p-2 lg:hidden" aria-label="Menüyü aç">
                <x-icon name="menu"/>
            </button>

            <div class="min-w-0 flex-1">
                <h1 class="truncate text-base font-semibold text-ink-900">{{ $title ?? 'Panel' }}</h1>
                @isset($subtitle)
                    <p class="truncate text-xs text-ink-500">{{ $subtitle }}</p>
                @endisset
            </div>

            @isset($actions)
                <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
            @endisset
        </header>

        <main id="main" class="flex-1 p-4 lg:p-6">
            {{-- Bildirimler --}}
            <div x-data="{ items: [] }"
                 @notify.window="
                    const id = Date.now();
                    items.push({ id, ...$event.detail });
                    setTimeout(() => items = items.filter(i => i.id !== id), 5000);
                 "
                 class="pointer-events-none fixed inset-x-0 top-16 z-50 flex flex-col items-center gap-2 px-4"
                 role="status" aria-live="polite">
                <template x-for="item in items" :key="item.id">
                    <div x-transition
                         class="pointer-events-auto flex max-w-md items-start gap-2.5 rounded-lg bg-ink-900 px-4 py-2.5 text-sm text-white shadow-lg">
                        <span x-text="item.message"></span>
                    </div>
                </template>
            </div>

            {{ $slot }}
        </main>
    </div>
</div>
<livewire:media-picker />
@livewireScripts
</body>
</html>
