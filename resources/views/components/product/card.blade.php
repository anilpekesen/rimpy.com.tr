@props(['item', 'locale'])

@php
    $prefix = \App\Support\Urls::prefix($locale);
    $url = $prefix.$item->path($locale);
    $image = $item->featuredImage;
    $code = $item->custom('model_kodu', $locale);

    // Kartta en ayırt edici dört değer. Her ürün türünde farklı alanlar
    // dolu olduğu için sırayla bakılır, boşlar atlanır.
    $specs = \App\Support\ProductSpecs::highlights($item, $locale, 4);
@endphp

<article {{ $attributes->class('pc') }}>
    <a class="pc-media" href="{{ $url }}" tabindex="-1" aria-hidden="true">
        @if ($image)
            <img src="{{ $image->url('medium') }}" alt="" loading="lazy" decoding="async"
                 width="{{ $image->width }}" height="{{ $image->height }}">
        @else
            <x-product.placeholder :code="$code" />
        @endif
    </a>
    <div class="pc-body">
        <h3 class="pc-title"><a href="{{ $url }}">{{ $item->t('title', $locale) }}</a></h3>

        @if ($specs)
            <dl class="pc-specs">
                @foreach ($specs as $spec)
                    <div><dt>{{ $spec['label'] }}</dt><dd>{{ $spec['value'] }}</dd></div>
                @endforeach
            </dl>
        @endif
    </div>
</article>
