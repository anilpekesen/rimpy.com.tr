<?php

namespace App\Import;

use App\Models\ContentType;
use App\Models\Form;
use App\Models\Media;
use App\Models\MediaFolder;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Taxonomy;
use App\Models\Template;
use App\Models\Term;
use App\Services\MediaUploader;
use App\Support\Urls;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Rimpy'nin üst başlığı, alt bilgisi, menüleri ve ana sayfası.
 *
 * Hepsi kurucunun kendi biçiminde üretilir; müşteri panelde her bölümü,
 * widget'ı ve metni normal şekilde düzenleyebilir. Komut tekrar
 * çalıştırıldığında şablonlar ve ana sayfa baştan yazılır — panelde
 * yapılan düzenlemeler varsa `--sayfalari-koru` ile atlanır.
 */
class RimpyComposer
{
    private const DATA = 'database/data/rimpy';

    private array $scenes = [];

    public function __construct(private MediaUploader $uploader) {}

    // ── Medya ────────────────────────────────────────────────────────────

    public function brand(): void
    {
        $folder = $this->folder('Marka');

        // Açık zeminde koyu yazılı logo, koyu zeminde (alt bilgi) orijinal beyaz yazılı.
        $dark = $this->upload(base_path('rimpy-logo-koyu.webp'), 'rimpy-logo-koyu.webp', $folder);
        $light = $this->upload(base_path('IxtK6ypfkQPx6C0XI4X6dE8O-large.webp'), 'rimpy-logo.webp', $folder);
        $icon = $this->upload(base_path('IxtK6ypfkQPx6C0XI4X6dE8O-small.webp'), 'rimpy-ikon.webp', $folder);

        Setting::put('general', 'logo_id', ($dark ?? $light)?->id);
        Setting::put('general', 'logo_light_id', $light?->id);
        Setting::put('general', 'favicon_id', $icon?->id);
    }

    /** Sahne görsellerini (varsa) medya kütüphanesine alır. */
    public function scenes(): array
    {
        $folder = $this->folder('Sahneler');

        foreach (glob(base_path(self::DATA.'/gorseller/sahne-*.{jpg,jpeg,png,webp,mp4,webm}'), GLOB_BRACE) as $path) {
            $key = Str::after(pathinfo($path, PATHINFO_FILENAME), 'sahne-');

            // Aynı sahnenin videosu ayrı anahtarla tutulur: sahne-hero.mp4 → hero-video
            if (in_array(pathinfo($path, PATHINFO_EXTENSION), ['mp4', 'webm'], true)) {
                $key .= '-video';
            }

            $this->scenes[$key] = $this->upload($path, 'rimpy-'.basename($path), $folder)?->id;
        }

        // Ana kategorilere kart ve mega menü görseli: sahne varsa sahne,
        // yoksa o gruptaki bir ürünün fotoğrafı.
        $map = [
            'tork-ekipmanlari' => ['kategori-tork', 'RHT-08'],
            'hidrolik-ekipmanlar' => ['kategori-hidrolik', 'RPE-554'],
            'flans-bakim-ekipmanlari' => [null, 'RFS-14'],
            'test-ve-kalibrasyon' => [null, 'RGA-63'],
            'endustriyel-ekipmanlar' => ['kategori-endustriyel', 'RPL-303'],
        ];

        foreach ($map as $slug => [$scene, $code]) {
            $image = ($scene ? ($this->scenes[$scene] ?? null) : null) ?? $this->productImage($code);

            if (! $image) {
                continue;
            }

            Term::whereHas('translations', fn ($q) => $q->where('locale', 'tr')->where('slug', $slug))
                ->update(['image_id' => $image]);
        }

        return $this->scenes;
    }

    // ── Menüler ──────────────────────────────────────────────────────────

