<?php

namespace App\Console\Commands;

use App\Models\ContentItem;
use App\Models\ContentType;
use App\Models\Form;
use App\Models\MediaFolder;
use App\Models\Setting;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Models\Translation;
use App\Services\MediaUploader;
use App\Support\Theme;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Rimpy kurulumunu Mimar'ın üzerine kurar.
 *
 * Tekrar çalıştırılabilir: var olan kayıtlar model koduna, kategori adresine
 * ve form anahtarına göre bulunup güncellenir, kopyası oluşmaz. Katalog ve
 * görseller `database/data/rimpy/` altından okunur.
 */
class RimpyKurCommand extends Command
{
    protected $signature = 'rimpy:kur
        {--gorselsiz : Ürün görsellerini aktarma}
        {--sayfalari-koru : Üst başlık, alt bilgi ve ana sayfayı yeniden yazma (panelde düzenlendiyse)}
        {--sadece-gorseller : Yalnızca görseli olmayan ürünlere yeni gelen görselleri bağla}';

    protected $description = 'Rimpy temasını, ürün alanlarını, kategorileri, ürünleri ve teklif formunu kurar';

    private const DATA = 'database/data/rimpy';

    /**
     * Ürün alanları. Sayısal olanlar filtrelerde, tork hesaplayıcıda ve
     * silindir seçicide kullanılır; o yüzden metin değil sayı tutulur.
     */
    private const FIELDS = [
        ['key' => 'model_kodu', 'type' => 'text', 'label' => ['tr' => 'Model kodu', 'en' => 'Model code']],
        ['key' => 'ozellikler', 'type' => 'textarea', 'label' => ['tr' => 'Teknik özellikler (her satıra "Etiket: Değer")', 'en' => 'Specifications (one "Label: Value" per line)']],
        ['key' => 'tork_maks_nm', 'type' => 'number', 'label' => ['tr' => 'Maks. tork (N·m)', 'en' => 'Max. torque (N·m)']],
        ['key' => 'tork_min_nm', 'type' => 'number', 'label' => ['tr' => 'Min. tork (N·m)', 'en' => 'Min. torque (N·m)']],
        ['key' => 'kare_tahrik', 'type' => 'text', 'label' => ['tr' => 'Kare tahrik', 'en' => 'Square drive']],
        ['key' => 'altigen_aralik', 'type' => 'text', 'label' => ['tr' => 'Altıgen aralığı (mm, örn. 41-95)', 'en' => 'Hex range (mm, e.g. 41-95)']],
        ['key' => 'basinc_bar', 'type' => 'number', 'label' => ['tr' => 'Çalışma basıncı (bar)', 'en' => 'Working pressure (bar)']],
        ['key' => 'kapasite_ton', 'type' => 'number', 'label' => ['tr' => 'Kapasite (ton)', 'en' => 'Capacity (t)']],
        ['key' => 'strok_mm', 'type' => 'number', 'label' => ['tr' => 'Strok (mm)', 'en' => 'Stroke (mm)']],
        ['key' => 'germe_yuk_kn', 'type' => 'number', 'label' => ['tr' => 'Maks. germe yükü (kN)', 'en' => 'Max. tensioning load (kN)']],
        ['key' => 'civata_aralik', 'type' => 'text', 'label' => ['tr' => 'Cıvata aralığı (örn. M42-M52)', 'en' => 'Bolt range (e.g. M42-M52)']],
        ['key' => 'yag_hacmi_cm3', 'type' => 'number', 'label' => ['tr' => 'Silindir yağ hacmi (cm³)', 'en' => 'Cylinder oil capacity (cm³)']],
        ['key' => 'kullanilabilir_yag_cm3', 'type' => 'number', 'label' => ['tr' => 'Pompa kullanılabilir yağ (cm³)', 'en' => 'Pump usable oil (cm³)']],
        ['key' => 'etki', 'type' => 'text', 'label' => ['tr' => 'Etki (tek / çift)', 'en' => 'Action (single / double)']],
        ['key' => 'agirlik_kg', 'type' => 'number', 'label' => ['tr' => 'Ağırlık (kg)', 'en' => 'Weight (kg)']],
        ['key' => 'stok_durumu', 'type' => 'text', 'label' => ['tr' => 'Stok durumu', 'en' => 'Stock status']],
        ['key' => 'teslim_suresi', 'type' => 'text', 'label' => ['tr' => 'Tahmini teslim süresi', 'en' => 'Estimated lead time']],
        ['key' => 'muadil', 'type' => 'textarea', 'label' => ['tr' => 'Muadil modeller (her satıra bir tane)', 'en' => 'Equivalent models (one per line)']],
        ['key' => 'baslangic_fiyati', 'type' => 'text', 'label' => ['tr' => 'Başlangıç fiyatı (boşsa gösterilmez)', 'en' => 'Starting price (hidden if empty)']],
        ['key' => 'rozet', 'type' => 'text', 'label' => ['tr' => 'Rozet (örn. Yeni, Stokta)', 'en' => 'Badge']],
        ['key' => 'belge_test', 'type' => 'url', 'label' => ['tr' => 'Test sertifikası (bağlantı)', 'en' => 'Test certificate (link)']],
        ['key' => 'belge_ce', 'type' => 'url', 'label' => ['tr' => 'CE uygunluk beyanı (bağlantı)', 'en' => 'CE declaration (link)']],
        ['key' => 'belge_kalibrasyon', 'type' => 'url', 'label' => ['tr' => 'Kalibrasyon sertifikası (bağlantı)', 'en' => 'Calibration certificate (link)']],
        ['key' => 'belge_katalog', 'type' => 'url', 'label' => ['tr' => 'Ürün kataloğu PDF (bağlantı)', 'en' => 'Product catalogue PDF (link)']],
    ];

