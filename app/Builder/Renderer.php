<?php

namespace App\Builder;

use App\Models\Template;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\View;

/**
 * Kurucu ağacını HTML'e çevirir.
 *
 * Aynı çizici hem ön yüzde hem de panelin düzenleme tuvalinde kullanılır.
 * Düzenleme kipinde her elemana kimliğini taşıyan veri öznitelikleri
 * eklenir; tarayıcıdaki sürükle-bırak katmanı elemanları bunlarla tanır.
 */
class Renderer
{
    public function __construct(private WidgetRegistry $registry) {}

    public function render(Tree $tree, RenderContext $context): HtmlString
    {
        $context->preloadMedia($tree);

        $html = '';

        foreach ($tree->elements as $element) {
            $html .= $this->element($element, $context);
        }

        return new HtmlString($html);
    }

    private function element(Element $element, RenderContext $context): string
    {
        return match ($element->type) {
            'section' => $this->section($element, $context),
            'column' => $this->column($element, $context),
            'widget' => $this->widget($element, $context),
            default => '',
        };
    }

    private function section(Element $element, RenderContext $context): string
    {
        $inner = '';

        foreach ($element->elements as $child) {
            $inner .= $this->element($child, $context);
        }

        $width = $element->setting('width', 'boxed');
        $classes = $this->classes($element, [
            'sec',
            'sec--'.$width,
            $element->setting('height') === 'screen' ? 'sec--screen' : null,
            $element->setting('reverse_mobile') ? 'sec--reverse-m' : null,
        ]);

        $overlay = $element->setting('overlay') ? '<div class="sec-overlay" aria-hidden="true"></div>' : '';
        $dividers = $this->dividers($element);

        return sprintf(
            '<section %s>%s%s<div class="sec-inner">%s</div>%s</section>',
            $this->attributes($element, $classes, $context),
            $overlay,
            $dividers['top'],
            $inner,
            $dividers['bottom'],
        );
    }

    private function column(Element $element, RenderContext $context): string
    {
        $inner = '';

        foreach ($element->elements as $child) {
            $inner .= $this->element($child, $context);
        }

        $classes = $this->classes($element, [
            'col',
            $element->setting('sticky') ? 'col--sticky' : null,
        ]);

        // Sütun genişliği satır içinde yazılır; derlenmiş CSS'i beklemeden
        // düzen doğru oturur ve düzenleyicide sürükleme akıcı hisseder.
        $style = $this->columnWidthStyle($element);

        return sprintf(
            '<div %s%s>%s</div>',
            $this->attributes($element, $classes, $context),
            $style ? ' style="'.e($style).'"' : '',
            $inner,
        );
    }

    private function widget(Element $element, RenderContext $context): string
    {
        $widget = $this->registry->find($element->widget);

        if (! $widget) {
            // Tanınmayan widget ziyaretçiye hiçbir şey göstermez, düzenleyicide
            // ise ne olduğu yazar; böylece sessizce kaybolmaz.
            return $context->editing
                ? '<div class="wg wg--missing">Bilinmeyen öğe: '.e((string) $element->widget).'</div>'
                : '';
        }

        $classes = $this->classes($element, ['wg', 'wg--'.$widget->key()]);

        try {
            $body = View::make($widget->view(), $widget->data($element, $context) + [
                'element' => $element,
                'context' => $context,
            ])->render();
        } catch (\Throwable $e) {
            report($e);

            // Bir widget hata verirse tüm sayfa çökmemeli.
            $body = $context->editing
                ? '<div class="wg--error">Bu öğe çizilemedi: '.e($e->getMessage()).'</div>'
                : '';
        }

        return sprintf(
            '<div %s>%s</div>',
            $this->attributes($element, $classes, $context),
            $body,
        );
    }

    /** Elemanın sınıf listesi: yapısal sınıflar + kullanıcının eklediği. */
    private function classes(Element $element, array $base): string
    {
        $classes = array_filter($base);
        $classes[] = $element->cssClass();

        if ($animation = $element->setting('_animation')) {
            $classes[] = 'anim';
            $classes[] = 'anim--'.$animation;
        }

        foreach (['t' => 'hide-t', 'm' => 'hide-m', 'd' => 'hide-d'] as $device => $class) {
            if (data_get($element->settings, "_hide.{$device}")) {
                $classes[] = $class;
            }
        }

        if ($custom = $element->setting('_css_class')) {
            // Kullanıcının girdiği sınıf adından tırnak ve açı parantezi ayıklanır.
            $classes[] = preg_replace('/[^a-zA-Z0-9_\- ]/', '', (string) $custom);
        }

        return implode(' ', array_filter($classes));
    }

    private function attributes(Element $element, string $classes, RenderContext $context): string
    {
        $attributes = ['class' => $classes];

        if ($id = $element->setting('_css_id')) {
            $attributes['id'] = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string) $id);
        }

        if ($delay = $element->setting('_animation_delay')) {
            $attributes['style'] = '--anim-delay:'.(int) (is_array($delay) ? ($delay['size'] ?? 0) : $delay).'ms';
        }

        if ($context->editing) {
            $attributes['data-el'] = $element->id;
            $attributes['data-el-type'] = $element->type;

            if ($element->widget) {
                $attributes['data-el-widget'] = $element->widget;
            }
        }

        return collect($attributes)
            ->map(fn ($value, $key) => $key.'="'.e($value).'"')
            ->implode(' ');
    }

    private function columnWidthStyle(Element $element): string
    {
        $width = $element->setting('width');

        if (! is_array($width)) {
            return '';
        }

        $parts = [];

        foreach (['d' => '--w-d', 't' => '--w-t', 'm' => '--w-m'] as $device => $variable) {
            if (! empty($width[$device])) {
                $parts[] = $variable.':'.(float) $width[$device].'%';
            }
        }

        return implode(';', $parts);
    }

    /** Bölümlerin üst/alt kenarına eklenen dekoratif şekiller. */
    private function dividers(Element $element): array
    {
        $shapes = [
            'wave' => '<path d="M0,60 C300,120 900,0 1200,60 L1200,120 L0,120 Z"/>',
            'tilt' => '<path d="M0,120 L1200,0 L1200,120 Z"/>',
            'curve' => '<path d="M0,120 Q600,0 1200,120 Z"/>',
            'triangle' => '<path d="M600,0 L1200,120 L0,120 Z"/>',
        ];

        $out = ['top' => '', 'bottom' => ''];

        foreach (['top', 'bottom'] as $edge) {
            $shape = $element->setting('divider_'.$edge);

            if ($shape && isset($shapes[$shape])) {
                $out[$edge] = '<div class="sec-divider sec-divider--'.$edge.'" aria-hidden="true">'
                    .'<svg viewBox="0 0 1200 120" preserveAspectRatio="none">'.$shapes[$shape].'</svg></div>';
            }
        }

        return $out;
    }

    /**
     * Kayıtlı bir şablonu (üst başlık, alt bilgi, bölüm) çizer. Şablon
     * bulunamazsa boş döner — eksik bir şablon siteyi çökertmez.
     */
    public function template(int|Template|null $template, RenderContext $context): HtmlString
    {
        $template = $template instanceof Template ? $template : Template::find($template);

        if (! $template) {
            return new HtmlString('');
        }

        return $this->render(Tree::fromArray($template->builder_data), $context);
    }
}
