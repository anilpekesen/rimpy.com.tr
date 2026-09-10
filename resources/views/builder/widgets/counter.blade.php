@php
    $value = (int) ($settings['value'] ?? 0);
    $display = ($settings['separator'] ?? true) ? number_format($value, 0, ',', '.') : (string) $value;
@endphp

<div class="counter">
    <span class="counter-value"
          data-counter="{{ $value }}"
          data-counter-duration="{{ (int) (data_get($settings, 'duration.size', 2) * 1000) }}"
          data-counter-separator="{{ ($settings['separator'] ?? true) ? '1' : '0' }}">
        {{ $settings['prefix'] ?? '' }}<span data-counter-number>{{ $display }}</span>{{ $settings['suffix'] ?? '' }}
    </span>
    @if (! empty($settings['title']))
        <span class="counter-title">{{ $settings['title'] }}</span>
    @endif
</div>