    /**
     * Üst menü: Anasayfa · Kurumsal · Ürünler (görselli mega menü) ·
     * Markalar · Teknik & Blog · Hizmetlerimiz · İletişim.
     *
     * @param  array<string, Page>  $pages  RimpyPages::all()
     */
    public function menus(array $pages): void
    {
        $type = ContentType::where('key', 'urunler')->first();
        $blog = ContentType::where('key', 'blog')->where('is_active', true)->first();
        $taxonomy = Taxonomy::where('content_type_id', $type->id)->where('key', 'kategori')->first();
        $roots = Term::withTranslations()->with(['children' => fn ($q) => $q->where('is_active', true)->with('translations')])
            ->where('taxonomy_id', $taxonomy->id)->whereNull('parent_id')->where('is_active', true)->orderBy('sort')->get();
        $name = fn (Term $term) => $term->translations->pluck('name', 'locale')->all();
        $title = fn (Page $page) => $page->translations->pluck('title', 'locale')->all();

        $header = $this->resetMenu('header', 'Üst menü');

        $this->item($header, 'home', null, ['tr' => 'Anasayfa', 'en' => 'Home'], 0);
        $this->item($header, 'page', $pages['kurumsal']->id, $title($pages['kurumsal']), 1);

        // Ürünler: ana kategoriler sütun, alt kategoriler sütunun listesi.
        $products = $this->item($header, 'archive', $type->id, ['tr' => 'Ürünler', 'en' => 'Products'], 2, mega: true);

        foreach ($roots as $i => $root) {
            $column = $this->item($header, 'term', $root->id, $name($root), $i, $products->id);

            foreach ($root->children->values() as $j => $child) {
                $this->item($header, 'term', $child->id, $name($child), $j, $column->id);
            }
        }

        $this->item($header, 'page', $pages['markalar']->id, $title($pages['markalar']), 3);

        // Mühendislik araçları toolPages() içinde bu başlığın altına eklenir.
        $blog
            ? $this->item($header, 'archive', $blog->id, ['tr' => 'Teknik & Blog', 'en' => 'Technical & Blog'], 4)
            : $this->item($header, 'url', null, ['tr' => 'Teknik & Blog', 'en' => 'Technical & Blog'], 4, url: '#');

        $services = $this->item($header, 'page', $pages['hizmetler']->id, $title($pages['hizmetler']), 5);
        $sort = 0;

        foreach ($pages as $key => $page) {
            if (str_starts_with($key, 'hizmet:')) {
                $this->item($header, 'page', $page->id, $title($page), $sort++, $services->id);
            }
        }

        $this->item($header, 'page', $pages['iletisim']->id, $title($pages['iletisim']), 6);

        $footerProducts = $this->resetMenu('footer-urunler', 'Alt menü: ürünler');
        $this->item($footerProducts, 'archive', $type->id, ['tr' => 'Tüm ürünler', 'en' => 'All products'], 0);

        foreach ($roots as $i => $root) {
            $this->item($footerProducts, 'term', $root->id, $name($root), $i + 1);
        }

        $company = $this->resetMenu('footer-kurumsal', 'Alt menü: kurumsal');
        $this->item($company, 'page', $pages['kurumsal']->id, $title($pages['kurumsal']), 0);
        $this->item($company, 'page', $pages['hizmetler']->id, $title($pages['hizmetler']), 1);
        $this->item($company, 'page', $pages['markalar']->id, $title($pages['markalar']), 2);
        $this->item($company, 'page', $pages['iletisim']->id, $title($pages['iletisim']), 5);
    }

    // ── Şablonlar ────────────────────────────────────────────────────────

