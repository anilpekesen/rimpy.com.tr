@props(['code' => null])

{{-- Görseli henüz yüklenmemiş ürün: boş kutu yerine marka altıgeni ve
     model kodu. Görsel eklenince kendiliğinden kaybolur. --}}
<span {{ $attributes->class('ph') }} aria-hidden="true">
    <svg viewBox="0 0 120 120" class="ph-hex">
        <polygon points="60,8 105,34 105,86 60,112 15,86 15,34" fill="none" stroke="currentColor" stroke-width="1.5"/>
        <polygon points="60,26 89,43 89,77 60,94 31,77 31,43" fill="none" stroke="currentColor" stroke-width="1" opacity=".5"/>
        <circle cx="60" cy="60" r="12" fill="none" stroke="var(--c-primary)" stroke-width="1.5"/>
    </svg>
    @if ($code)<span class="ph-code">{{ $code }}</span>@endif
</span>
