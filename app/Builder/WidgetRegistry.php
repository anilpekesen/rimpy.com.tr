<?php

namespace App\Builder;

use Illuminate\Support\Collection;

/**
 * Sistemdeki tüm widget'ların listesi.
 *
 * Yeni bir widget eklemek `$widgets` dizisine bir satır yazmakla biter;
 * panel onu otomatik olarak kütüphanede gösterir, ayarlar panelini kendi
 * denetim tanımından çizer ve stilini derler.
 */
class WidgetRegistry
{
    /** @var array<int, class-string<Widget>> */
    protected array $widgets = [
        Widgets\HeadingWidget::class,
        Widgets\TextWidget::class,
        Widgets\ImageWidget::class,
        Widgets\ButtonWidget::class,
        Widgets\SpacerWidget::class,
        Widgets\DividerWidget::class,
        Widgets\IconBoxWidget::class,
        Widgets\ListWidget::class,
        Widgets\VideoWidget::class,
        Widgets\GalleryWidget::class,
        Widgets\SliderWidget::class,
        Widgets\AccordionWidget::class,
        Widgets\TabsWidget::class,
        Widgets\CounterWidget::class,
        Widgets\TestimonialWidget::class,
        Widgets\LogoStripWidget::class,
        Widgets\ContentListWidget::class,
        Widgets\FormWidget::class,
        Widgets\MapWidget::class,
        Widgets\HtmlWidget::class,
        Widgets\MenuWidget::class,
        Widgets\LanguageSwitcherWidget::class,
        Widgets\BreadcrumbWidget::class,
        Widgets\TemplateWidget::class,
        Widgets\QuoteCartWidget::class,
        Widgets\Showcase\TopbarWidget::class,
        Widgets\Showcase\HeroGaugeWidget::class,
        Widgets\Showcase\CategoryTilesWidget::class,
        Widgets\Showcase\ProductGridWidget::class,
        Widgets\Showcase\StepsWidget::class,
        Widgets\Showcase\ServiceRailWidget::class,
        Widgets\Showcase\StatBandWidget::class,
        Widgets\Showcase\IndustryTabsWidget::class,
        Widgets\Showcase\TrustGridWidget::class,
        Widgets\Showcase\CtaBandWidget::class,
        Widgets\Showcase\TorqueCalculatorWidget::class,
        Widgets\Showcase\CylinderSelectorWidget::class,
        Widgets\Showcase\UnitConverterWidget::class,
        Widgets\Showcase\FlangeSequenceWidget::class,
        Widgets\Showcase\SpecWriterWidget::class,
        Widgets\Showcase\BoltTensionerWidget::class,
    ];

    public const CATEGORIES = [
        'basic' => 'Temel',
        'media' => 'Görsel ve Video',
        'layout' => 'Düzen',
        'content' => 'İçerik',
        'form' => 'Form',
        'site' => 'Site Öğeleri',
        'catalog' => 'Katalog',
        'showcase' => 'Vitrin',
    ];

    /** @var array<string, Widget>|null */
    private ?array $instances = null;

    /** @return array<string, Widget> */
    public function all(): array
    {
        return $this->instances ??= collect($this->widgets)
            ->map(fn (string $class) => app($class))
            ->keyBy(fn (Widget $widget) => $widget->key())
            ->all();
    }

    public function find(?string $key): ?Widget
    {
        return $key ? ($this->all()[$key] ?? null) : null;
    }

    public function has(string $key): bool
    {
        return isset($this->all()[$key]);
    }

    /** Panelin widget kütüphanesi için gruplanmış liste. */
    public function grouped(): Collection
    {
        return collect($this->all())
            ->groupBy(fn (Widget $w) => $w->category())
            ->map(fn (Collection $items) => $items->map(fn (Widget $w) => [
                'key' => $w->key(),
                'label' => $w->label(),
                'icon' => $w->icon(),
            ])->values())
            ->sortKeysUsing(fn ($a, $b) => array_search($a, array_keys(self::CATEGORIES), true)
                <=> array_search($b, array_keys(self::CATEGORIES), true));
    }

    public function register(string $class): void
    {
        $this->widgets[] = $class;
        $this->instances = null;
    }
}
