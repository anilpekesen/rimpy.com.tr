@php $phone = ($settings['phone'] ?? null) ?: \App\Models\Setting::get('contact.phone'); @endphp

<div class="cb">
    <h2 class="cb-title">{!! nl2br(e($settings['title'] ?? '')) !!}</h2>
    <div class="cb-side">
        @if (filled($settings['text'] ?? null))<p class="cb-text">{{ $settings['text'] }}</p>@endif
        <button type="button" class="xb xb--k" data-quote-open>{{ $settings['button_text'] ?? '' }} <x-icon name="arrow-right" /></button>
        @if ($phone)
            <a class="cb-phone" href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}">{{ $phone }}</a>
        @endif
    </div>
</div>
