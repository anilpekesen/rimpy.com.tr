<dl class="sb">
    @foreach ($settings['items'] ?? [] as $item)
        <div class="sb-item">
            <dd class="sb-value"><span data-counter="{{ (int) ($item['value'] ?? 0) }}" data-counter-separator="1" data-counter-duration="1600"><span data-counter-number>{{ number_format((int) ($item['value'] ?? 0), 0, ',', '.') }}</span></span><sup>{{ $item['suffix'] ?? '' }}</sup></dd>
            <dt class="sb-label">{{ $item['label'] ?? '' }}</dt>
        </div>
    @endforeach
</dl>
