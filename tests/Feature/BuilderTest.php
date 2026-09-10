<?php

namespace Tests\Feature;

use App\Livewire\Builder\Editor;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Sayfa kurucusunun uçtan uca çalıştığını doğrular: bölüm ekleme, widget
 * yerleştirme, ayar değiştirme, geri alma, kaydetme ve ön yüzde çizim.
 */
class BuilderTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected Page $page;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->owner = User::where('role', 'owner')->firstOrFail();
        $this->page = Page::firstOrFail();
    }

    public function test_bolum_ve_widget_eklenebilir(): void
    {
        Livewire::actingAs($this->owner)
            ->test(Editor::class, ['page' => $this->page])
            ->assertSet('tree', [])
            ->call('addSection', [50, 50])
            ->assertCount('tree', 1)
            ->tap(function ($component) {
                $tree = $component->get('tree');

                $this->assertSame('section', $tree[0]['type']);
                $this->assertCount(2, $tree[0]['elements'], 'İki sütunlu bölüm bekleniyordu.');
                $this->assertSame(50.0, (float) $tree[0]['elements'][0]['settings']['width']['d']);
            });
    }

    public function test_widget_secili_sutuna_eklenir(): void
    {
        $component = Livewire::actingAs($this->owner)
            ->test(Editor::class, ['page' => $this->page])
            ->call('addSection', [100]);

        $columnId = $component->get('tree')[0]['elements'][0]['id'];

        $component->call('selectElement', $columnId)
            ->call('addWidget', 'heading')
            ->tap(function ($c) {
                $widget = $c->get('tree')[0]['elements'][0]['elements'][0];

                $this->assertSame('widget', $widget['type']);
                $this->assertSame('heading', $widget['widget']);
                // Yeni widget örnek içerikle gelmeli; boş kutu görünmemeli.
                $this->assertNotEmpty($widget['settings']['text']);
            });
    }

    public function test_ayar_degistirilir_ve_cihaza_gore_saklanir(): void
    {
        $component = Livewire::actingAs($this->owner)
            ->test(Editor::class, ['page' => $this->page])
            ->call('addSection', [100]);

        $columnId = $component->get('tree')[0]['elements'][0]['id'];
        $component->call('selectElement', $columnId)->call('addWidget', 'heading');

        $widgetId = $component->get('tree')[0]['elements'][0]['elements'][0]['id'];

        $component->call('selectElement', $widgetId)
            ->call('setSetting', 'text', 'Uyanıkerler Gıda', false)
            ->call('setSetting', 'align', 'center', true)
            ->call('$set', 'device', 'm')
            ->call('setSetting', 'align', 'left', true);

        $settings = $component->get('tree')[0]['elements'][0]['elements'][0]['settings'];

        $this->assertSame('Uyanıkerler Gıda', $settings['text']);
        $this->assertSame('center', $settings['align']['d'], 'Masaüstü hizası korunmalı.');
        $this->assertSame('left', $settings['align']['m'], 'Telefon hizası ayrı saklanmalı.');
    }

    public function test_geri_alma_calisir(): void
    {
        $component = Livewire::actingAs($this->owner)
            ->test(Editor::class, ['page' => $this->page])
            ->call('addSection', [100])
            ->assertCount('tree', 1)
            ->call('addSection', [50, 50])
            ->assertCount('tree', 2)
            ->call('undo')
            ->assertCount('tree', 1)
            ->call('redo')
            ->assertCount('tree', 2);
    }

    public function test_kaydedince_css_derlenir_ve_on_yuzde_gorunur(): void
    {
        $component = Livewire::actingAs($this->owner)
            ->test(Editor::class, ['page' => $this->page])
            ->call('addSection', [100]);

        $columnId = $component->get('tree')[0]['elements'][0]['id'];
        $component->call('selectElement', $columnId)->call('addWidget', 'heading');

        $widgetId = $component->get('tree')[0]['elements'][0]['elements'][0]['id'];

        $component->call('selectElement', $widgetId)
            ->call('setSetting', 'text', 'Köklü tarım deneyimi', false)
            ->call('setSetting', 'color', '@primary', false)
            ->call('save')
            ->assertSet('dirty', false);

        $translation = $this->page->fresh()->translation('tr');

        $this->assertNotEmpty($translation->builder_data);
        $this->assertStringContainsString(
            'var(--c-primary)',
            $translation->compiled_css,
            'Tema rengi CSS değişkenine dönüşmeliydi.'
        );

        // Ziyaretçi sayfayı açtığında içerik gerçekten görünüyor mu?
        $this->get('/')
            ->assertOk()
            ->assertSee('Köklü tarım deneyimi')
            ->assertSee('e-'.$widgetId, false);
    }

    public function test_eleman_kopyalanir_ve_silinir(): void
    {
        $component = Livewire::actingAs($this->owner)
            ->test(Editor::class, ['page' => $this->page])
            ->call('addSection', [100]);

        $sectionId = $component->get('tree')[0]['id'];

        $component->call('duplicateElement', $sectionId)->assertCount('tree', 2);

        // Kopyanın kimliği farklı olmalı; aynı olsaydı CSS'leri çakışırdı.
        $tree = $component->get('tree');
        $this->assertNotSame($tree[0]['id'], $tree[1]['id']);

        $component->call('deleteElement', $tree[1]['id'])->assertCount('tree', 1);
    }

    public function test_dil_arasi_tasarim_kopyalanir(): void
    {
        $component = Livewire::actingAs($this->owner)
            ->test(Editor::class, ['page' => $this->page])
            ->call('addSection', [100])
            ->call('save');

        $component->call('switchLocale', 'en')
            ->assertSet('tree', [])
            ->call('copyFromLocale', 'tr')
            ->assertCount('tree', 1);

        // Kimlikler yenilenmeli ki iki dil birbirinin stilini ezmesin.
        $trId = Page::first()->translation('tr')->builder_data['elements'][0]['id'];
        $this->assertNotSame($trId, $component->get('tree')[0]['id']);
    }

    public function test_ayar_panelinde_tema_renkleri_dogru_basiliyor(): void
    {
        $component = Livewire::actingAs($this->owner)
            ->test(Editor::class, ['page' => $this->page])
            ->call('addSection', [100]);

        $columnId = $component->get('tree')[0]['elements'][0]['id'];
        $component->call('selectElement', $columnId)->call('addWidget', 'heading');

        $widgetId = $component->get('tree')[0]['elements'][0]['elements'][0]['id'];

        $html = $component->call('selectElement', $widgetId)
            ->set('settingsTab', 'style')
            ->html();

        // Blade'in "@{{ }}" kaçışı yüzünden jeton bir kez ham metin olarak
        // basılmıştı; palet düğmeleri gerçek jetonu göndermeli.
        $this->assertStringNotContainsString('{{ $token }}', $html);
        $this->assertStringContainsString('@primary', $html);
    }

    public function test_tema_rengi_secilince_paletle_birlikte_degisir(): void
    {
        $component = Livewire::actingAs($this->owner)
            ->test(Editor::class, ['page' => $this->page])
            ->call('addSection', [100]);

        $columnId = $component->get('tree')[0]['elements'][0]['id'];
        $component->call('selectElement', $columnId)->call('addWidget', 'heading');
        $widgetId = $component->get('tree')[0]['elements'][0]['elements'][0]['id'];

        $component->call('selectElement', $widgetId)
            ->call('setSetting', 'color', '@primary', false)
            ->call('save');

        $css = $this->page->fresh()->translation('tr')->compiled_css;
        $this->assertStringContainsString('var(--c-primary)', $css);

        // Palet değişince derlenmiş CSS'e dokunmadan site güncellenmeli.
        \App\Models\Setting::putGroup('theme', array_replace_recursive(
            \App\Support\Theme::DEFAULTS,
            ['colors' => ['primary' => '#e11d48']]
        ));

        $this->get('/')->assertOk()->assertSee('--c-primary: #e11d48', false);
    }

    // ── Tekrarlayan listeler ─────────────────────────────────────────────

    /** Seçili widget'ı hazırlar ve kimliğini döndürür. */
    private function addWidgetTo(&$component, string $key): string
    {
        $component->call('addSection', [100]);
        $columnId = $component->get('tree')[0]['elements'][0]['id'];
        $component->call('selectElement', $columnId)->call('addWidget', $key);

        $sections = $component->get('tree');
        $widgetId = end($sections)['elements'][0]['elements'][0]['id'];

        $component->call('selectElement', $widgetId);

        return $widgetId;
    }

    private function rowsOf($component, string $widgetId, string $key): array
    {
        foreach (\App\Builder\Tree::fromArray(['elements' => $component->get('tree')])->flatten() as $element) {
            if ($element->id === $widgetId) {
                return $element->setting($key, []);
            }
        }

        return [];
    }

    public function test_listeye_satir_eklenip_cikarilabilir(): void
    {
        $component = Livewire::actingAs($this->owner)->test(Editor::class, ['page' => $this->page]);
        $widgetId = $this->addWidgetTo($component, 'accordion');

        $baslangic = count($this->rowsOf($component, $widgetId, 'items'));
        $this->assertGreaterThan(0, $baslangic, 'Yeni SSS örnek satırlarla gelmeliydi.');

        $component->call('addRepeaterRow', 'items');
        $this->assertCount($baslangic + 1, $this->rowsOf($component, $widgetId, 'items'));

        $component->call('removeRepeaterRow', 'items', 0);
        $this->assertCount($baslangic, $this->rowsOf($component, $widgetId, 'items'));
    }

    public function test_liste_satirlari_siralanabilir(): void
    {
        $component = Livewire::actingAs($this->owner)->test(Editor::class, ['page' => $this->page]);
        $widgetId = $this->addWidgetTo($component, 'accordion');

        $component->call('setRepeaterField', 'items', 0, 'title', 'Birinci')
            ->call('setRepeaterField', 'items', 1, 'title', 'İkinci')
            ->call('moveRepeaterRow', 'items', 0, 'down');

        $rows = $this->rowsOf($component, $widgetId, 'items');

        $this->assertSame('İkinci', $rows[0]['title']);
        $this->assertSame('Birinci', $rows[1]['title']);
    }

    public function test_gecersiz_satir_islemleri_agaci_bozmuyor(): void
    {
        $component = Livewire::actingAs($this->owner)->test(Editor::class, ['page' => $this->page]);
        $widgetId = $this->addWidgetTo($component, 'accordion');

        $once = $this->rowsOf($component, $widgetId, 'items');

        // Olmayan satır, sınır dışına taşıma, tanımsız liste
        $component->call('removeRepeaterRow', 'items', 99)
            ->call('moveRepeaterRow', 'items', 0, 'up')
            ->call('setRepeaterField', 'items', 99, 'title', 'x')
            ->call('addRepeaterRow', 'olmayan_liste');

        $this->assertSame($once, $this->rowsOf($component, $widgetId, 'items'));
    }

    public function test_liste_icerigi_on_yuzde_gorunuyor(): void
    {
        $component = Livewire::actingAs($this->owner)->test(Editor::class, ['page' => $this->page]);
        $this->addWidgetTo($component, 'accordion');

        $component->call('setRepeaterField', 'items', 0, 'title', 'Teslimat ne kadar sürer?')
            ->call('setRepeaterField', 'items', 0, 'body', '<p>Ortalama üç iş günü.</p>')
            ->call('save');

        $this->get('/')
            ->assertOk()
            ->assertSee('Teslimat ne kadar sürer?')
            ->assertSee('Ortalama üç iş günü.', false)
            // SSS yapısal verisi arama motorları için basılmalı.
            ->assertSee('FAQPage', false);
    }
}