    public function header(): Template
    {
        $logo = Setting::get('general.logo_id');

        $tree = [
            Blocks::section([
                'width' => 'full-content',
                '_padding' => Blocks::padY(8, 8),
                'align_items' => 'center',
                '_css_class' => 'topbar',
                '_hide' => ['m' => true],
            ], [
                Blocks::column(100, [Blocks::widget('topbar')]),
            ]),
            Blocks::section([
                'width' => 'boxed',
                '_padding' => Blocks::padY(14, 14),
                'align_items' => 'center',
                'gap' => Blocks::size(24),
                '_css_class' => 'masthead',
            ], [
                Blocks::column(14, [
                    Blocks::widget('image', [
                        'image' => $logo,
                        'size' => 'medium',
                        'lazy' => false,
                        'width' => Blocks::size(168, 'px', ['m' => 132]),
                        'link' => ['type' => 'url', 'url' => '/'],
                    ]),
                ], [], ['t' => 30, 'm' => 45]),
                Blocks::column(70, [
                    Blocks::widget('menu', [
                        'menu_key' => 'header',
                        'orientation' => 'horizontal',
                        'mobile' => 'drawer',
                        'breakpoint' => 'tablet',
                        'align' => Blocks::responsive('flex-start', 'flex-end'),
                        'color' => '@heading',
                        'hover_color' => '@accent',
                        'active_color' => '@accent',
                        'indicator' => '',
                        'gap' => Blocks::size(20),
                        'submenu_bg' => '#ffffff',
                        'typography' => Blocks::type(['family' => 'heading', 'size' => 15, 'weight' => 600]),
                    ]),
                ], [], ['t' => 40, 'm' => 25]),
                Blocks::column(16, [
                    Blocks::widget('quote_cart', [
                        'text' => ['tr' => 'Teklif sepeti', 'en' => 'Quote'],
                        'align' => Blocks::responsive('right'),
                    ]),
                ], [], ['t' => 30, 'm' => 30]),
            ]),
        ];

        return $this->saveTemplate('Rimpy üst başlık', 'header', $tree);
    }

