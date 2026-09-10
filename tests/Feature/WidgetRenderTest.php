<?php

namespace Tests\Feature;

use App\Builder\RenderContext;
use App\Builder\Renderer;
use App\Builder\Tree;
use App\Builder\TreeEditor;
use App\Builder\WidgetRegistry;
use App\Livewire\Builder\Editor;
use App\Models\Page;
use App\Models\User;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Her widget'ı üç yerde birden çizer: ön yüzde, düzenleme tuvalinde ve
 * ayarlar panelinde.
 *
 * Bir widget'ın şablonu bozulduğunda bunu müşteri değil bu test görmelidir.
 * Blade'in yönerge tarayıcısına takılan bir ifade ya da eksik bir değişken,
 * yalnızca o widget sayfaya konduğunda ortaya çıkar; elle test etmekle
 * yirmi dört widget'ın hepsini denemek gerçekçi değildir.
 */
class WidgetRenderTest extends TestCase
{
    use RefreshDatabase;

    public static function widgetler(): array
    {
        // Kayıt defteri uygulama başlatılmadan okunamaz; anahtarlar sabit
        // listeden gelir ve eksiksizliği ayrı bir testle doğrulanır.
        return array_map(fn ($key) => [$key], [
            'heading', 'text', 'image', 'button', 'spacer', 'divider',
            'icon_box', 'list', 'video', 'gallery', 'slider', 'accordion',
            'tabs', 'counter', 'testimonial', 'logo_strip', 'content_list',
            'form', 'map', 'html', 'menu', 'language_switcher', 'breadcrumb',
            'template',
        ]);
    }

    public function test_veri_saglayici_kayitli_widgetlarin_tamamini_kapsiyor(): void
    {
        $registered = array_keys(app(WidgetRegistry::class)->all());
        $tested = array_merge(...self::widgetler());

        sort($registered);
        sort($tested);

        $this->assertSame(
            $registered,
            $tested,
            'Yeni bir widget eklendi ama bu testin listesine yazılmadı.'
        );
    }

    #[DataProvider('widgetler')]
    public function test_widget_on_yuzde_ciziliyor(string $key): void
    {
        $this->seed();

        $widget = app(WidgetRegistry::class)->find($key);
        $this->assertNotNull($widget, "Widget bulunamadı: {$key}");

        // Bölüm > sütun > widget zinciri elle kurulur; `+` birleştirmesi
        // var olan "elements" anahtarını ezmediği için iç içe atama yapılır.
        $section = TreeEditor::makeSection([100]);
        $section['elements'][0]['elements'][] = TreeEditor::makeWidget($key, $widget->starterSettings());

        $tree = Tree::fromArray(['elements' => [$section]]);

        // Ziyaretçiye hata gösterilmez; bozuk bir widget sessizce boş çizilir.
        // Bu yüzden çıktıya bakmak yetmez, bildirilen istisnalar da dinlenir.
        //
        // İşleyici `instance()` ile değiştirilir: Laravel onu tekil olarak
        // çoktan çözmüş olduğu için `bind()` bir işe yaramaz.
        $collector = new CollectsReports;
        $this->app->instance(ExceptionHandler::class, $collector);

        $html = app(Renderer::class)->render($tree, new RenderContext('tr'))->toHtml();

        $this->assertSame(
            [],
            array_map(fn (\Throwable $e) => $e->getMessage(), $collector->reported),
            "{$key} çizilirken hata bildirildi."
        );

        $this->assertStringNotContainsString('wg--error', $html, "{$key} ön yüzde hata verdi.");
        $this->assertStringContainsString('wg--'.$key, $html);
    }

    #[DataProvider('widgetler')]
    public function test_widget_tuvalde_ve_ayarlar_panelinde_ciziliyor(string $key): void
    {
        $this->seed();

        $owner = User::where('role', 'owner')->firstOrFail();
        $page = Page::firstOrFail();

        $component = Livewire::actingAs($owner)
            ->test(Editor::class, ['page' => $page])
            ->call('addSection', [100]);

        $columnId = $component->get('tree')[0]['elements'][0]['id'];

        $component->call('selectElement', $columnId)->call('addWidget', $key);

        $widgetId = $component->get('tree')[0]['elements'][0]['elements'][0]['id'];

        // Üç ayar sekmesi de açılabilmeli; denetimlerden biri bozuksa
        // yalnızca o sekme patlar ve elle fark edilmesi zordur.
        foreach (['content', 'style', 'advanced'] as $tab) {
            $html = $component->call('selectElement', $widgetId)
                ->set('settingsTab', $tab)
                ->html();

            $this->assertStringNotContainsString(
                'Bu öğe çizilemedi',
                $html,
                "{$key} widget'ı “{$tab}” sekmesinde çizilemedi."
            );
        }
    }

    #[DataProvider('widgetler')]
    public function test_widget_kaydedilip_yayinlanabiliyor(string $key): void
    {
        $this->seed();

        $owner = User::where('role', 'owner')->firstOrFail();
        $page = Page::firstOrFail();

        $component = Livewire::actingAs($owner)
            ->test(Editor::class, ['page' => $page])
            ->call('addSection', [100]);

        $columnId = $component->get('tree')[0]['elements'][0]['id'];

        $component->call('selectElement', $columnId)
            ->call('addWidget', $key)
            ->call('save');

        // Kaydedilen sayfa ziyaretçiye 200 dönmeli; şablon hatası burada
        // 500 olarak ortaya çıkar.
        $this->get('/')->assertOk();
    }
}

/**
 * Çizim sırasında bildirilen istisnaları biriktirir.
 *
 * Ön yüzde bir widget hata verdiğinde ziyaretçiye hiçbir şey gösterilmez —
 * doğru davranış budur, ama testin bunu görmesi gerekir.
 */
class CollectsReports implements ExceptionHandler
{
    /** @var array<int, \Throwable> */
    public array $reported = [];

    public function report(\Throwable $e): void
    {
        $this->reported[] = $e;
    }

    public function shouldReport(\Throwable $e): bool
    {
        return true;
    }

    public function render($request, \Throwable $e)
    {
        throw $e;
    }

    public function renderForConsole($output, \Throwable $e): void
    {
        throw $e;
    }
}
