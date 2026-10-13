@php
    $tr = $context->locale === 'tr';
    // Aynı sayfada iki dönüştürücü olursa sekme kimlikleri çakışmasın.
    $uid = 'uc'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(6));
@endphp

<div class="tool" data-unit-converter>
    @include('builder.widgets.partials.section-head')

    <div class="tool-tabs" role="tablist" aria-label="{{ $tr ? 'Birim grubu' : 'Unit group' }}">
        @foreach ($groups as $key => $group)
            <button type="button" role="tab" id="{{ $uid }}-tab-{{ $key }}" aria-controls="{{ $uid }}-panel-{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" data-uc-tab="{{ $key }}">{{ $group['label'] }}</button>
        @endforeach
    </div>

    @foreach ($groups as $key => $group)
        <form class="tool-form uc-panel" role="tabpanel" id="{{ $uid }}-panel-{{ $key }}" aria-labelledby="{{ $uid }}-tab-{{ $key }}" data-uc-panel="{{ $key }}" onsubmit="return false" @if (! $loop->first) hidden @endif>
            <div class="uc-grid">
                @foreach ($group['units'] as $unit => $factor)
                    <label class="tool-field">
                        <span>{{ $unit }}</span>
                        <input type="text" inputmode="decimal" autocomplete="off" data-factor="{{ $factor }}" @if ($loop->first) value="{{ $tr ? str_replace('.', ',', (string) $group['start']) : $group['start'] }}" @endif>
                    </label>
                @endforeach
            </div>
            <p class="uc-note">{{ $group['note'] }}</p>
        </form>
    @endforeach

    @if (filled($settings['note'] ?? null))<p class="tool-note">{{ $settings['note'] }}</p>@endif
</div>
