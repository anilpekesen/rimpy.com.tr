@php
    // Kurucuda kullanılabilen ikonlar. Dışarıdan bir ikon paketi yüklenmediği
    // için liste bilinçli olarak dar ve kurumsal sitelerin ihtiyacına göre seçili.
    $icons = [
        '' => 'Yok',
        'check' => 'Onay', 'check-circle' => 'Onay (daire)', 'star' => 'Yıldız',
        'heart' => 'Kalp', 'shield' => 'Kalkan', 'award' => 'Ödül',
        'phone' => 'Telefon', 'mail' => 'Zarf', 'map-pin' => 'Konum',
        'clock' => 'Saat', 'truck' => 'Kamyon', 'package' => 'Paket',
        'factory' => 'Fabrika', 'leaf' => 'Yaprak', 'users' => 'Ekip',
        'trending-up' => 'Artış', 'target' => 'Hedef', 'settings' => 'Ayar',
        'arrow-right' => 'Sağ ok', 'download' => 'İndir', 'play' => 'Oynat',
    ];
@endphp

<select id="{{ $id }}" class="field" wire:key="{{ $selected['id'] }}-{{ $key }}-icon"
        x-on:change="$wire.setSetting('{{ $key }}', $event.target.value, false)">
    @foreach ($icons as $iconKey => $iconLabel)
        <option value="{{ $iconKey }}" @selected($value === $iconKey)>{{ $iconLabel }}</option>
    @endforeach
</select>
