@if (! empty($settings['embed']))
    <div class="map @if ($settings['grayscale'] ?? false) map--gray @endif">
        <iframe src="{{ $settings['embed'] }}" title="Konum haritası"
                loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
    </div>
@elseif ($context->editing)
    <div class="map flex items-center justify-center bg-ink-100 text-sm text-ink-400" style="min-height:200px">
        Adres girilmedi.
    </div>
@endif
