<?php

namespace Tests\Feature;

use App\Livewire\Admin\Design\MenuBuilder;
use App\Livewire\Admin\Design\SliderManager;
use App\Livewire\Admin\Settings\ContentTypes;
use App\Models\ContentItem;
use App\Models\ContentType;
use App\Models\Form;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Slide;
use App\Models\Slider;
use App\Models\Taxonomy;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Panelin her ekranının açıldığını doğrular.
 *
 * Bu test tek başına iş mantığını denetlemez; amacı bir ekranın sessizce
 * bozulmasını yakalamaktır. Kurulum sonrası her siteye deploy etmeden önce
 * çalıştırılır.
 */
class AdminSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->owner = User::where('role', 'owner')->firstOrFail();
    }

    public function test_giris_ekrani_acilir(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Giriş yap');
    }

    public function test_giris_yapilmadan_panele_erisilemez(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_tum_panel_ekranlari_acilir(): void
    {
        $page = Page::firstOrFail();
        $type = ContentType::firstOrFail();
        $taxonomy = Taxonomy::firstOrFail();
        $form = Form::firstOrFail();

        $item = ContentItem::create(['content_type_id' => $type->id, 'status' => 'draft']);
        $item->fillTranslation('tr', ['title' => 'Deneme kaydı', 'slug' => 'deneme-kaydi']);

        $template = Template::create(['name' => 'Üst başlık', 'type' => 'header']);

        $routes = [
            route('admin.dashboard'),
            route('admin.pages.index'),
            route('admin.pages.settings', $page),
            route('admin.media.index'),
            route('admin.content.index', $type->key),
            route('admin.content.edit', [$type->key, $item->id]),
            route('admin.terms.index', [$type->key, $taxonomy->key]),
            route('admin.design.theme'),
            route('admin.templates.index'),
            route('admin.menus.index'),
            route('admin.sliders.index'),
            route('admin.forms.index'),
            route('admin.forms.edit', $form),
            route('admin.submissions.index'),
            route('admin.settings.general'),
            route('admin.settings.languages'),
            route('admin.settings.content-types'),
            route('admin.settings.seo'),
            route('admin.settings.redirects'),
            route('admin.users.index'),
            route('admin.settings.security'),
            route('admin.account'),
            route('admin.builder.page', $page),
            route('admin.builder.template', $template),
            route('admin.builder.content', $item->id),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($this->owner)->get($url);

            $this->assertSame(200, $response->status(), "Açılmadı: {$url} (HTTP {$response->status()})");
        }
    }

    public function test_on_yuz_acilir(): void
    {
        $this->get('/')->assertOk();
        $this->get('/sitemap.xml')->assertOk();
        $this->get('/robots.txt')->assertOk();
        // Dil öneki olmayan adres önce varsayılan dile yönlendirilir,
        // ardından bulunamadı yanıtı döner.
        $this->get('/olmayan-bir-adres')
            ->assertRedirect('/tr/olmayan-bir-adres');

        $this->get('/tr/olmayan-bir-adres')->assertNotFound();
    }

    public function test_menu_surukle_birak_sirasi_ve_hiyerarsisi_kaydedilir(): void
    {
        $menu = Menu::where('key', 'header')->firstOrFail();
        $first = MenuItem::create([
            'menu_id' => $menu->id,
            'type' => 'url',
            'url' => '/bir',
            'label' => ['tr' => 'Bir'],
            'sort' => 0,
        ]);
        $second = MenuItem::create([
            'menu_id' => $menu->id,
            'type' => 'url',
            'url' => '/iki',
            'label' => ['tr' => 'İki'],
            'sort' => 1,
        ]);

        Livewire::actingAs($this->owner)
            ->test(MenuBuilder::class)
            ->set('menuId', $menu->id)
            ->call('reorderItems', [
                ['id' => $second->id, 'parent_id' => null, 'sort' => 0],
                ['id' => $first->id, 'parent_id' => $second->id, 'sort' => 0],
            ]);

        $this->assertSame(0, $second->fresh()->sort);
        $this->assertSame($second->id, $first->fresh()->parent_id);
    }

    public function test_hazir_icerik_modulu_tek_tikla_kurulur(): void
    {
        Livewire::actingAs($this->owner)
            ->test(ContentTypes::class)
            ->set('presetKey', 'projeler')
            ->call('installPreset')
            ->assertHasNoErrors();

        $projects = ContentType::where('key', 'projeler')->firstOrFail();

        $this->assertSame('Projeler', $projects->pluralLabel('tr'));
        $this->assertTrue($projects->supports('gallery'));
        $this->assertSame(['musteri', 'konum', 'yil'], collect($projects->fields)->pluck('key')->all());
    }

    public function test_slider_ve_slayt_olusturulup_siralanir(): void
    {
        $slider = Slider::create(['name' => 'Ana sayfa', 'key' => 'ana-sayfa']);
        $first = Slide::create(['slider_id' => $slider->id, 'title' => ['tr' => 'Bir'], 'sort' => 0]);
        $second = Slide::create(['slider_id' => $slider->id, 'title' => ['tr' => 'İki'], 'sort' => 1]);

        Livewire::actingAs($this->owner)
            ->test(SliderManager::class)
            ->set('sliderId', $slider->id)
            ->call('reorderSlides', [$second->id, $first->id]);

        $this->assertSame(1, $first->fresh()->sort);
        $this->assertSame(0, $second->fresh()->sort);
    }

    public function test_editor_ayarlara_giremez(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);

        $this->actingAs($editor)->get(route('admin.settings.general'))->assertForbidden();
        $this->actingAs($editor)->get(route('admin.pages.index'))->assertOk();
    }
}
