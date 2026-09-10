<?php

namespace App\Builder\Widgets;

use App\Builder\Element;
use App\Builder\RenderContext;
use App\Builder\Widget;
use App\Models\Template;

/**
 * Kayıtlı bir şablonu sayfaya gömer. Aynı iletişim bölümünü on sayfada
 * kullanan bir müşteri, şablonu bir kez değiştirdiğinde hepsi birden
 * güncellenir.
 */
class TemplateWidget extends Widget
{
    public function key(): string { return 'template'; }
    public function label(): string { return 'Kayıtlı bölüm'; }
    public function icon(): string { return 'blocks'; }
    public function category(): string { return 'site'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'Şablon', 'controls' => [
                'template_id' => [
                    'type' => 'template_select',
                    'label' => 'Hangi bölüm?',
                    'template_type' => 'section',
                    'hint' => 'Bu bölümü düzenlediğinizde onu kullanan tüm sayfalar güncellenir.',
                ],
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        $settings = $this->resolved($element, $context);

        return [
            'settings' => $settings,
            'template' => Template::find($settings['template_id'] ?? null),
        ];
    }
}