    public function footer(): Template
    {
        $logo = Setting::get('general.logo_light_id');
        $contact = Setting::get('contact', []) ?: [];
        $menu = fn (string $key) => Blocks::widget('menu', [
            'menu_key' => $key,
            'orientation' => 'vertical',
            'mobile' => 'same',
            'align' => Blocks::responsive('flex-start'),
            'color' => '#cfd2d6',
            'hover_color' => '@primary',
            'active_color' => '@primary',
            'indicator' => '',
            'gap' => Blocks::size(10),
            'typography' => Blocks::type(['family' => 'body', 'size' => 15, 'weight' => 500]),
        ]);
        $heading = fn (array $text) => Blocks::widget('heading', [
            'text' => $text, 'tag' => 'div', '_css_class' => 'f-h',
        ]);

        $tree = [
            // Teklif şeridi: her sayfanın sonunda tek bir net çağrı.
            Blocks::section([
                'width' => 'boxed',
                '_padding' => Blocks::padY(44, 44, [36, 36]),
                'align_items' => 'center',
                'gap' => Blocks::size(24),
                '_css_class' => 'f-cta',
            ], [
                Blocks::column(68, [
                    Blocks::widget('heading', [
                        'tag' => 'div',
                        '_css_class' => 'f-cta-title',
                        'text' => ['tr' => 'Doğru ekipmandan emin değil misiniz?', 'en' => 'Not sure which equipment fits?'],
                    ]),
                    Blocks::widget('text', [
                        '_css_class' => 'f-cta-text',
                        'body' => [
                            'tr' => '<p>Cıvata ölçüsünü ve tork değerini yazın; mühendislerimiz 24 saat içinde doğru seti ve fiyatı iletsin.</p>',
                            'en' => '<p>Send us the bolt size and torque; our engineers reply with the right kit and price within 24 hours.</p>',
                        ],
                    ]),
                ], [], ['t' => 60, 'm' => 100]),
                Blocks::column(32, [
                    Blocks::widget('button', [
                        '_css_class' => 'f-cta-btn',
                        'text' => ['tr' => 'Teklif iste', 'en' => 'Request a quote'],
                        'link' => ['type' => 'anchor', 'anchor' => 'teklif'],
                        'icon' => 'arrow-right',
                        'align' => Blocks::responsive('right', 'left'),
                        'bg' => '@primary',
                        'color' => '#111111',
                        'size' => 'lg',
                    ]),
                ], [], ['t' => 40, 'm' => 100]),
            ]),
            Blocks::section([
                'width' => 'boxed',
                '_padding' => Blocks::padY(64, 48, [48, 32]),
                'gap' => Blocks::size(40),
                '_css_class' => 'f-top',
            ], [
                Blocks::column(34, [
                    Blocks::widget('image', ['image' => $logo, 'size' => 'medium', 'width' => Blocks::size(132, 'px'), 'link' => ['type' => 'url', 'url' => '/']]),
                    Blocks::widget('text', [
                        '_css_class' => 'f-about',
                        'body' => [
                            'tr' => '<p>Hidrolik tork anahtarları, 700 bar silindir ve pompalar, endüstriyel cıvata çözümleri. Stoktan teslim, Türkiye\'de servis ve kalibrasyon.</p>',
                            'en' => '<p>Hydraulic torque wrenches, 700 bar cylinders and pumps, industrial bolting solutions. Delivery from stock, service and calibration in Türkiye.</p>',
                        ],
                    ]),
                ], [], ['t' => 100, 'm' => 100]),
                Blocks::column(22, [$heading(['tr' => 'Ürünler', 'en' => 'Products']), $menu('footer-urunler')], [], ['t' => 33.33, 'm' => 50]),
                Blocks::column(22, [$heading(['tr' => 'Kurumsal', 'en' => 'Company']), $menu('footer-kurumsal')], [], ['t' => 33.33, 'm' => 50]),
                Blocks::column(22, [
                    $heading(['tr' => 'İletişim', 'en' => 'Contact']),
                    Blocks::widget('list', [
                        'items' => array_values(array_filter([
                            filled($contact['phone'] ?? null) ? ['text' => $contact['phone'], 'icon' => 'phone', 'link' => ['type' => 'phone', 'phone' => $contact['phone']]] : null,
                            ['text' => $contact['email'] ?? 'info@rimpy.com.tr', 'icon' => 'mail', 'link' => ['type' => 'email', 'email' => $contact['email'] ?? 'info@rimpy.com.tr']],
                            filled($contact['address']['tr'] ?? null) ? ['text' => $contact['address']['tr'], 'icon' => 'map-pin'] : null,
                        ])),
                        'icon_color' => '@primary',
                        'text_color' => '#cfd2d6',
                        'gap' => Blocks::size(12),
                    ]),
                ], [], ['t' => 33.33, 'm' => 100]),
            ]),
            Blocks::section(['width' => 'boxed', '_padding' => Blocks::padY(20, 24), '_css_class' => 'f-bottom', 'align_items' => 'center'], [
                Blocks::column(50, [Blocks::widget('text', ['body' => ['tr' => '<p>© '.date('Y').' Rimpy. Tüm hakları saklıdır.</p>', 'en' => '<p>© '.date('Y').' Rimpy. All rights reserved.</p>']])], [], ['t' => 50, 'm' => 100]),
                Blocks::column(50, [
                    Blocks::widget('text', ['_css_class' => 'f-legal', 'body' => ['tr' => '<p>Fiyatlar teklif usulüdür · KVKK</p>', 'en' => '<p>Prices on request · Privacy</p>']]),
                    Blocks::widget('language_switcher', ['_css_class' => 'f-lang', 'display' => 'code', 'style' => 'inline', 'color' => '#8B9099', 'active_color' => '#FFFFFF']),
                ], ['_css_class' => 'f-bottom-end'], ['t' => 50, 'm' => 100]),
            ]),
        ];

        return $this->saveTemplate('Rimpy alt bilgi', 'footer', $tree);
    }

    // ── Ana sayfa ────────────────────────────────────────────────────────