    /** Teknik özellik etiketinden sayısal alana eşleme. */
    private const SPEC_MAP = [
        'Maks. tork' => 'tork_maks_nm',
        'Min. tork' => 'tork_min_nm',
        'Kare tahrik' => 'kare_tahrik',
        'Altıgen aralığı' => 'altigen_aralik',
        'Ölçü aralığı' => 'altigen_aralik',
        'Çalışma basıncı' => 'basinc_bar',
        'Basınç' => 'basinc_bar',
        'Kapasite' => 'kapasite_ton',
        'Strok' => 'strok_mm',
        'Yağ hacmi' => 'yag_hacmi_cm3',
        'Kullanılabilir yağ' => 'kullanilabilir_yag_cm3',
        'Ağırlık' => 'agirlik_kg',
        'Maks. yük' => 'germe_yuk_kn',
        'Cıvata' => 'civata_aralik',
    ];

    public function handle(MediaUploader $uploader): int
    {
        $catalogue = json_decode(file_get_contents(base_path(self::DATA.'/katalog.json')), true, flags: JSON_THROW_ON_ERROR);

        if ($this->option('sadece-gorseller')) {
            return $this->attachNewImages($uploader, $catalogue);
        }

        $this->components->task('Site ayarları ve tema', fn () => $this->settings());
        $this->components->task('Ürün alanları', fn () => $this->productType());
        $this->components->task('Teklif formu', fn () => $this->quoteForm());
        $this->components->task('Cıvata germe talep formu', fn () => $this->tensionForm());
        $this->components->task('Arayüz metinleri', fn () => $this->uiStrings());

        $terms = [];
        $this->components->task('Kategoriler', function () use ($catalogue, &$terms) {
            $terms = $this->categories($catalogue['kategoriler']);
        });

        $type = ContentType::where('key', 'urunler')->firstOrFail();
        $folder = MediaFolder::firstOrCreate(['parent_id' => null, 'slug' => 'urunler'], ['name' => 'Ürünler'])->id;
        $missing = [];

        $bar = $this->output->createProgressBar(count($catalogue['urunler']));
        $bar->start();

        foreach ($catalogue['urunler'] as $index => $product) {
            $item = $this->product($type, $product, $terms, $index);

            if (! $this->option('gorselsiz')) {
                $image = $this->image($uploader, $product['kod'], $product['ad'], $folder);
                $image ? $item->update(['featured_image_id' => $image]) : $missing[] = $product['kod'];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($missing && ! $this->option('gorselsiz')) {
            $this->components->warn('Görseli olmayan ürünler: '.implode(', ', $missing));
        }

        $composer = app(\App\Import\RimpyComposer::class);

        $this->components->task('Logo ve site ikonu', fn () => $composer->brand());
        $this->components->task('Sahne görselleri', fn () => $composer->scenes());
        $this->components->task('Menüler', fn () => $composer->menus());
        $this->components->task('Mühendislik araçları', fn () => $composer->toolPages());

        if (! $this->option('sayfalari-koru')) {
            $this->components->task('Üst başlık', fn () => $composer->header());
            $this->components->task('Alt bilgi', fn () => $composer->footer());
            $this->components->task('Ana sayfa', fn () => $composer->homePage());
        }

        $this->components->info(count($catalogue['urunler']).' ürün hazır.');

        return self::SUCCESS;
    }

    /**
     * Görseller üretildikçe çalıştırılır: yalnızca henüz görseli olmayan
     * ürünlere bakar, başka hiçbir şeye dokunmaz.
     */
    private function attachNewImages(MediaUploader $uploader, array $catalogue): int
    {
        $type = ContentType::where('key', 'urunler')->firstOrFail();
        $folder = MediaFolder::firstOrCreate(['parent_id' => null, 'slug' => 'urunler'], ['name' => 'Ürünler'])->id;
        $attached = [];

        foreach ($catalogue['urunler'] as $product) {
            $item = ContentItem::where('content_type_id', $type->id)->whereNull('featured_image_id')
                ->whereHas('translations', fn ($q) => $q->where('locale', 'tr')->where('custom->model_kodu', $product['kod']))
                ->first();

            if ($item && ($image = $this->image($uploader, $product['kod'], $product['ad'], $folder))) {
                $item->update(['featured_image_id' => $image]);
                $attached[] = $product['kod'];
            }
        }

        $this->line($attached ? 'Bağlandı: '.implode(', ', $attached) : 'Yeni görsel yok.');

        return self::SUCCESS;
    }

    private function settings(): void
    {
        Setting::put('general', 'site_name', 'Rimpy');
        Setting::put('contact', 'email', 'info@rimpy.com.tr');

        // Açık, ferah zemin; koyu bölümler (açılış, hizmetler, alt bilgi)
        // local.css'teki .tone-dark ile kendi jetonlarını taşır. Tek vurgu
        // logodaki sarı.
        Setting::putGroup('theme', array_replace_recursive(Theme::DEFAULTS, Setting::get('theme', []) ?: [], [
            'colors' => [
                'primary' => '#FDC500',
                'secondary' => '#121418',
                'accent' => '#8A6A00',
                'text' => '#3B3E44',
                'muted' => '#6E727A',
                'heading' => '#121418',
                'background' => '#F5F4F0',
                'surface' => '#FFFFFF',
                'border' => '#E1DED6',
            ],
            'fonts' => [
                'heading' => ['family' => 'Archivo', 'weights' => [600, 700, 800], 'width' => '62..125'],
                'body' => ['family' => 'Archivo', 'weights' => [400, 500, 600], 'width' => '62..125'],
            ],
            'layout' => ['container' => 1320, 'radius' => 4, 'section_padding' => 120],
        ]));
    }

    private function productType(): void
    {
        $type = ContentType::where('key', 'urunler')->firstOrFail();

        $type->update([
            'supports' => array_values(array_unique([...$type->supports ?? [], 'image', 'gallery', 'excerpt', 'body', 'seo', 'taxonomies'])),
            'fields' => self::FIELDS,
            'has_archive' => true,
            'is_active' => true,
        ]);

        Taxonomy::where('content_type_id', $type->id)->where('key', 'kategori')->update(['is_hierarchical' => true]);
    }

    private function quoteForm(): void
    {
        Form::updateOrCreate(['key' => config('cms.quote.form_key')], [
            'name' => 'Teklif Talebi',
            'fields' => [
                ['key' => 'ad_soyad', 'type' => 'text', 'label' => ['tr' => 'Ad soyad', 'en' => 'Full name'], 'required' => true, 'width' => 50],
                ['key' => 'firma', 'type' => 'text', 'label' => ['tr' => 'Firma', 'en' => 'Company'], 'required' => true, 'width' => 50],
                ['key' => 'telefon', 'type' => 'tel', 'label' => ['tr' => 'Telefon', 'en' => 'Phone'], 'required' => true, 'width' => 50],
                ['key' => 'eposta', 'type' => 'email', 'label' => ['tr' => 'E-posta', 'en' => 'Email'], 'required' => true, 'width' => 50],
                ['key' => 'not', 'type' => 'textarea', 'label' => ['tr' => 'Proje notu: cıvata ölçüsü, tork, teslim yeri…', 'en' => 'Project note: bolt size, torque, delivery site…'], 'required' => false, 'width' => 100, 'rows' => 3],
                ['key' => 'onay', 'type' => 'checkbox', 'label' => ['tr' => 'Kişisel verilerimin teklif için işlenmesini kabul ediyorum.', 'en' => 'I agree to the processing of my data for this quote.'], 'required' => true, 'width' => 100],
            ],
            'submit_label' => ['tr' => 'Teklif talebini gönder', 'en' => 'Send quote request'],
            'success_message' => [
                'tr' => 'Talebiniz alındı. Mühendislerimiz 24 saat içinde size özel teklifle dönecek.',
                'en' => 'Your request has been received. Our engineers will reply with a quote within 24 hours.',
            ],
            'notify_emails' => 'info@rimpy.com.tr',
            'store_submissions' => true,
            'spam_protection' => true,
        ]);
    }

    /**
     * Kritik cıvata germe uygulamaları için bilgi formu. Alanlar Nord-Lock /
     * Boltight'ın uygulama kontrol listesindeki başlıkları izler: cıvata,
     * yük, somun, flanş, çalışma alanı, sıcaklık ve proje aşaması.
     */
    private function tensionForm(): void
    {
        $option = fn (string $value, string $tr, string $en) => ['value' => $value, 'label' => ['tr' => $tr, 'en' => $en]];
        $field = fn (string $key, string $type, string $tr, string $en, int $width = 50, bool $required = false, array $extra = []) => [
            'key' => $key, 'type' => $type, 'label' => ['tr' => $tr, 'en' => $en], 'required' => $required, 'width' => $width,
        ] + $extra;

        Form::updateOrCreate(['key' => 'germe-talebi'], [
            'name' => 'Cıvata Germe Uygulama Talebi',
            'fields' => [
                // İletişim
                $field('ad_soyad', 'text', 'Ad soyad', 'Full name', 50, true),
                $field('firma', 'text', 'Firma', 'Company', 50, true),
                $field('telefon', 'tel', 'Telefon', 'Phone', 50, true),
                $field('eposta', 'email', 'E-posta', 'Email', 50, true),
                $field('proje', 'text', 'Proje veya uygulama adı', 'Project or application reference', 100),
                // 1–2. Cıvata ve yük
                $field('civata', 'text', 'Cıvata çapı ve diş adımı (örn. M48×5, 1-7/8" 8UN)', 'Bolt diameter and pitch (e.g. M48×5, 1-7/8" 8UN)', 50, true),
                $field('civata_malzeme', 'text', 'Cıvata malzemesi / kalitesi (örn. B7, 10.9)', 'Bolt material / grade (e.g. B7, 10.9)', 50),
                $field('yuk', 'text', 'Gerekli yük veya tork, birimiyle (örn. 450 kN)', 'Required load or torque, with unit (e.g. 450 kN)', 50, true),
                $field('yuk_tipi', 'select', 'Bu değer hangi yük?', 'Which load is this?', 50, false, ['options' => [
                    $option('kalici', 'Kalıcı (germe sonrası) yük', 'Residual load after tensioning'),
                    $option('uygulanan', 'Germe anında uygulanan yük', 'Load applied during tensioning'),
                    $option('alet', 'Alet yükü / basıncı', 'Tool load / pressure'),
                ]]),
                $field('yaglayici', 'text', 'Yağlayıcı kullanılıyor mu? Kullanılıyorsa tipi', 'Is a lubricant used? If so, which type', 100),
                // 3. Somun
                $field('somun_tipi', 'select', 'Somun tipi', 'Nut type', 50, false, ['options' => [
                    $option('altigen', 'Altıgen', 'Hex'),
                    $option('yuvarlak', 'Yuvarlak', 'Round'),
                    $option('delikli', 'Delikli (pimli)', 'Drilled'),
                    $option('diger', 'Diğer — nota yazın', 'Other — describe in the note'),
                ]]),
                $field('somun_olcu', 'text', 'Somun anahtar ağzı / dış çapı / yüksekliği (mm)', 'Nut across flats / OD / height (mm)', 50),
                // 4. Flanş
                $field('civata_adedi', 'number', 'Cıvata adedi', 'Number of bolts', 50),
                $field('bcd', 'number', 'Cıvata dairesi çapı, BCD (mm)', 'Bolt circle diameter, BCD (mm)', 50),
                $field('sikma_boyu', 'number', 'Sıkma boyu (mm)', 'Grip length (mm)', 50),
                $field('civata_boyu', 'number', 'Cıvata / saplama boyu (mm)', 'Bolt / stud length (mm)', 50),
                // 5. Çalışma alanı
                $field('ust_bosluk', 'number', 'Somun üstünde kullanılabilir yükseklik (mm)', 'Usable height above the nut (mm)', 50),
                $field('yan_bosluk', 'number', 'Cıvata ekseninden en yakın engele mesafe (mm)', 'Distance from bolt axis to nearest obstruction (mm)', 50),
                $field('rondela', 'text', 'Rondela çapı, kalınlığı ve malzemesi (varsa)', 'Washer diameter, thickness and material (if any)', 100),
                // 6. Sıcaklık ve tercih
                $field('sicaklik', 'text', 'Çalışma sıcaklığı aralığı (°C)', 'Operating temperature range (°C)', 50),
                $field('yontem', 'select', 'Tercih edilen yöntem', 'Preferred method', 50, false, ['options' => [
                    $option('hidrolik', 'Hidrolik germe', 'Hydraulic tensioning'),
                    $option('mekanik', 'Mekanik germe (çok cıvatalı somun)', 'Mechanical tensioning (multi-jackbolt nut)'),
                    $option('tork', 'Hidrolik tork', 'Hydraulic torque'),
                    $option('farketmez', 'Fark etmez', 'No preference'),
                ]]),
                // Proje
                $field('asama', 'select', 'Proje aşaması', 'Project phase', 50, false, ['options' => [
                    $option('butce', 'Bütçe / ön çalışma', 'Budget / concept'),
                    $option('tasarim', 'Tasarım', 'Design'),
                    $option('siparis', 'Sipariş', 'Order'),
                ]]),
                $field('miktar', 'select', 'Miktar', 'Quantity', 50, false, ['options' => [
                    $option('tek', 'Tek seferlik iş', 'One-off job'),
                    $option('prototip', 'Prototip / deneme', 'Prototype / trial'),
                    $option('seri', 'Yıllık tekrar eden', 'Recurring every year'),
                    $option('kiralama', 'Kiralama / saha hizmeti', 'Rental / site service'),
                ]]),
                $field('resim', 'file', 'Teknik resim veya fotoğraf (PDF, JPG)', 'Drawing or photo (PDF, JPG)', 100),
                $field('not', 'textarea', 'Eklemek istedikleriniz: ortam, kaplama, kalite gereksinimi, teslim tarihi…', 'Anything else: environment, coating, quality requirements, target date…', 100, false, ['rows' => 4]),
                $field('onay', 'checkbox', 'Kişisel verilerimin bu talep için işlenmesini kabul ediyorum.', 'I agree to the processing of my data for this request.', 100, true),
            ],
            'submit_label' => ['tr' => 'Talebi gönder', 'en' => 'Send request'],
            'success_message' => [
                'tr' => 'Talebiniz alındı. Uygulama mühendisimiz hesabı yapıp 2 iş günü içinde size dönecek.',
                'en' => 'Your request has been received. Our application engineer will run the calculation and reply within 2 working days.',
            ],
            'notify_emails' => 'info@rimpy.com.tr',
            'store_submissions' => true,
            'spam_protection' => true,
        ]);
    }

    private function uiStrings(): void
    {
        $strings = [
            'quote_cart' => ['tr' => 'Teklif sepeti', 'en' => 'Quote cart'],
            'quote_add' => ['tr' => 'Teklif sepetine ekle', 'en' => 'Add to quote'],
            'quote_added' => ['tr' => 'teklif sepetine eklendi', 'en' => 'added to your quote'],
            'quote_view' => ['tr' => 'Sepeti gör', 'en' => 'View quote'],
            'quote_remove' => ['tr' => 'Kaldır', 'en' => 'Remove'],
            'quote_less' => ['tr' => 'Azalt', 'en' => 'Decrease'],
            'quote_more' => ['tr' => 'Arttır', 'en' => 'Increase'],
            'quote_in_cart' => ['tr' => 'Sepette', 'en' => 'In quote'],
            'quote_empty' => ['tr' => 'Sepetiniz boş. Ürünleri ekleyebilir ya da yalnızca ihtiyacınızı yazıp gönderebilirsiniz.', 'en' => 'Your quote is empty. Add products or just describe what you need.'],
            'ask_engineer' => ['tr' => 'Mühendise sor', 'en' => 'Ask an engineer'],
            'specifications' => ['tr' => 'Teknik özellikler', 'en' => 'Specifications'],
            'equivalents' => ['tr' => 'Muadil modeller', 'en' => 'Equivalent models'],
            'documents' => ['tr' => 'Belgeler', 'en' => 'Documents'],
            'stock' => ['tr' => 'Stok', 'en' => 'Stock'],
            'lead_time' => ['tr' => 'Teslim', 'en' => 'Lead time'],
            'starting_price' => ['tr' => 'Başlangıç fiyatı', 'en' => 'From'],
            'related_products' => ['tr' => 'Benzer ürünler', 'en' => 'Related products'],
            'category' => ['tr' => 'Kategori', 'en' => 'Category'],
            'skip_to_content' => ['tr' => 'İçeriğe geç', 'en' => 'Skip to content'],
        ];

        foreach ($strings as $key => $values) {
            Translation::updateOrCreate(['group' => 'site', 'key' => $key], ['values' => $values]);
        }
    }

    /** @return array<string, int>  kategori adresi => terim kimliği */
    private function categories(array $tree): array
    {
        $taxonomy = Taxonomy::whereHas('contentType', fn ($q) => $q->where('key', 'urunler'))
            ->where('key', 'kategori')->firstOrFail();

        $map = [];

        $save = function (array $node, ?int $parentId, int $sort) use ($taxonomy, &$map): int {
            $term = Term::where('taxonomy_id', $taxonomy->id)
                ->whereHas('translations', fn ($q) => $q->where('locale', 'tr')->where('slug', $node['slug']))
                ->first()
                ?? Term::create(['taxonomy_id' => $taxonomy->id]);

            $term->update(['parent_id' => $parentId, 'sort' => $sort, 'is_active' => true]);
            $term->fillTranslation('tr', ['name' => $node['ad'], 'slug' => $node['slug']]);
            $term->fillTranslation('en', ['name' => $node['en'], 'slug' => Str::slug($node['en'])]);

            return $map[$node['slug']] = $term->id;
        };

        foreach ($tree as $i => $root) {
            $rootId = $save($root, null, $i);

            foreach ($root['alt'] ?? [] as $j => $child) {
                $save($child, $rootId, $j);
            }
        }

        return $map;
    }

    private function product(ContentType $type, array $product, array $terms, int $index): ContentItem
    {
        $item = ContentItem::where('content_type_id', $type->id)
            ->whereHas('translations', fn ($q) => $q->where('locale', 'tr')->where('custom->model_kodu', $product['kod']))
            ->first()
            ?? ContentItem::create(['content_type_id' => $type->id, 'status' => 'published', 'published_at' => now()]);

        $item->update([
            'is_featured' => (bool) ($product['one_cikan'] ?? false),
            'sort' => $index,
        ]);

        $custom = $this->customFields($product);

        $item->fillTranslation('tr', [
            'title' => $product['ad'],
            'slug' => Str::slug($product['kod'].' '.$product['ad'], '-', 'tr'),
            'excerpt' => $product['kisa'],
            'custom' => $custom,
            'seo_title' => "{$product['ad']} {$product['kod']} | Rimpy",
            'seo_description' => Str::limit($product['kisa'], 155),
        ]);

        $item->fillTranslation('en', [
            'title' => $product['en'],
            'slug' => Str::slug($product['kod'].' '.$product['en']),
            'excerpt' => $product['en_kisa'] ?? null,
            'custom' => $this->englishCustom($custom),
            'seo_title' => "{$product['en']} {$product['kod']} | Rimpy",
            'seo_description' => Str::limit($product['en_kisa'] ?? '', 155),
        ]);

        // Kategori ve üst kategorisi birlikte bağlanır; üst kategori sayfası
        // da alt kategorilerdeki ürünleri listeler.
        $termId = $terms[$product['kategori']] ?? null;
        $ids = array_filter([$termId, $termId ? Term::find($termId)?->parent_id : null]);
        $item->terms()->sync($ids);

        return $item;
    }

    private function customFields(array $product): array
    {
        $custom = [
            'model_kodu' => $product['kod'],
            'ozellikler' => collect($product['ozellikler'])->map(fn ($row) => "{$row[0]}: {$row[1]}")->implode("\n"),
            'rozet' => $product['rozet'] ?? null,
            'muadil' => implode("\n", $product['muadil'] ?? []),
            'stok_durumu' => ($product['rozet'] ?? null) === 'Stokta' || ! empty($product['one_cikan']) ? 'Stokta' : 'Siparişe göre',
            'teslim_suresi' => ($product['rozet'] ?? null) === 'Stokta' || ! empty($product['one_cikan']) ? '1–2 iş günü' : '2–4 hafta',
            'etki' => str_contains($product['ad'], 'Çift Etkili') ? 'çift' : (str_contains($product['ad'], 'Silindir') ? 'tek' : null),
        ];

        foreach ($product['ozellikler'] as [$label, $value]) {
            if (! isset(self::SPEC_MAP[$label])) {
                continue;
            }

            $key = self::SPEC_MAP[$label];
            $custom[$key] = in_array($key, ['kare_tahrik', 'altigen_aralik', 'civata_aralik'], true)
                ? $this->range($value)
                : $this->number($value);
        }

        return array_filter($custom, fn ($v) => $v !== null && $v !== '');
    }

    // ── İngilizce ────────────────────────────────────────────────────────

    private const EN_LABELS = [
        'Akü' => 'Battery', 'Akışkan' => 'Medium', 'Altıgen aralığı' => 'Hex range', 'Aralık' => 'Range',
        'Ayırma kuvveti' => 'Spreading force', 'Ağırlık' => 'Weight', 'Basınç' => 'Pressure', 'Bağlantı' => 'Connection',
        'Başlık yüksekliği' => 'Head height', 'Besleme' => 'Power supply', 'Cıvata' => 'Bolt', 'Cıvata aralığı' => 'Bolt range',
        'Cıvata deliği' => 'Bolt hole', 'Debi (yüksek bas.)' => 'Flow (high press.)', 'Dolgu' => 'Fill', 'Doğruluk' => 'Accuracy',
        'Düzeltme' => 'Correction', 'Emniyet' => 'Safety factor', 'Erişim' => 'Reach', 'Giriş' => 'Input', 'Gövde' => 'Body',
        'Hassasiyet' => 'Accuracy', 'Hava basıncı' => 'Air pressure', 'Hizalama kuvveti' => 'Alignment force', 'Hız' => 'Speed',
        'Kademe' => 'Stages', 'Kadran' => 'Dial', 'Kaldırma kolu' => 'Lifting handle', 'Kapalı yükseklik' => 'Collapsed height',
        'Kapasite' => 'Capacity', 'Kaplama' => 'Finish', 'Kare tahrik' => 'Square drive', 'Keski' => 'Chisel',
        'Kesme kuvveti' => 'Cutting force', 'Kol kuvveti' => 'Handle effort', 'Kol sayısı' => 'Jaws', 'Kontrol' => 'Control',
        'Kullanılabilir yağ' => 'Usable oil', 'Kullanım' => 'Application', 'Maks. açıklık' => 'Max. spread', 'Maks. açılma' => 'Max. opening',
        'Maks. tork' => 'Max. torque', 'Maks. yük' => 'Max. load', 'Maks. çıkış' => 'Max. output', 'Malzeme' => 'Material',
        'Manometre' => 'Gauges', 'Min. tork' => 'Min. torque', 'Min. yükseklik' => 'Min. height', 'Motor' => 'Motor', 'Oran' => 'Ratio',
        'Parça' => 'Pieces', 'Pompa' => 'Pump', 'Port' => 'Ports', 'Somun ölçüsü' => 'Nut size', 'Standart' => 'Standard',
        'Strok' => 'Stroke', 'Sıkma / akü' => 'Cycles / battery', 'Tahrik' => 'Drive', 'Tank' => 'Tank', 'Test basıncı' => 'Test pressure',
        'Uzunluk' => 'Length', 'Uç boşluğu' => 'Tip clearance', 'Valf' => 'Valve', 'Yağ hacmi' => 'Oil capacity',
        'Çalışma basıncı' => 'Working pressure', 'Çerçeve' => 'Frame', 'Çıkış' => 'Output', 'Ölçü aralığı' => 'Size range', 'İç çap' => 'Inner diameter',
    ];

    private const EN_VALUES = [
        '4/3 manuel' => '4/3 manual', 'dev/dk' => 'rpm', 'L/dk' => 'L/min', 'Alüminyum' => 'Aluminium', 'Ayak pedalı' => 'Foot pedal',
        'Değiştirilebilir' => 'Replaceable', 'Entegre' => 'Integrated', 'Flanş, kule' => 'Flanges, towers', 'Fosfat' => 'Phosphate',
        'Gliserin' => 'Glycerin', 'Kompozit' => 'Composite', 'Paslanmaz' => 'Stainless', 'Su / yağ' => 'Water / oil', 'Sınıf' => 'Class',
        'Termoplastik' => 'Thermoplastic', 'Var' => 'Yes', 'Çelik' => 'Steel',
        'Stokta' => 'In stock', 'Siparişe göre' => 'Made to order', 'iş günü' => 'business days', 'hafta' => 'weeks',
        'Çok satan' => 'Best seller', 'Yeni' => 'New', 'Dar alan' => 'Tight access', 'Kısa boy' => 'Low height',
    ];

    /** Türkçe özel alanların İngilizce karşılığı; sayısal alanlar aynen kalır. */
    private function englishCustom(array $custom): array
    {
        $out = $custom;

        $out['ozellikler'] = collect(preg_split('/\R/u', $custom['ozellikler'] ?? ''))
            ->filter(fn ($line) => str_contains($line, ':'))
            ->map(function ($line) {
                [$label, $value] = array_map('trim', explode(':', $line, 2));

                return (self::EN_LABELS[$label] ?? $label).': '.$this->englishValue($value);
            })
            ->implode("\n");

        // "etki" çevrilmez: silindir seçici bu değeri (tek / çift) makine değeri olarak okur.
        foreach (['stok_durumu', 'teslim_suresi', 'rozet'] as $key) {
            if (isset($custom[$key])) {
                $out[$key] = $this->englishValue($custom[$key]);
            }
        }

        return $out;
    }

    /** "11.500 N·m" → "11,500 N·m", "0,8 L/dk" → "0.8 L/min" */
    private function englishValue(string $value): string
    {
        $value = preg_replace_callback('/\d[\d.,]*/', fn ($m) => strtr($m[0], ['.' => ',', ',' => '.']), $value);

        // Uzun ifadeler önce: "Siparişe göre" içindeki parçalar ayrıca çevrilmesin.
        $map = self::EN_VALUES;
        uksort($map, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));

        return strtr($value, $map);
    }

        /** "11.500 N·m" → 11500, "0,8 L/dk" → 0.8 */
    private function number(string $value): ?float
    {
        if (! preg_match('/\d[\d.]*(,\d+)?/', $value, $m)) {
            return null;
        }

        return (float) str_replace(['.', ','], ['', '.'], $m[0]);
    }

    /** "41–95 mm" → "41-95"; kare tahrik olduğu gibi kalır. */
    private function range(string $value): string
    {
        return trim(preg_replace(['/\s*mm$/', '/\s*[–—]\s*/u'], ['', '-'], $value));
    }

    private function image(MediaUploader $uploader, string $code, string $title, int $folder): ?int
    {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $extension) {
            $path = base_path(self::DATA."/gorseller/{$code}.{$extension}");

            if (! is_file($path)) {
                continue;
            }

            $media = $uploader->store(new UploadedFile($path, Str::slug($code.' '.$title).'.'.$extension, null, null, true), $folder);
            $media->update(['alt' => ['tr' => $title, 'en' => $title]]);

            return $media->id;
        }

        return null;
    }
}
