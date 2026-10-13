@props(['item', 'locale', 'compact' => false])

{{-- Betik çalışmazsa normal bir form gönderimi olur ve ziyaretçi aynı
     sayfaya döner; çalışırsa istek arka planda gider ve sepet açılmadan
     sayaç güncellenir. --}}
<form method="POST" action="{{ route('quote.add') }}" data-quote-add data-quote-title="{{ $item->t('title', $locale) }}"
      {{ $attributes->class(['qa', 'qa--compact' => $compact]) }}>
    @csrf
    <input type="hidden" name="item" value="{{ $item->id }}">
    <input type="hidden" name="locale" value="{{ $locale }}">
    <button type="submit" class="qa-btn" data-quote-item="{{ $item->id }}">
        <x-icon name="plus" class="qa-icon" />
        <span data-quote-label>{{ \App\Models\Translation::get('quote_add', 'Teklif sepetine ekle', $locale) }}</span>
    </button>
</form>