    public function homePage(): Page
    {
        $page = Page::where('is_home', true)->first()
            ?? Page::create(['status' => 'published', 'published_at' => now(), 'is_home' => true]);

        $seo = [
            'tr' => ['Ana Sayfa', 'ana-sayfa', 'Rimpy — Hidrolik Tork Anahtarı, 700 Bar Silindir ve Pompa',
                'Hidrolik tork anahtarları, yüksek basınç silindir ve pompalar, endüstriyel cıvata çözümleri. Stoktan teslim, Türkiye\'de servis, 24 saatte teklif.'],
            'en' => ['Home', 'home', 'Rimpy — Hydraulic Torque Wrenches, 700 Bar Cylinders and Pumps',
                'Hydraulic torque wrenches, high pressure cylinders and pumps, industrial bolting solutions. Delivery from stock, service in Türkiye, quotes within 24 hours.'],
        ];

        // Her dilin kendi ağacı var: tekrarlayıcı alanlar (adımlar, kartlar)
        // dil haritası taşıyamadığı için metinler dile göre baştan yazılır.
        foreach ($seo as $locale => [$title, $slug, $seoTitle, $seoDescription]) {
            [$data, $css] = Blocks::compile($this->homeTree($locale));

            $page->fillTranslation($locale, [
                'title' => $title,
                'slug' => $slug,
                'builder_data' => $data,
                'compiled_css' => $css,
                'seo_title' => $seoTitle,
                'seo_description' => $seoDescription,
            ]);
        }

        return $page;
    }

    private function homeTree(string $locale): array
    {
        $scene = fn (string $key) => $this->scenes[$key] ?? null;
        $pad = Blocks::padY(140, 140, [88, 88]);
        $copy = $locale === 'en' ? HomeCopy::english() : [];
        $with = fn (string $widget, array $settings = []) => Blocks::widget($widget, array_replace($copy[$widget] ?? [], $settings));

        return [
            Blocks::section(['width' => 'full', '_padding' => Blocks::padY(0, 0), '_css_class' => 'tone-dark'], [
                Blocks::column(100, [
                    $with('hero_gauge', [
                        'primary_link' => ['type' => 'url', 'url' => $locale === 'en' ? '/en/products' : '/tr/urunler'],
                        'poster' => $scene('hero'),
                        'video' => $scene('hero-video'),
                    ]),
                ]),
            ]),
            Blocks::section(['width' => 'boxed', '_padding' => $pad, '_css_id' => 'urun-gruplari'], [
                Blocks::column(100, [$with('category_tiles')]),
            ]),
            Blocks::section(['width' => 'boxed', '_padding' => $pad, '_background' => Blocks::bgColor('#EAE7E0'), '_css_id' => 'urunler'], [
                Blocks::column(100, [$with('product_grid', ['tone' => 'light'])]),
            ]),
            Blocks::section(['width' => 'boxed', '_padding' => $pad], [
                Blocks::column(100, [$with('steps')]),
            ]),
            Blocks::section(['width' => 'full', '_padding' => Blocks::padY(0, 0), '_css_id' => 'hizmetler', '_css_class' => 'tone-dark'], [
                Blocks::column(100, [$with('service_rail', [
                    'items' => $this->withImages($copy['service_rail']['items'] ?? $this->defaultsOf(\App\Builder\Widgets\Showcase\ServiceRailWidget::class), [
                        0 => ['hizmet-kiralama', 'RHT-20'],
                        1 => ['hizmet-kalibrasyon', 'RET-35'],
                        2 => ['hizmet-kaldirma', 'RSC-DA200'],
                        3 => [null, 'RLP-36'],
                        4 => [null, 'RBT-27'],
                    ]),
                ])]),
            ]),
            Blocks::section(['width' => 'boxed', '_padding' => $pad], [
                Blocks::column(100, [$with('stat_band')]),
            ]),
            Blocks::section(['width' => 'boxed', '_padding' => Blocks::padY(0, 140, [0, 88]), '_css_id' => 'sektorler'], [
                Blocks::column(100, [$with('industry_tabs', [
                    'items' => $this->withImages($copy['industry_tabs']['items'] ?? $this->defaultsOf(\App\Builder\Widgets\Showcase\IndustryTabsWidget::class), [
                        0 => ['hero', 'RBT-27'],
                        1 => ['sektor-enerji', 'RFS-14'],
                        2 => [null, 'RSC-DA200'],
                        3 => [null, 'RPL-303'],
                        4 => [null, 'RTM-125'],
                    ]),
                ])]),
            ]),
            Blocks::section(['width' => 'boxed', '_padding' => Blocks::padY(0, 140, [0, 88])], [
                Blocks::column(100, [$with('trust_grid')]),
            ]),
            Blocks::section(['width' => 'boxed', '_padding' => Blocks::padY(0, 0), '_background' => Blocks::bgColor('@primary'), '_css_class' => 'cta-hazard'], [
                Blocks::column(100, [$with('cta_band')]),
            ]),
        ];
    }

