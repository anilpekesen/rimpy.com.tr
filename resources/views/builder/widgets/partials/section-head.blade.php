{{-- Vitrin bölümlerinin başlığı. *yıldızlı* kelime vurgu renginde. --}}
@if (filled($settings['eyebrow'] ?? null) || filled($settings['title'] ?? null))
    <header class="sh">
        <div>
            @if (filled($settings['eyebrow'] ?? null))
                <span class="sh-eyebrow">{{ $settings['eyebrow'] }}</span>
            @endif
            @if (filled($settings['title'] ?? null))
                <h2 class="sh-title">{!! nl2br(preg_replace('/\*(.+?)\*/u', '<em>$1</em>', e($settings['title']))) !!}</h2>
            @endif
        </div>
        @if (filled($settings['text'] ?? null))
            <p class="sh-text">{{ $settings['text'] }}</p>
        @endif
    </header>
@endif
