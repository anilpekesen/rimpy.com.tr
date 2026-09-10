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

@if (($settings['schema'] ?? true) && $items && ! $context->editing && $context->claimOnce('faq'))
    @php
        /**
         * Arama motorlarının soruları doğrudan sonuçlarda gösterebilmesi için.
         *
         * Yapı burada kuruluyor, şablonda değil: Schema.org anahtarları "@"
         * ile başladığı için Blade onları kendi yönergesi sanıyor ve
         * şablon içinde yazıldıklarında derleme hatası veriyor.
         */
        $faqSchema = \App\Support\Schema::faq($items);
    @endphp

    <script type="application/ld+json">{!! \App\Support\Schema::encode($faqSchema) !!}</script>
@endif
