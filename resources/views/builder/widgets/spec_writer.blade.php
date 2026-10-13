@php
    $tr = $context->locale === 'tr';
    $first = reset($spec['equipment']);
@endphp

<div class="tool" data-spec-tool>
    @include('builder.widgets.partials.section-head')

    <div class="tool-grid">
        <form class="tool-form" data-tool-form onsubmit="return false">
            <label class="tool-field">
                <span>{{ $tr ? 'Ekipman' : 'Equipment' }}</span>
                <select name="cat">
                    @foreach ($spec['equipment'] as $key => $equipment)
                        <option value="{{ $key }}">{{ $equipment['label'] }}</option>
                    @endforeach
                </select>
            </label>
            <div class="tool-pair">
                <label class="tool-field">
                    <span data-out="capLabel">{{ $first['cap'][0] }}</span>
                    <input type="number" name="cap" min="0" step="any" value="{{ $first['cap'][1] }}" inputmode="decimal">
                </label>
                <label class="tool-field">
                    <span>{{ $tr ? 'Adet' : 'Quantity' }}</span>
                    <input type="number" name="qty" min="1" step="1" value="2" inputmode="numeric">
                </label>
            </div>
            <label class="tool-field">
                <span data-out="extLabel">{{ $first['ext'][0] }}</span>
                <input type="text" name="ext" value="{{ $first['ext'][1] }}">
            </label>
            <label class="tool-field">
                <span>{{ $tr ? 'Tahrik / enerji' : 'Drive / power' }}</span>
                <select name="drive">
                    @foreach ($spec['drives'] as $key => [$label])
                        <option value="{{ $key }}" @selected($key === $first['drive'])>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <div class="tool-checks">
                <label class="tool-check"><input type="checkbox" name="atex"><span>{{ $tr ? 'Patlayıcı ortam (ATEX) uygunluğu' : 'Explosive atmosphere (ATEX) compliance' }}</span></label>
                <label class="tool-check"><input type="checkbox" name="cal" checked><span>{{ $tr ? 'Akredite laboratuvar kalibrasyon sertifikası' : 'Accredited laboratory calibration certificate' }}</span></label>
                <label class="tool-check"><input type="checkbox" name="training" checked><span>{{ $tr ? 'Kullanıcı eğitimi' : 'Operator training' }}</span></label>
                <label class="tool-check"><input type="checkbox" name="case" checked><span>{{ $tr ? 'Taşıma çantası / sandık' : 'Carry case / crate' }}</span></label>
            </div>
            <div class="tool-pair">
                <label class="tool-field">
                    <span>{{ $tr ? 'Garanti (ay)' : 'Warranty (months)' }}</span>
                    <input type="number" name="war" min="6" step="6" value="24" inputmode="numeric">
                </label>
                <label class="tool-field">
                    <span>{{ $tr ? 'Teslim (gün)' : 'Delivery (days)' }}</span>
                    <input type="number" name="del" min="1" step="1" value="45" inputmode="numeric">
                </label>
            </div>
        </form>

        <div class="tool-result spec-out" data-print-target>
            <h3 class="spec-title" data-out="title"></h3>
            <div class="spec-body" data-out="spec"></div>
            <div class="spec-actions">
                <button type="button" class="xb xb--y" data-act="copy">{{ $tr ? 'Metni kopyala' : 'Copy text' }}</button>
                <button type="button" class="xb xb--o" data-act="print">{{ $tr ? 'Yazdır / PDF' : 'Print / PDF' }}</button>
            </div>
        </div>
    </div>

    @if (filled($settings['note'] ?? null))<p class="tool-note">{{ $settings['note'] }}</p>@endif

    <script type="application/json" data-tool-data>{!! json_encode([
        'spec' => $spec,
        'text' => [
            'copied' => $tr ? 'Kopyalandı' : 'Copied',
            'selected' => $tr ? 'Metin seçildi, Ctrl+C ile kopyalayın' : 'Text selected, press Ctrl+C to copy',
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
</div>
