@php $tr = $context->locale === 'tr'; @endphp

<div class="tool" data-tension-tool>
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
                    <select name="size"><option value="M48" selected>M48</option></select>
                </label>
                <label class="tool-field">
                    <span>{{ $tr ? 'Kalite sınıfı' : 'Property class' }}</span>
                    <select name="grade"><option value="10.9" selected>10.9</option></select>
                </label>
            </div>
            <label class="tool-field">
                <span>{{ $tr ? 'Kalıcı ön yük (akmanın %)' : 'Residual preload (% of yield)' }} <output data-out="pct">50</output></span>
                <input type="range" name="pct" min="20" max="80" step="1" value="50">
            </label>
            <div class="tool-pair">
                <label class="tool-field">
                    <span>{{ $tr ? 'Sıkma boyu (mm)' : 'Grip length (mm)' }}</span>
                    <input type="number" name="grip" min="1" step="1" value="200" inputmode="numeric">
                </label>
                <label class="tool-field">
                    <span>{{ $tr ? 'Cıvata adedi' : 'Number of bolts' }}</span>
                    <input type="number" name="bolts" min="1" step="1" value="16" inputmode="numeric">
                </label>
            </div>
            <fieldset class="tool-field">
                <legend>{{ $tr ? 'Aynı anda gerilen cıvata' : 'Bolts tensioned at once' }}</legend>
                <div class="tool-seg">
                    <label><input type="radio" name="coverage" value="100" checked><span>{{ $tr ? '%100 — hepsi' : '100% — all' }}</span></label>
                    <label><input type="radio" name="coverage" value="50"><span>{{ $tr ? '%50 — iki tur' : '50% — two passes' }}</span></label>
                </div>
            </fieldset>
            <label class="tool-field">
                <span>{{ $tr ? 'Alet hidrolik alanı (mm², isteğe bağlı)' : 'Tool pressure area (mm², optional)' }}</span>
                <input type="number" name="area" min="0" step="any" placeholder="{{ $tr ? 'Katalog dışı alet için' : 'For a tool not in the catalogue' }}" inputmode="decimal">
            </label>
        </form>

        <div class="tool-result" aria-live="polite">
            <div class="tool-big">
                <span data-out="bigLabel">{{ $tr ? 'Pompa basıncı' : 'Pump pressure' }}</span>
                <b data-out="pressure">—</b>
                <small data-out="psi"></small>
                <small class="tool-method" data-out="passA"></small>
            </div>
            <dl class="tool-facts">
                <div><dt>{{ $tr ? 'Kalıcı yük' : 'Residual load' }}</dt><dd data-out="residual">—</dd></div>
                <div><dt>LTF</dt><dd data-out="ltf">—</dd></div>
                <div><dt>{{ $tr ? 'Germe yükü' : 'Tensioning load' }}</dt><dd data-out="applied">—</dd></div>
                <div><dt>{{ $tr ? 'Germe anında akma' : 'Yield at tensioning' }}</dt><dd data-out="ratio">—</dd></div>
                <div><dt>{{ $tr ? 'Gerilme kesiti' : 'Stress area' }}</dt><dd data-out="stressArea">—</dd></div>
                <div><dt>{{ $tr ? 'Gereken alet' : 'Tools needed' }}</dt><dd data-out="tools">—</dd></div>
            </dl>
            <p class="tool-warn" data-out="warn" hidden></p>

            <h3 class="tool-h">{{ $tr ? 'Uygun germe aletleri' : 'Suitable tensioners' }}</h3>
            <ul class="tool-list" data-out="matches"></ul>
            <p class="tool-empty" data-out="none" hidden>
                {{ $tr ? 'Katalogda bu cıvata ve yüke uygun germe aleti yok. Alet alanını biliyorsanız yukarıya girin ya da uygulama formuyla bize ulaşın.' : 'No catalogue tensioner fits this bolt and load. Enter the tool area above if you know it, or send us the application form.' }}
            </p>

            @if (filled($settings['request_url'] ?? null))
                <a class="xb xb--o tool-add" href="{{ $settings['request_url'] }}">{{ $tr ? 'Uygulama talep formunu doldur' : 'Fill in the application form' }} <x-icon name="arrow-right" /></a>
            @endif
        </div>
    </div>

    @if (filled($settings['note'] ?? null))<p class="tool-note">{{ $settings['note'] }}</p>@endif

    <script type="application/json" data-tool-data>{!! json_encode([
        'tensioners' => $tensioners,
        'addUrl' => route('quote.add'),
        'locale' => $context->locale,
        'text' => [
            'pressure' => $tr ? 'Pompa basıncı' : 'Pump pressure',
            'pressureB' => $tr ? 'Son tur basıncı (B)' : 'Final pass pressure (B)',
            'passA' => $tr ? 'İlk tur (A): :a bar — B × 1,25' : 'First pass (A): :a bar — B × 1.25',
            'manual' => $tr ? 'Girilen alet alanıyla (:area mm²)' : 'With the entered tool area (:area mm²)',
            'withTool' => $tr ? ':code ile' : 'With :code',
            'usage' => $tr ? 'kapasite' : 'of capacity',
            'bolt' => $tr ? 'Cıvata' : 'Bolt',
            'add' => $tr ? 'Teklife ekle' : 'Add to quote',
            'tools' => $tr ? ':n adet' : ':n',
            'ltfMin' => $tr ? 'Hesaplanan LTF 1,10\'un altında kaldığı için 1,10 kullanıldı.' : 'The calculated LTF was below 1.10, so 1.10 was used.',
            'yield' => $tr ? 'Germe anında gerilme akmanın %95\'ini aşıyor (%:r). Sıkma boyunu artırın veya hedef ön yükü düşürün.' : 'Stress during tensioning exceeds 95% of yield (:r%). Increase the grip length or lower the target preload.',
            'short' => $tr ? 'Sıkma boyu çapın 3 katından kısa; yük kaybı yüksek olur. Bu bağlantıda tork yöntemi daha verimli olabilir.' : 'Grip length is under 3 × diameter, so load loss is high. Torque tightening may suit this joint better.',
            'over' => $tr ? ':code için :p bar gerekiyor; aletin sınırı :max bar.' : ':code needs :p bar; its limit is :max bar.',
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
</div>
