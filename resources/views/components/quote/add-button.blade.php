@props(['item', 'locale', 'compact' => false])

@php
    // Seçenekli üründe (lokma ölçüleri gibi) ziyaretçi bir ya da birkaç
    // seçenek işaretler; her biri sepette ayrı satır olur.
    $options = \App\Services\QuoteCart::options($item, $locale);
@endphp

{{-- Betik çalışmazsa normal bir form gönderimi olur ve ziyaretçi aynı
     sayfaya döner; çalışırsa istek arka planda gider ve sepet açılmadan
     sayaç güncellenir. --}}
<form method="POST" action="{{ route('quote.add') }}" data-quote-add data-quote-title="{{ $item->t('title', $locale) }}"
      {{ $attributes->class(['qa', 'qa--compact' => $compact, 'qa--options' => $options]) }}>
    @csrf
    <input type="hidden" name="item" value="{{ $item->id }}">
    <input type="hidden" name="locale" value="{{ $locale }}">
    @if ($options)
        <fieldset class="qa-options" data-quote-options>
            <legend>{{ \App\Models\Translation::get('quote_choose', 'Model / ölçü seçin', $locale) }}</legend>
            @foreach ($options as $option)
                <label class="qa-option">
                    <input type="checkbox" name="options[]" value="{{ $option }}">
                    <span>{{ $option }}</span>
                </label>
            @endforeach
        </fieldset>
    @endif
    <button type="submit" class="qa-btn" data-quote-item="{{ $item->id }}">
        <x-icon name="plus" class="qa-icon" />
        <span data-quote-label>{{ \App\Models\Translation::get('quote_add', 'Teklif sepetine ekle', $locale) }}</span>
    </button>
</form>
