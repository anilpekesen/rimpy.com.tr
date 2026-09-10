@php $items = $settings['items'] ?? []; @endphp

<div class="acc" @if ($settings['single'] ?? true) data-accordion-single @endif>
    @foreach ($items as $index => $item)
        <details class="acc-item" @if ($item['open'] ?? false) open @endif
                 @if ($settings['single'] ?? true) name="acc-{{ $element->id }}" @endif>
            <summary class="acc-head">
                <span class="acc-title">{{ $item['title'] ?? '' }}</span>
                <span class="acc-icon"><x-builder.icon name="chevron-down"/></span>
            </summary>
            <div class="acc-body tx">{!! \App\Support\Html::sanitize($item['body'] ?? '') !!}</div>
        </details>
    @endforeach
</div>

@if (($settings['schema'] ?? true) && $items && ! $context->editing)
    {{-- Arama motorlarının soruları doğrudan sonuçlarda gösterebilmesi için --}}
    <script type="application/ld+json">@json([
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($items)->map(fn ($i) => [
            '@type' => 'Question',
            'name' => strip_tags((string) ($i['title'] ?? '')),
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags((string) ($i['body'] ?? ''))],
        ])->values(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>
@endif
