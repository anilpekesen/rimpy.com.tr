<?php

namespace Tests\Feature;

use App\Builder\Tree;
use App\Livewire\Builder\Editor;
use App\Livewire\MediaPicker;
use App\Models\Media;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Slide;
use App\Models\Slider;
use App\Models\User;
use App\Support\Theme;
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
        Setting::putGroup('theme', array_replace_recursive(
            Theme::DEFAULTS,
            ['colors' => ['primary' => '#e11d48']]
        ));

        $this->get('/')->assertOk()->assertSee('--c-primary: #e11d48', false);
    }

    // ── Tekrarlayan listeler ─────────────────────────────────────────────

    /** Seçili widget'ı hazırlar ve kimliğini döndürür. */
    private function addWidgetTo(&$component, string $key): string
    {
        $component->call('addSection', [100]);

        // Her zaman en son eklenen bölümün sütununa yerleştir; birden çok
        // bölüm eklendiğinde ilk bölüme geri dönmemek için.
        $sections = $component->get('tree');
        $columnId = end($sections)['elements'][0]['id'];

        $component->call('selectElement', $columnId)->call('addWidget', $key);

        $sections = $component->get('tree');
        $column = end($sections)['elements'][0];
        $widgetId = end($column['elements'])['id'];

        $component->call('selectElement', $widgetId);

        return $widgetId;
    }

    private function rowsOf($component, string $widgetId, string $key): array
    {
        foreach (Tree::fromArray(['elements' => $component->get('tree')])->flatten() as $element) {
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

    public function test_sayfada_yalnizca_bir_sss_bildirimi_basilir(): void
    {
        $component = Livewire::actingAs($this->owner)->test(Editor::class, ['page' => $this->page]);

        // Aynı sayfaya iki SSS bölümü koy.
        $this->addWidgetTo($component, 'accordion');
        $this->addWidgetTo($component, 'accordion');

        $component->call('save');

        $html = $this->get('/')->assertOk()->getContent();

        // Google sayfa başına tek bir FAQPage bekler; ikisi de basılırsa
        // hiçbirini dikkate almaz.
        $this->assertSame(
            1,
            substr_count($html, '"@type":"FAQPage"'),
            'Sayfada birden fazla SSS bildirimi basıldı.'
        );
    }

    public function test_gorsel_secici_panelde_ve_kurucuda_yuklenir(): void
    {
        $media = Media::create([
            'disk' => 'public',
            'path' => 'media/test.jpg',
            'filename' => 'test.jpg',
            'original_name' => 'test.jpg',
            'type' => 'image',
            'mime' => 'image/jpeg',
            'extension' => 'jpg',
            'size' => 100,
            'hash' => hash('sha256', 'test-image'),
            'alt' => ['tr' => 'Test görseli'],
            'title' => ['tr' => 'Test'],
        ]);

        Livewire::actingAs($this->owner)
            ->test(MediaPicker::class)
            ->dispatch('open-media-picker', target: 'image', accept: 'image')
            ->assertSet('isOpen', true)
            ->assertSee('test.jpg')
            ->call('choose', $media->id)
            ->assertDispatched('media-selected')
            ->assertSet('isOpen', false);
    }

    public function test_kayitli_slider_sayfa_kurucuda_gosterilir(): void
    {
        $media = Media::create([
            'disk' => 'public', 'path' => 'media/hero.jpg', 'filename' => 'hero.jpg',
            'original_name' => 'hero.jpg', 'type' => 'image', 'mime' => 'image/jpeg',
            'extension' => 'jpg', 'size' => 100, 'hash' => hash('sha256', 'hero-image'),
            'alt' => ['tr' => 'Fabrika'], 'title' => ['tr' => 'Fabrika'],
        ]);
        $slider = Slider::create(['name' => 'Ana sayfa', 'key' => 'ana-sayfa']);
        Slide::create([
            'slider_id' => $slider->id,
            'image_id' => $media->id,
            'title' => ['tr' => 'Üretimde güçlü çözüm'],
            'text' => ['tr' => 'Deneyimli ekip, güvenilir üretim.'],
            'sort' => 0,
        ]);

        $component = Livewire::actingAs($this->owner)
            ->test(Editor::class, ['page' => $this->page])
            ->call('addSection', [100]);

        $columnId = $component->get('tree')[0]['elements'][0]['id'];
        $component->call('selectElement', $columnId)->call('addWidget', 'slider');
        $widgetId = $component->get('tree')[0]['elements'][0]['elements'][0]['id'];

        $component->call('selectElement', $widgetId)
            ->call('setSetting', 'slider_id', $slider->id, false)
            ->call('save');

        $this->get('/')->assertOk()->assertSee('Üretimde güçlü çözüm');
    }

    /**
     * Kaydedilmiş bir sayfa yeniden açıldığında eleman seçilebilmeli.
     *
     * `Element::toArray()` ayarları bilinçli olarak nesneye çevirir — boş ayar
     * kümesi JSON'a `[]` değil `{}` yazılsın diye. Düzenleyici ise ayarlara
     * dizi gibi erişir; ağaç yüklenirken düz diziye indirgenmezse ilk seçimde
     * "Cannot use object of type stdClass as array" hatası alınıyordu.
     */
    public function test_kaydedilmis_sayfada_eleman_secilebilir(): void
    {
        Livewire::actingAs($this->owner)
            ->test(Editor::class, ['page' => $this->page])
            ->call('addSection', [100])
            ->call('addWidget', 'heading')
            ->call('save');

        $component = Livewire::actingAs($this->owner)
            ->test(Editor::class, ['page' => $this->page->fresh()]);

        $tree = $component->get('tree');

        $this->assertNotEmpty($tree, 'Kaydedilen tasarım geri yüklenmeliydi.');
        $this->assertIsArray($tree[0]['settings'], 'Ayarlar düz dizi olmalı.');

        // Ayar panelinin çizilmesi bu çağrıyla tetikleniyor.
        $component->call('selectElement', $tree[0]['id'])->assertOk();
    }

    public function test_onizleme_kaydedilmemis_agaci_sitenin_duzeniyle_cizer(): void
    {
        $component = Livewire::actingAs($this->owner)
            ->test(Editor::class, ['page' => $this->page])
            ->call('addSection', [100])
            ->call('addWidget', 'heading');

        preg_match('#/kurucu/onizleme/([a-f0-9]{40})#', $component->html(), $match);
        $this->assertNotEmpty($match, 'Düzenleyici önizleme adresini basmalı.');

        $response = $this->actingAs($this->owner)->get(route('admin.builder.preview', $match[1]));

        $response->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertSee('data-el-type="section"', false)
            ->assertSee('data-el-label="Başlık"', false)
            ->assertSee('builder-preview', false);
    }

    public function test_onizleme_baska_kullaniciya_acilmaz(): void
    {
        $component = Livewire::actingAs($this->owner)
            ->test(Editor::class, ['page' => $this->page])
            ->call('addSection', [100]);

        preg_match('#/kurucu/onizleme/([a-f0-9]{40})#', $component->html(), $match);

        $other = User::factory()->create(['role' => 'admin']);

        $this->actingAs($other)->get(route('admin.builder.preview', $match[1]))->assertNotFound();
        $this->actingAs($this->owner)->get(route('admin.builder.preview', str_repeat('a', 40)))->assertNotFound();
    }
}
