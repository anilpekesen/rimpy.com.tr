@php $tr = $context->locale === 'tr'; @endphp

<div class="tool" data-cylinder-tool>
    @include('builder.widgets.partials.section-head')

    <div class="tool-grid">
        <form class="tool-form" data-tool-form onsubmit="return false">
            <label class="tool-field">
                <span>{{ $tr ? 'Toplam yük (ton)' : 'Total load (t)' }}</span>
                <input type="number" name="load" min="1" max="5000" step="1" value="120" inputmode="numeric">
            </label>
            <label class="tool-field">
                <span>{{ $tr ? 'Kaldırma noktası (silindir sayısı)' : 'Lifting points (cylinders)' }} <output data-out="n">4</output></span>
                <input type="range" name="points" min="1" max="8" step="1" value="4">
            </label>
            <fieldset class="tool-field">
                <legend>{{ $tr ? 'Emniyet katsayısı' : 'Safety factor' }}</legend>
                <div class="tool-seg">
                    @foreach (['1.25' => '1,25', '1.5' => '1,5', '2' => '2,0'] as $value => $label)
                        <label><input type="radio" name="sf" value="{{ $value }}" @checked($value === '1.25')><span>{{ $label }}</span></label>
                    @endforeach
                </div>
            </fieldset>
            <label class="tool-field">
                <span>{{ $tr ? 'Gereken strok (mm)' : 'Required stroke (mm)' }}</span>
                <input type="number" name="stroke" min="1" max="2000" step="1" value="100" inputmode="numeric">
            </label>
            <fieldset class="tool-field">
                <legend>{{ $tr ? 'Geri dönüş' : 'Return' }}</legend>
                <div class="tool-seg">
                    <label><input type="radio" name="action" value="" checked><span>{{ $tr ? 'Fark etmez' : 'Any' }}</span></label>
                    <label><input type="radio" name="action" value="tek"><span>{{ $tr ? 'Yay (tek etkili)' : 'Spring' }}</span></label>
                    <label><input type="radio" name="action" value="çift"><span>{{ $tr ? 'Hidrolik (çift)' : 'Hydraulic' }}</span></label>
                </div>
            </fieldset>
        </form>

        <div class="tool-result" aria-live="polite">
            <div class="tool-big">
                <span>{{ $tr ? 'Silindir başına kapasite' : 'Capacity per cylinder' }}</span>
                <b data-out="need">—</b>
                <small data-out="formula"></small>
            </div>

            <h3 class="tool-h">{{ $tr ? 'Önerilen set' : 'Recommended kit' }}</h3>
            <ul class="tool-list" data-out="kit"></ul>
            <p class="tool-empty" data-out="none" hidden>
                {{ $tr ? 'Bu yük ve strok için katalogda uygun silindir yok. Özel strok veya tonaj için mühendisimize danışın.' : 'No catalogue cylinder fits. Ask our engineers about custom stroke or tonnage.' }}
            </p>
            <p class="tool-warn" data-out="warn" hidden></p>
            <button type="button" class="xb xb--y tool-add" data-out="add" hidden>{{ $tr ? 'Seti teklif sepetine ekle' : 'Add kit to quote' }} <x-icon name="arrow-right" /></button>
        </div>
    </div>

    @if (filled($settings['note'] ?? null))<p class="tool-note">{{ $settings['note'] }}</p>@endif

    <script type="application/json" data-tool-data>{!! json_encode([
        'cylinders' => $cylinders,
        'pumps' => $pumps,
        'hose' => $hose,
        'gauge' => $gauge,
        'addManyUrl' => route('quote.add-many'),
        'locale' => $context->locale,
        'text' => [
            'cylinder' => $tr ? 'Silindir' : 'Cylinder',
            'pump' => $tr ? 'Pompa' : 'Pump',
            'hose' => $tr ? 'Hortum' : 'Hose',
            'gauge' => $tr ? 'Manometre' : 'Gauge',
            'usage' => $tr ? 'kapasite kullanımı' : 'capacity used',
            'oil' => $tr ? 'gereken yağ' : 'oil required',
            'manifold' => $tr ? 'noktalı kaldırmada bir dağıtım manifoldu gerekir; teklife ekleyeceğiz.' : 'point lift needs a split-flow manifold; we will include it in the quote.',
            'double' => $tr ? 'Çift etkili silindirler için 4/3 valfli pompa gerekir.' : 'Double-acting cylinders need a pump with a 4/3 valve.',
            'noPump' => $tr ? 'Bu yağ hacmine uygun pompa katalogda yok; güç ünitesi önereceğiz.' : 'No catalogue pump has enough oil; we will propose a power unit.',
            'added' => $tr ? 'Set teklif sepetine eklendi' : 'Kit added to your quote',
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
</div>