    // ── Mühendislik araçları ─────────────────────────────────────────────

    /** @return array<string, Page>  anahtar => sayfa */
    public function toolPages(): array
    {
        // Germe hesabındaki "uygulama formu" butonu form sayfasına gider.
        $formSlugs = ['tr' => 'civata-germe-talep-formu', 'en' => 'bolt-tensioning-application-form'];
        $tensionForm = Form::where('key', 'germe-talebi')->value('id');

        $pages = [
            'tork' => ['torque_calculator', [
                'tr' => ['Tork Hesaplayıcı', 'tork-hesaplayici', 'Cıvata tork hesaplayıcı — metrik ve inç, VDI 2230 | Rimpy',
                    'Cıvata çapı, kalite ve yağlamaya göre gerekli sıkma torkunu hesaplayın; uygun hidrolik tork anahtarını ve pompa basıncını görün.'],
                'en' => ['Torque Calculator', 'torque-calculator', 'Bolt torque calculator — metric and inch, VDI 2230 | Rimpy',
                    'Calculate the tightening torque from bolt size, property class and lubrication; see the right hydraulic torque wrench and pump pressure.'],
            ]],
            'silindir' => ['cylinder_selector', [
                'tr' => ['Silindir Seçici', 'silindir-secici', 'Hidrolik silindir seçici ve sistem kurucu | Rimpy',
                    'Yük, kaldırma noktası ve stroka göre hidrolik silindir seçin; pompa, hortum ve manometre setini tek tıkla teklif sepetine ekleyin.'],
                'en' => ['Cylinder Selector', 'cylinder-selector', 'Hydraulic cylinder selector and system builder | Rimpy',
                    'Choose hydraulic cylinders by load, lifting points and stroke; add the matching pump, hoses and gauge to your quote in one click.'],
            ]],
            'germe' => ['bolt_tensioner', [
                'tr' => ['Cıvata Germe Hesabı', 'civata-germe-hesabi', 'Hidrolik cıvata germe basıncı hesabı — LTF dahil | Rimpy',
                    'Hedef ön yük ve sıkma boyundan germe yükünü ve pompa basıncını yük kaybı (LTF) dahil hesaplayın; uygun germe aletini görün.'],
                'en' => ['Bolt Tensioning Calculator', 'bolt-tensioning-calculator', 'Hydraulic bolt tensioning pressure calculator with LTF | Rimpy',
                    'Calculate tensioning load and pump pressure from target preload and grip length, including load transfer factor; see the right tensioner.'],
            ], fn ($locale) => ['request_url' => Urls::prefix($locale).'/'.$formSlugs[$locale]]],
            'germe-formu' => ['form', [
                'tr' => ['Cıvata Germe Talep Formu', $formSlugs['tr'], 'Cıvata germe uygulama talep formu | Rimpy',
                    'Kritik cıvata germe uygulamanızın cıvata, yük, flanş ve çalışma alanı bilgilerini gönderin; mühendisimiz hesabı yapıp size dönsün.'],
                'en' => ['Bolt Tensioning Application Form', $formSlugs['en'], 'Bolt tensioning application form | Rimpy',
                    'Send the bolt, load, flange and space details of your critical tensioning application; our engineer will run the calculation and reply.'],
            ], fn ($locale) => [
                'form_id' => $tensionForm,
                'title' => $locale === 'en' ? 'Bolt tensioning application form' : 'Cıvata germe uygulama formu',
                'description' => $locale === 'en'
                    ? 'The more of these details you give, the more exact our calculation and tool selection. Leave blank what you do not know.'
                    : 'Bu bilgiler ne kadar eksiksiz olursa hesabımız ve alet seçimimiz o kadar kesin olur. Bilmediğiniz alanları boş bırakabilirsiniz.',
            ]],
            'birim' => ['unit_converter', [
                'tr' => ['Birim Dönüştürücü', 'birim-donusturucu', 'Hidrolik birim dönüştürücü — tork, basınç, kuvvet, debi | Rimpy',
                    'N·m, ft·lb, bar, psi, kN, ton, L/dk ve sıcaklık birimlerini anında dönüştürün.'],
                'en' => ['Unit Converter', 'unit-converter', 'Hydraulic unit converter — torque, pressure, force, flow | Rimpy',
                    'Convert N·m, ft·lb, bar, psi, kN, tonnes, L/min and temperature units instantly.'],
            ]],
            'flans' => ['flange_sequence', [
                'tr' => ['Flanş Sıkma Sıralaması', 'flans-sikma-siralamasi', 'Flanş cıvatası sıkma sıralaması ve tur torkları | Rimpy',
                    'Cıvata sayısı ve hedef torktan çapraz sıkma sırasını, her turun torkunu ve adım adım çizimi çıkarın.'],
                'en' => ['Flange Tightening Sequence', 'flange-tightening-sequence', 'Flange bolt tightening sequence and pass torques | Rimpy',
                    'Get the cross tightening pattern, the torque for each pass and a step-by-step drawing from bolt count and target torque.'],
            ]],
            'sartname' => ['spec_writer', [
                'tr' => ['Teknik Şartname Hazırlayıcı', 'teknik-sartname', 'Hidrolik ekipman teknik şartname hazırlayıcı | Rimpy',
                    'Tork anahtarı, silindir, cıvata germe ve pompa için ihaleye hazır teknik şartname maddelerini oluşturun.'],
                'en' => ['Specification Writer', 'specification-writer', 'Hydraulic equipment technical specification writer | Rimpy',
                    'Create tender-ready technical specification clauses for torque wrenches, cylinders, bolt tensioners and pumps.'],
            ]],
        ];

        $result = [];

        foreach ($pages as $key => $definition) {
            // Üçüncü öğe isteğe bağlı: dile göre ek widget ayarları üreten fonksiyon.
            [$widget, $locales, $extra] = $definition + [2 => null];

            $page = Page::whereHas('translations', fn ($q) => $q->where('locale', 'tr')->where('slug', $locales['tr'][1]))->first()
                ?? Page::create(['status' => 'published', 'published_at' => now(), 'show_in_search' => true]);

            foreach ($locales as $locale => [$title, $slug, $seoTitle, $seoDescription]) {
                $settings = ($locale === 'en' ? (HomeCopy::english()[$widget] ?? []) : []) + ($extra ? $extra($locale) : []);

                [$data, $css] = Blocks::compile([
                    Blocks::section(['width' => 'boxed', '_padding' => Blocks::padY(72, 140, [48, 88])], [
                        Blocks::column(100, [Blocks::widget($widget, $settings)]),
                    ]),
                ]);

                $page->fillTranslation($locale, [
                    'title' => $title,
                    'slug' => $slug,
                    'builder_data' => $data,
                    'compiled_css' => $css,
                    'seo_title' => $seoTitle,
                    'seo_description' => $seoDescription,
                ]);
            }

            $result[$key] = $page;
        }

        $header = Menu::where('key', 'header')->first();
        $parent = MenuItem::where('menu_id', $header->id)->whereNull('parent_id')->where('sort', 4)->first()
            ?? $this->item($header, 'page', $result['tork']->id, ['tr' => 'Mühendislik', 'en' => 'Engineering'], 9);
        $this->item($header, 'page', $result['tork']->id, ['tr' => 'Tork hesaplayıcı', 'en' => 'Torque calculator'], 0, $parent->id);
        $this->item($header, 'page', $result['silindir']->id, ['tr' => 'Silindir seçici ve sistem kurucu', 'en' => 'Cylinder selector and system builder'], 1, $parent->id);
        $this->item($header, 'page', $result['germe']->id, ['tr' => 'Cıvata germe hesabı', 'en' => 'Bolt tensioning calculator'], 2, $parent->id);
        $this->item($header, 'page', $result['flans']->id, ['tr' => 'Flanş sıkma sıralaması', 'en' => 'Flange tightening sequence'], 2, $parent->id);
        $this->item($header, 'page', $result['birim']->id, ['tr' => 'Birim dönüştürücü', 'en' => 'Unit converter'], 3, $parent->id);
        $this->item($header, 'page', $result['sartname']->id, ['tr' => 'Teknik şartname hazırlayıcı', 'en' => 'Specification writer'], 4, $parent->id);

        $company = Menu::where('key', 'footer-kurumsal')->first();
        $this->item($company, 'page', $result['tork']->id, ['tr' => 'Tork hesaplayıcı', 'en' => 'Torque calculator'], 3);
        $this->item($company, 'page', $result['silindir']->id, ['tr' => 'Silindir seçici', 'en' => 'Cylinder selector'], 4);

        return $result;
    }

