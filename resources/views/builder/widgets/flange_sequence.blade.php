@php $tr = $context->locale === 'tr'; @endphp

<div class="tool" data-flange-tool>
    @include('builder.widgets.partials.section-head')

    <div class="tool-grid">
        <form class="tool-form" data-tool-form onsubmit="return false">
            <fieldset class="tool-field">
                <legend>{{ $tr ? 'Flanş' : 'Flange' }}</legend>
                <div class="tool-seg">
                    <label><input type="radio" name="std" value="" checked><span>{{ $tr ? 'Elle gir' : 'Manual' }}</span></label>
                    <label><input type="radio" name="std" value="b165"><span>ASME B16.5</span></label>
                </div>
            </fieldset>
            <div class="tool-pair" data-std hidden>
                <label class="tool-field">
                    <span>{{ $tr ? 'Boru ölçüsü (NPS)' : 'Pipe size (NPS)' }}</span>
                    <select name="nps">
                        @foreach (array_keys($flanges['150']) as $nps)<option value="{{ $nps }}" @selected((string) $nps === '12')>{{ $nps }}"</option>@endforeach
                    </select>
                </label>
                <label class="tool-field">
                    <span>{{ $tr ? 'Basınç sınıfı' : 'Pressure class' }}</span>
                    <select name="class">
                        @foreach (array_keys($flanges) as $class)<option value="{{ $class }}" @selected((string) $class === '300')>{{ $class }}</option>@endforeach
                    </select>
                </label>
            </div>
            <p class="tool-warn" data-out="flange" hidden></p>
            <label class="tool-field">
                <span>{{ $tr ? 'Cıvata sayısı (4\'ün katı)' : 'Number of bolts (multiple of 4)' }}</span>
                <input type="number" name="n" min="4" max="96" step="4" value="{{ (int) ($settings['bolts'] ?? 16) }}" inputmode="numeric">
            </label>
            <label class="tool-field">
                <span>{{ $tr ? 'Hedef tork' : 'Target torque' }}</span>
                <input type="number" name="torque" min="1" step="any" value="{{ (float) ($settings['torque'] ?? 450) }}" inputmode="decimal">
            </label>
            <fieldset class="tool-field">
                <legend>{{ $tr ? 'Birim' : 'Unit' }}</legend>
                <div class="tool-seg">
                    <label><input type="radio" name="unit" value="N·m" checked><span>N·m</span></label>
                    <label><input type="radio" name="unit" value="ft·lb"><span>ft·lb</span></label>
                </div>
            </fieldset>
            <label class="tool-field">
                <span>{{ $tr ? 'Sıralama yöntemi' : 'Pattern' }}</span>
                <select name="pattern">
                    <option value="star">{{ $tr ? 'Çapraz / yıldız, tek anahtar' : 'Cross / star, single tool' }}</option>
                    <option value="quad">{{ $tr ? 'Çapraz, 4 anahtar aynı anda' : 'Cross, 4 tools at once' }}</option>
                </select>
            </label>
            <label class="tool-field">
                <span>{{ $tr ? 'Tur planı' : 'Pass plan' }}</span>
                <select name="passes">
                    <option value="30,60,100">{{ $tr ? '3 tur: %30 – %60 – %100' : '3 passes: 30% – 60% – 100%' }}</option>
                    <option value="20,50,80,100" selected>{{ $tr ? '4 tur: %20 – %50 – %80 – %100' : '4 passes: 20% – 50% – 80% – 100%' }}</option>
                    <option value="50,100">{{ $tr ? '2 tur: %50 – %100' : '2 passes: 50% – 100%' }}</option>
                </select>
            </label>
            <label class="tool-check">
                <input type="checkbox" name="check" checked>
                <span>{{ $tr ? 'Son kontrol turu ekle (%100, saat yönünde)' : 'Add a final check pass (100%, clockwise)' }}</span>
            </label>
            <p class="tool-warn" data-out="error" role="alert" hidden></p>
        </form>

        <div class="tool-result" aria-live="polite">
            <div class="fl-head">
                <div class="tool-big">
                    <span data-out="pass">—</span>
                    <b data-out="torque">—</b>
                    <small data-out="step"></small>
                </div>
                <div class="tool-tabs tool-tabs--sm" data-out="tabs" role="group" aria-label="{{ $tr ? 'Turlar' : 'Passes' }}"></div>
            </div>

            <svg class="fl-svg" viewBox="0 0 400 400" role="img" aria-label="{{ $tr ? 'Flanş cıvata sıralaması çizimi' : 'Flange bolt sequence drawing' }}" data-out="svg"></svg>

            <div class="fl-ctrl">
                <button type="button" class="xb xb--o" data-act="prev">{{ $tr ? 'Geri' : 'Back' }}</button>
                <button type="button" class="xb xb--y" data-act="play">{{ $tr ? 'Oynat' : 'Play' }}</button>
                <button type="button" class="xb xb--o" data-act="next">{{ $tr ? 'İleri' : 'Next' }}</button>
                <button type="button" class="xb xb--o" data-act="reset">{{ $tr ? 'Başa dön' : 'Reset' }}</button>
            </div>

            <h3 class="tool-h">{{ $tr ? 'Sıkma sırası' : 'Tightening order' }}</h3>
            <ol class="fl-seq" data-out="seq"></ol>

            <h3 class="tool-h">{{ $tr ? 'Tur planı' : 'Pass plan' }}</h3>
            <div class="tool-table"><table data-out="table"></table></div>
        </div>
    </div>

    @if (filled($settings['note'] ?? null))<p class="tool-note">{{ $settings['note'] }}</p>@endif

    <script type="application/json" data-tool-data>{!! json_encode([
        'flanges' => $flanges,
        'text' => [
            'pass' => $tr ? 'Tur' : 'Pass',
            'check' => $tr ? 'Kontrol' : 'Check',
            'step' => $tr ? 'Adım :n / :total' : 'Step :n of :total',
            'done' => $tr ? 'Tur tamamlandı' : 'Pass complete',
            'tightened' => $tr ? 'sıkılan cıvata' : 'bolts tightened',
            'play' => $tr ? 'Oynat' : 'Play',
            'pause' => $tr ? 'Durdur' : 'Pause',
            'cols' => $tr ? ['Tur', 'Oran', 'Tork', 'Sıra'] : ['Pass', 'Share', 'Torque', 'Order'],
            'star' => $tr ? 'Çapraz (yıldız)' : 'Cross (star)',
            'quad' => $tr ? 'Çapraz, 4\'lü gruplar' : 'Cross, groups of 4',
            'cw' => $tr ? 'Saat yönünde, tek tek' : 'Clockwise, one by one',
            'errBolts' => $tr ? 'Cıvata sayısı 4 ile 96 arasında ve 4\'ün katı olmalı.' : 'Number of bolts must be a multiple of 4 between 4 and 96.',
            'flange' => $tr ? 'NPS :nps" sınıf :class: :n adet :d cıvata. Hedef torku bu cıvataya göre tork hesaplayıcıdan alın.' : 'NPS :nps" class :class: :n bolts, :d. Take the target torque for this bolt from the torque calculator.',
            'noFlange' => $tr ? 'ASME B16.5\'te NPS :nps" için sınıf :class flanşı yok.' : 'ASME B16.5 has no class :class flange for NPS :nps".',
            'errTorque' => $tr ? 'Hedef tork sıfırdan büyük olmalı.' : 'Target torque must be greater than zero.',
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
</div>
