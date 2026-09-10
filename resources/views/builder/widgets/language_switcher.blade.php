@php
    $languages = \App\Support\Locales::all();
    $display = $settings['display'] ?? 'code';
@endphp

@if ($languages->count() > 1)
    <div class="lang lang--{{ $settings['style'] ?? 'inline' }}">
        @foreach ($languages as $language)
            @php
                // Ziyaretçi bulunduğu sayfanın diğer dildeki karşılığına gider;
                // karşılığı yoksa o dilin ana sayfasına düşer.
                $target = \App\Support\Urls::translate(request()->path(), $context->locale, $language->code);
            @endphp
            <a class="lang-link @if ($language->code === $context->locale) is-active @endif"
               href="{{ $target }}" hreflang="{{ $language->code }}"
               @if ($language->code === $context->locale) aria-current="true" @endif>
                {{ $display === 'name' ? $language->native_name : strtoupper($language->code) }}
            </a>
        @endforeach
    </div>
@endif