    // ── Yardımcılar ──────────────────────────────────────────────────────

    /**
     * Tekrarlayıcı satırlarına görsel bağlar: önce sahne görseli, o yoksa
     * verilen model kodlu ürünün fotoğrafı. Sahneler sonradan gelince
     * komut yeniden çalıştırıldığında yerlerini alırlar.
     *
     * @param  array<int, array{0: ?string, 1: ?string}>  $sources  satır => [sahne, model kodu]
     */
    private function withImages(array $items, array $sources): array
    {
        foreach ($sources as $index => [$scene, $code]) {
            if (! isset($items[$index])) {
                continue;
            }

            $items[$index]['image'] = ($scene ? ($this->scenes[$scene] ?? null) : null)
                ?? ($code ? $this->productImage($code) : null);
        }

        return $items;
    }

    private function productImage(string $code): ?int
    {
        return \App\Models\ContentItem::whereNotNull('featured_image_id')
            ->whereHas('translations', fn ($q) => $q->where('locale', 'tr')->where('custom->model_kodu', $code))
            ->value('featured_image_id');
    }

    private function defaultsOf(string $widget): array
    {
        return (new $widget)->defaults()['items'] ?? [];
    }

    private function saveTemplate(string $name, string $type, array $tree): Template
    {
        [$data, $css] = Blocks::compile($tree);

        $template = Template::updateOrCreate(['name' => $name, 'type' => $type], [
            'builder_data' => $data,
            'compiled_css' => $css,
            'is_global' => true,
            'is_default' => true,
            'category' => 'Site',
        ]);

        Template::where('type', $type)->whereKeyNot($template->id)->update(['is_default' => false]);

        return $template;
    }

    private function resetMenu(string $key, string $name): Menu
    {
        $menu = Menu::firstOrCreate(['key' => $key], ['name' => $name]);
        MenuItem::where('menu_id', $menu->id)->delete();

        return $menu;
    }

    private function item(Menu $menu, string $type, ?int $targetId, array $label, int $sort, ?int $parentId = null, ?string $url = null, bool $mega = false): MenuItem
    {
        return MenuItem::create([
            'is_mega' => $mega,
            'menu_id' => $menu->id,
            'parent_id' => $parentId,
            'type' => $type,
            'target_id' => $targetId,
            'url' => $url,
            'label' => $label,
            'sort' => $sort,
            'is_active' => true,
        ]);
    }

    private function folder(string $name): int
    {
        return MediaFolder::firstOrCreate(['parent_id' => null, 'slug' => Str::slug($name)], ['name' => $name])->id;
    }

    private function upload(string $path, string $name, int $folder): ?Media
    {
        if (! is_file($path)) {
            return null;
        }

        return $this->uploader->store(new UploadedFile($path, $name, null, null, true), $folder);
    }
}
