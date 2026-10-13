@if ($enabled || $context->editing)
    <button type="button" class="qcb" data-quote-open
            aria-label="{{ $settings['text'] ?: 'Teklif sepeti' }}">
        <x-icon name="cart" class="qcb-icon" />
        @if ($settings['show_text'] ?? true)
            <span class="qcb-text">{{ $settings['text'] }}</span>
        @endif
        <span class="qcb-count" data-quote-count>{{ $count }}</span>
    </button>
@endif
