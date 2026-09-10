<?php

namespace App\Builder;

use App\Support\Theme;

/**
 * Eleman ayarlarını CSS'e çevirir.
 *
 * Her stil denetimi kendi CSS'ini nasıl üreteceğini kendi tanımında söyler:
 * hangi seçiciye, hangi özelliği yazacağını. Böylece yeni bir widget
 * eklerken CSS üretimi için ayrıca kod yazmak gerekmez — denetimi tanımlamak
 * yeterlidir.
 *
 * Üretilen CSS eleman kimliğine göre kapsanır (`.e-a1b2c3`), bu yüzden iki
 * widget birbirinin stilini asla ezmez. Sonuç sayfayla birlikte veritabanına
 * yazılır; ziyaretçi her istekte yeniden derleme maliyeti ödemez.
 */
class StyleCompiler
{
    /** Cihaz kırılımları: masaüstü kuralları koşulsuz, diğerleri medya sorgusu içinde. */
    private array $buckets = ['d' => [], 't' => [], 'm' => []];

    public function __construct(
        private WidgetRegistry $registry,
        private Theme $theme,
    ) {}

    /**
     * @param  string|null  $previewDevice  Düzenleyicide tablet/telefon önizlemesi
     *   açıkken 't' ya da 'm' gelir. Tuval gerçek bir telefon kadar dar
     *   olmadığı için medya sorguları tetiklenmez; bunun yerine o cihazın
     *   kuralları koşulsuz olarak yazılır ve önizleme gerçeğe uyar.
     */
    public function compile(Tree $tree, ?string $previewDevice = null): string
    {
        $this->buckets = ['d' => [], 't' => [], 'm' => []];

        foreach ($tree->flatten() as $element) {
            $this->compileElement($element);
        }

        if ($previewDevice && $previewDevice !== 'd') {
            return $this->flattenForDevice($previewDevice);
        }

        return $this->output();
    }

    /** Cihaz kurallarını masaüstünün üzerine bindirerek tek katman üretir. */
    private function flattenForDevice(string $device): string
    {
        $merged = $this->buckets['d'];

        // Telefon önizlemesinde tablet kuralları da geçerlidir; gerçek
        // tarayıcıda `max-width` sorguları böyle basamaklanır.
        $order = $device === 'm' ? ['t', 'm'] : ['t'];

        foreach ($order as $bucket) {
            foreach ($this->buckets[$bucket] as $selector => $declarations) {
                $merged[$selector] = array_merge($merged[$selector] ?? [], $declarations);
            }
        }

        return $this->rules($merged);
    }

    private function compileElement(Element $element): void
    {
        $wrapper = '.'.$element->cssClass();

        foreach ($this->controlsFor($element) as $key => $control) {
            if (empty($control['selector'])) {
                continue;
            }

            $value = $element->setting($key);

            if ($value === null || $value === '' || $value === []) {
                continue;
            }

            $selector = str_replace('{{WRAPPER}}', $wrapper, $control['selector']);

            if (! empty($control['responsive'])) {
                foreach (['d', 't', 'm'] as $device) {
                    $deviceValue = is_array($value) ? ($value[$device] ?? null) : ($device === 'd' ? $value : null);

                    if ($deviceValue === null || $deviceValue === '') {
                        continue;
                    }

                    $this->write($device, $selector, $this->declarations($control, $deviceValue));
                }

                continue;
            }

            $this->write('d', $selector, $this->declarations($control, $value));
        }
    }

    /** Bir elemana uygulanabilecek tüm stil denetimleri (widget'a özel + ortak). */
    private function controlsFor(Element $element): array
    {
        $controls = Controls::common($element->type);

        if ($element->isWidget() && $widget = $this->registry->find($element->widget)) {
            $controls = array_merge($controls, $widget->flatControls());
        } elseif ($element->type === 'section') {
            $controls = array_merge($controls, Controls::section());
        } elseif ($element->type === 'column') {
            $controls = array_merge($controls, Controls::column());
        }

        return array_filter($controls, fn ($c) => ! empty($c['selector']));
    }

    /**
     * Bir denetimin değerini CSS bildirimlerine çevirir. Bileşik türler
     * (kenar boşluğu, tipografi, arka plan) tek bir değerden birden çok
     * bildirim üretir.
     */
    private function declarations(array $control, mixed $value): array
    {
        return match ($control['type'] ?? 'text') {
            'dimensions' => $this->dimensions($control, $value),
            'typography' => $this->typography($value),
            'background' => $this->background($value),
            'border' => $this->border($value),
            'shadow' => $this->shadow($value, $control['property'] ?? 'box-shadow'),
            'slider' => [($control['property'] ?? 'width') => $this->unit($value, $control)],
            'color' => [($control['property'] ?? 'color') => $this->color($value)],
            default => [($control['property'] ?? 'display') => is_scalar($value) ? (string) $value : ''],
        };
    }

    /** {t,r,b,l,unit} → "padding-top: 40px" ... */
    private function dimensions(array $control, mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $property = $control['property'] ?? 'padding';
        $unit = $value['unit'] ?? 'px';
        $sides = ['t' => 'top', 'r' => 'right', 'b' => 'bottom', 'l' => 'left'];
        $out = [];

        foreach ($sides as $short => $side) {
            $amount = $value[$short] ?? null;

            if ($amount === null || $amount === '') {
                continue;
            }

            $out["{$property}-{$side}"] = is_numeric($amount) ? $amount.$unit : (string) $amount;
        }

        return $out;
    }

