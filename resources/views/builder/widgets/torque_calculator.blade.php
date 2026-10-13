@php
    $tr = $context->locale === 'tr';
    $sizes = ['M12','M14','M16','M18','M20','M22','M24','M27','M30','M33','M36','M39','M42','M45','M48','M52','M56','M60','M64','M68','M72','M76','M80','M90','M100'];
@endphp

<div class="tool" data-torque-calc data-headroom="{{ (float) ($settings['headroom'] ?? 90) / 100 }}">
    @include('builder.widgets.partials.section-head')

    <div class="tool-grid">
        <form class="tool-form" data-tool-form onsubmit="return false">
            <fieldset class="tool-field">
                <legend>{{ $tr ? 'Diş sistemi' : 'Thread system' }}</legend>
                <div class="tool-seg">
                    <label><input type="radio" name="sys" value="M" checked><span>{{ $tr ? 'Metrik' : 'Metric' }}</span></label>
                    <label><input type="radio" name="sys" value="I"><span>{{ $tr ? 'İnç (UNC / 8UN)' : 'Inch (UNC / 8UN)' }}</span></label>
                </div>
            </fieldset>
            <div class="tool-pair">
                <label class="tool-field">
                    <span>{{ $tr ? 'Cıvata ölçüsü' : 'Bolt size' }}</span>
                    <select name="size">
                        @foreach ($sizes as $size)
                            <option value="{{ $size }}" @selected($size === 'M48')>{{ $size }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="tool-field">
                    <span>{{ $tr ? 'Kalite sınıfı' : 'Property class' }}</span>
                    <select name="grade">
                        @foreach (['4.6', '5.6', '8.8', '10.9', '12.9'] as $grade)
                            <option value="{{ $grade }}" @selected($grade === '10.9')>{{ $grade }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <fieldset class="tool-field">
                <legend>{{ $tr ? 'Hesap yöntemi' : 'Method' }}</legend>
                <div class="tool-seg">
                    <label><input type="radio" name="method" value="k" checked><span>{{ $tr ? 'Basit (K katsayısı)' : 'Simple (nut factor K)' }}</span></label>
                    <label><input type="radio" name="method" value="vdi"><span>{{ $tr ? 'VDI 2230 (μ ayrı)' : 'VDI 2230 (split μ)' }}</span></label>
                </div>
            </fieldset>
            <label class="tool-field" data-method="k">
                <span>{{ $tr ? 'Yağlama (sürtünme katsayısı K)' : 'Lubrication (nut factor K)' }}</span>
                <select name="k">
                    <option value="0.10">{{ $tr ? 'MoS₂ / bakır pasta — 0,10' : 'MoS₂ / copper paste — 0.10' }}</option>
                    <option value="0.14" selected>{{ $tr ? 'Hafif yağlı — 0,14' : 'Lightly oiled — 0.14' }}</option>
                    <option value="0.18">{{ $tr ? 'Galvaniz, kuru — 0,18' : 'Galvanised, dry — 0.18' }}</option>
                    <option value="0.20">{{ $tr ? 'Kuru, yağsız — 0,20' : 'Dry, unlubricated — 0.20' }}</option>
                </select>
            </label>
            @php
                $frictions = [
                    '0.08' => $tr ? 'MoS₂ pasta — 0,08' : 'MoS₂ paste — 0.08',
                    '0.10' => $tr ? 'Bakır / nikel pasta — 0,10' : 'Copper / nickel paste — 0.10',
                    '0.12' => $tr ? 'Yağlı, fosfatlı — 0,12' : 'Oiled, phosphated — 0.12',
                    '0.14' => $tr ? 'Hafif yağlı — 0,14' : 'Lightly oiled — 0.14',
                    '0.16' => $tr ? 'Galvaniz, yağlı — 0,16' : 'Galvanised, oiled — 0.16',
                    '0.20' => $tr ? 'Kuru — 0,20' : 'Dry — 0.20',
                ];
            @endphp
            <div class="tool-pair" data-method="vdi" hidden>
                <label class="tool-field">
                    <span>{{ $tr ? 'Diş sürtünmesi μG' : 'Thread friction μG' }}</span>
                    <select name="mu_thread">
                        @foreach ($frictions as $value => $label)<option value="{{ $value }}" @selected($value === '0.14')>{{ $label }}</option>@endforeach
                    </select>
                </label>
                <label class="tool-field">
                    <span>{{ $tr ? 'Baş / somun altı μK' : 'Under-head friction μK' }}</span>
                    <select name="mu_head">
                        @foreach ($frictions as $value => $label)<option value="{{ $value }}" @selected($value === '0.14')>{{ $label }}</option>@endforeach
                    </select>
                </label>
            </div>
            <label class="tool-field">
                <span><span data-out="pctLabel">{{ $tr ? 'Ön yük (akma dayanımının %)' : 'Preload (% of yield)' }}</span> <output data-out="pct">75</output></span>
                <input type="range" name="pct" min="50" max="90" step="5" value="75">
            </label>
        </form>

        <div class="tool-result" aria-live="polite">
            <div class="tool-big">
                <span>{{ $tr ? 'Gerekli tork' : 'Required torque' }}</span>
                <b data-out="torque">—</b>
                <small data-out="ftlb"></small>
                <small class="tool-method" data-out="method"></small>
            </div>
            <dl class="tool-facts">
                <div><dt>{{ $tr ? 'Ön yük' : 'Preload' }}</dt><dd data-out="force">—</dd></div>
                <div><dt>{{ $tr ? 'Gerilme kesiti' : 'Stress area' }}</dt><dd data-out="area">—</dd></div>
                <div><dt>{{ $tr ? 'Somun anahtar ağzı' : 'Nut across flats' }}</dt><dd data-out="af">—</dd></div>
            </dl>

            <h3 class="tool-h">{{ $tr ? 'Bu torku verebilen anahtarlar' : 'Wrenches for this torque' }}</h3>
            <ul class="tool-list" data-out="matches"></ul>
            <p class="tool-empty" data-out="none" hidden>
                {{ $tr ? 'Katalogdaki anahtarlar bu torka uygun değil. Mühendisimiz size özel çözüm önersin.' : 'No catalogue wrench fits this torque. Ask our engineers for a custom solution.' }}
            </p>
        </div>
    </div>

    @if (filled($settings['note'] ?? null))<p class="tool-note">{{ $settings['note'] }}</p>@endif

    <script type="application/json" data-tool-data>{!! json_encode([
        'wrenches' => $wrenches,
        'sockets' => $sockets,
        'addUrl' => route('quote.add'),
        'locale' => $context->locale,
        'text' => [
            'pressure' => $tr ? 'Pompa basıncı' : 'Pump pressure',
            'usage' => $tr ? 'kapasite' : 'of capacity',
            'electric' => $tr ? 'Elektrikli — pompa gerekmez' : 'Electric — no pump needed',
            'socket' => $tr ? 'Lokma' : 'Socket',
            'cassette' => $tr ? 'Kaset uyumlu' : 'Cassette fits',
            'add' => $tr ? 'Teklife ekle' : 'Add to quote',
            'pctK' => $tr ? 'Ön yük (akma dayanımının %)' : 'Preload (% of yield)',
            'pctVdi' => $tr ? 'Eşdeğer gerilme sınırı (akmanın %)' : 'Equivalent stress limit (% of yield)',
            'vdiNote' => $tr ? 'VDI 2230 · μG :g / μK :k · burulma payı düşüldü' : 'VDI 2230 · μG :g / μK :k · torsion allowed for',
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
</div>
