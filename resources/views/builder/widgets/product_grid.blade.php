<div class="pg pg--{{ $settings['tone'] ?? 'light' }}" data-product-grid>
    @include('builder.widgets.partials.section-head')

    @if ($filters->isNotEmpty())
        <div class="pa-chips" role="toolbar">
            <button type="button" class="pa-chip is-active" data-filter="">{{ \App\Models\Translation::get('all', 'Tümü', $context->locale) }}</button>
            @foreach ($filters as $filter)
                <button type="button" class="pa-chip" data-filter="{{ $filter->id }}">{{ $filter->t('name', $context->locale) }}</button>
            @endforeach
        </div>
    @endif

    <div class="pc-grid pc-grid--4">
        @foreach ($items as $item)
            <x-product.card :item="$item" :locale="$context->locale" data-terms="{{ $item->terms->pluck('id')->implode(',') }}" />
        @endforeach
    </div>

    @if (filled($settings['button_text'] ?? null))
        <div class="pg-foot"><a class="xb xb--o" href="{{ $archive }}">{{ $settings['button_text'] }} <x-icon name="arrow-right" /></a></div>
    @endif
</div>