    private function typography(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $out = [];

        // Yazı tipi ailesi tema jetonuna referans verebilir ("heading"/"body").
        if ($family = $value['family'] ?? null) {
            $out['font-family'] = in_array($family, ['heading', 'body'], true)
                ? "var(--font-{$family})"
                : "'{$family}', sans-serif";
        }

        $map = [
            'size' => ['font-size', 'px'],
            'weight' => ['font-weight', ''],
            'lh' => ['line-height', ''],
            'ls' => ['letter-spacing', 'em'],
            'transform' => ['text-transform', ''],
            'style' => ['font-style', ''],
            'decoration' => ['text-decoration', ''],
        ];

        foreach ($map as $key => [$property, $unit]) {
            $v = $value[$key] ?? null;

            if ($v === null || $v === '') {
                continue;
            }

            $out[$property] = is_numeric($v) && $unit ? $v.$unit : (string) $v;
        }

        return $out;
    }

    private function background(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $out = [];

        switch ($value['type'] ?? 'color') {
            case 'color':
                if ($color = $value['color'] ?? null) {
                    $out['background-color'] = $this->color($color);
                }
                break;

            case 'gradient':
                $from = $this->color($value['from'] ?? '#000000');
                $to = $this->color($value['to'] ?? '#ffffff');
                $angle = $value['angle'] ?? 180;
                $out['background-image'] = sprintf(
                    'linear-gradient(%ddeg, %s %d%%, %s %d%%)',
                    $angle, $from, $value['from_stop'] ?? 0, $to, $value['to_stop'] ?? 100
                );
                break;

            case 'image':
                if ($url = $value['url'] ?? null) {
                    $out['background-image'] = "url('".addcslashes($url, "'")."')";
                    $out['background-position'] = $value['position'] ?? 'center center';
                    $out['background-size'] = $value['size'] ?? 'cover';
                    $out['background-repeat'] = $value['repeat'] ?? 'no-repeat';

                    if (($value['attachment'] ?? null) === 'fixed') {
                        $out['background-attachment'] = 'fixed';
                    }
                }

                if ($color = $value['color'] ?? null) {
                    $out['background-color'] = $this->color($color);
                }
                break;
        }

        return $out;
    }

    private function border(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $out = [];
        $style = $value['style'] ?? 'solid';

        if (($width = $value['width'] ?? null) && is_array($width)) {
            $unit = $width['unit'] ?? 'px';

            foreach (['t' => 'top', 'r' => 'right', 'b' => 'bottom', 'l' => 'left'] as $short => $side) {
                if (($width[$short] ?? null) !== null && $width[$short] !== '') {
                    $out["border-{$side}-width"] = $width[$short].$unit;
                }
            }

            if ($style !== 'none') {
                $out['border-style'] = $style;
            }
        }

        if ($color = $value['color'] ?? null) {
            $out['border-color'] = $this->color($color);
        }

        if (($radius = $value['radius'] ?? null) && is_array($radius)) {
            $unit = $radius['unit'] ?? 'px';
            $corners = ['t' => 'top-left', 'r' => 'top-right', 'b' => 'bottom-right', 'l' => 'bottom-left'];

            foreach ($corners as $short => $corner) {
                if (($radius[$short] ?? null) !== null && $radius[$short] !== '') {
                    $out["border-{$corner}-radius"] = $radius[$short].$unit;
                }
            }
        }

        return $out;
    }

    private function shadow(mixed $value, string $property): array
    {
        if (! is_array($value) || ($value['enabled'] ?? true) === false) {
            return [];
        }

        $parts = sprintf(
            '%dpx %dpx %dpx %dpx %s',
            $value['x'] ?? 0,
            $value['y'] ?? 4,
            $value['blur'] ?? 12,
            $value['spread'] ?? 0,
            $this->color($value['color'] ?? 'rgba(0,0,0,.12)')
        );

        if (! empty($value['inset'])) {
            $parts = 'inset '.$parts;
        }

        return [$property => $parts];
    }

    /** Sayısal değere birim ekler; kullanıcı birimi değiştirebilir. */
    private function unit(mixed $value, array $control): string
    {
        if (is_array($value)) {
            return ($value['size'] ?? 0).($value['unit'] ?? $control['unit'] ?? 'px');
        }

        return $value.($control['unit'] ?? 'px');
    }

    /**
     * Tema paletinden seçilen renkler jeton olarak saklanır ("primary"),
     * böylece müşteri paleti değiştirdiğinde tüm site birlikte değişir.
     */
    private function color(string $value): string
    {
        if (str_starts_with($value, '@')) {
            return 'var(--c-'.substr($value, 1).')';
        }

        return $value;
    }

    private function write(string $device, string $selector, array $declarations): void
    {
        $declarations = array_filter($declarations, fn ($v) => $v !== '' && $v !== null);

        if (! $declarations) {
            return;
        }

        $this->buckets[$device][$selector] = array_merge(
            $this->buckets[$device][$selector] ?? [],
            $declarations
        );
    }

    private function output(): string
    {
        $css = $this->rules($this->buckets['d']);

        if ($tablet = $this->rules($this->buckets['t'])) {
            $css .= '@media(max-width:'.$this->theme->breakpoint('tablet').'px){'.$tablet.'}';
        }

        if ($mobile = $this->rules($this->buckets['m'])) {
            $css .= '@media(max-width:'.$this->theme->breakpoint('mobile').'px){'.$mobile.'}';
        }

        return $css;
    }

    private function rules(array $bucket): string
    {
        $out = '';

        foreach ($bucket as $selector => $declarations) {
            $body = '';

            foreach ($declarations as $property => $value) {
                $body .= $property.':'.$value.';';
            }

            $out .= $selector.'{'.$body.'}';
        }

        return $out;
    }
}
