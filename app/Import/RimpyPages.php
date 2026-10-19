<?php

namespace App\Import;

use App\Models\Form;
use App\Models\Page;
use App\Models\Setting;

/**
 * Menüdeki kurumsal sayfalar: Kurumsal, Markalar, Hizmetlerimiz (ve alt
 * hizmetler), İletişim.
 *
 * Metinler başlangıç içeriğidir; müşteri her sayfayı kurucuda düzenler.
 * Sayfa bir kez oluşturulduktan sonra komut yeniden çalıştırıldığında
 * içeriğine dokunulmaz, yalnızca bulunup menüye bağlanır.
 */
class RimpyPages
{
    /** @return array<string, Page>  anahtar => sayfa */
    public function all(): array
    {
        $pages = [];

        $pages['kurumsal'] = $this->page('kurumsal', [
            'tr' => ['Kurumsal', 'Rimpy hakkında',
                'Hidrolik tork anahtarları, cıvata gerdirme sistemleri, 700 bar silindir ve pompalarla endüstriyel cıvata bağlantılarına çözüm sunuyoruz.',
                '<p>Rimpy; enerji, petrokimya, çelik yapı, rüzgâr ve ağır sanayi sahalarında çalışan ekiplere doğru tork ve doğru ön yükü sağlayan ekipmanları tedarik eder. Ürün seçiminden saha uygulamasına, kalibrasyondan operatör eğitimine kadar süreç boyunca yanınızdayız.</p><p>Stoktan teslim edilen ürünler, Türkiye\'de servis ve kalibrasyon desteği ile ekipmanınızın her zaman sahaya hazır olmasını hedefliyoruz.</p>'],
            'en' => ['About us', 'About Rimpy', 'company',
                '<p>Rimpy supplies the equipment that gives teams in energy, petrochemical, steel construction, wind and heavy industry the right torque and the right preload. From product selection to site work, from calibration to operator training, we stay with you throughout.</p><p>With delivery from stock and service and calibration in Türkiye, our aim is to keep your equipment ready for the job.</p>'],
        ]);

        $pages['markalar'] = $this->page('markalar', [
            'tr' => ['Markalar', 'Çalıştığımız markalar',
                'Tedarik ettiğimiz ve servis verdiğimiz üreticiler.',
                '<p>Çalıştığımız markalar ve temsil ettiğimiz üreticiler hakkında bilgi almak için bizimle iletişime geçin.</p>'],
            'en' => ['Brands', 'Brands we work with', 'brands',
                '<p>Contact us for information about the brands we supply and the manufacturers we represent.</p>'],
        ]);

        $pages['hizmetler'] = $this->page('hizmetlerimiz', [
            'tr' => ['Hizmetlerimiz', 'Hizmetlerimiz',
                'Ekipmanın yanında, işi sahada doğru yapmak için gereken her şey.',
                '<p>Operatör eğitiminden kalibrasyona, kiralık ekipmandan yerinde cıvata sıkmaya kadar saha ekiplerinizi destekliyoruz.</p>'],
            'en' => ['Services', 'Our services', 'services',
                '<p>From operator training to calibration, from rental equipment to on-site bolting, we support your site teams.</p>'],
        ]);

        $services = [
            'operator-egitimi' => [
                'tr' => ['Operatör Eğitimi', 'Operatör eğitimi',
                    'Hidrolik tork ve gerdirme ekipmanını güvenle ve doğru kullanmak için uygulamalı eğitim.',
                    '<p>Eğitim; ekipman tanıtımı, pompa basıncı ile tork ilişkisi, sıkma sıralaması, reaksiyon noktası seçimi ve iş güvenliğini kapsar. Tesisinizde ya da sahada, kendi ekipmanınızla yapılabilir.</p>'],
                'en' => ['Operator Training', 'Operator training', 'operator-training',
                    '<p>Training covers the equipment, the relation between pump pressure and torque, tightening sequences, choosing the reaction point and safe working. It can be held at your plant or on site, with your own equipment.</p>'],
            ],
            'kalibrasyon-ve-tamir' => [
                'tr' => ['Kalibrasyon ve Tamir', 'Kalibrasyon ve tamir',
                    'Tork anahtarı, pompa ve manometrelerin kalibrasyonu, bakımı ve onarımı.',
                    '<p>Ekipmanınızın doğruluğunu düzenli kalibrasyonla koruyun. Sızdırmazlık elemanı değişimi, periyodik bakım ve arıza onarımı için ekipmanınızı servisimize gönderebilirsiniz.</p>'],
                'en' => ['Calibration and Repair', 'Calibration and repair', 'calibration-and-repair',
                    '<p>Keep your equipment accurate with regular calibration. Send your tools to our service for seal replacement, periodic maintenance and repair.</p>'],
            ],
            'tork-anahtari-kiralama' => [
                'tr' => ['Tork Anahtarı Kiralama', 'Tork anahtarı kiralama',
                    'Kısa süreli projeler ve duruşlar için hidrolik tork anahtarı, pompa ve lokma setleri.',
                    '<p>Bakım duruşu ya da tek seferlik bir proje için ekipman satın almanız gerekmez. İhtiyacınız olan tork aralığını ve süreyi bildirin, uygun seti hazırlayalım.</p>'],
                'en' => ['Torque Wrench Rental', 'Torque wrench rental', 'torque-wrench-rental',
                    '<p>No need to buy equipment for a shutdown or a one-off project. Tell us the torque range and the period, and we will prepare the right kit.</p>'],
            ],
            'yerinde-civata-sikma' => [
                'tr' => ['Yerinde Cıvata Sıkma', 'Yerinde cıvata sıkma',
                    'Flanş, kule ve makine bağlantılarında sahada kontrollü sıkma ve gerdirme.',
                    '<p>Ekibimiz ekipmanıyla sahaya gelir; cıvata bağlantılarını belirlenen tork ya da ön yük değerinde, doğru sıralamayla sıkar ve her bağlantıyı kayıt altına alır.</p>'],
                'en' => ['On-site Bolting', 'On-site bolting', 'on-site-bolting',
                    '<p>Our team comes to site with the equipment, tightens bolted joints to the specified torque or preload in the right sequence and records every joint.</p>'],
            ],
            'bina-ve-kopru-kaldirma' => [
                'tr' => ['Bina ve Köprü Kaldırma', 'Bina ve köprü kaldırma',
                    'Senkron hidrolik silindirlerle yapı kaldırma, mesnet değişimi ve konumlandırma.',
                    '<p>Köprü mesnet değişimi, yapı kaldırma ve ağır yük konumlandırma işlerinde silindir, pompa ve kontrol ekipmanının seçimi ve uygulaması için bizimle görüşün.</p>'],
                'en' => ['Building and Bridge Lifting', 'Building and bridge lifting', 'building-and-bridge-lifting',
                    '<p>Talk to us about selecting and applying cylinders, pumps and control equipment for bridge bearing replacement, structure lifting and heavy load positioning.</p>'],
            ],
        ];

        foreach ($services as $slug => $locales) {
            $pages['hizmet:'.$slug] = $this->page($slug, $locales, $pages['hizmetler']);
        }

        $pages['iletisim'] = $this->contact();

        return $pages;
    }

    /**
     * @param  array<string, array{0: string, 1: string, 2: string, 3: string}>  $locales
     *         tr: [başlık, üst başlık, giriş, gövde], en: [başlık, üst başlık, adres, gövde]
     */
    private function page(string $slug, array $locales, ?Page $parent = null): Page
    {
        if ($page = $this->find($slug)) {
            return $page;
        }

        $page = Page::create([
            'status' => 'published', 'published_at' => now(), 'show_in_search' => true, 'parent_id' => $parent?->id,
        ]);

        foreach ($locales as $locale => $copy) {
            [$title, $eyebrow] = $copy;
            $lead = $locale === 'tr' ? $copy[2] : null;
            $body = $copy[3];

            [$data, $css] = Blocks::compile($this->tree($eyebrow, $title, $lead, $body, $locale));

            $page->fillTranslation($locale, [
                'title' => $title,
                'slug' => $locale === 'tr' ? $slug : $copy[2],
                'builder_data' => $data,
                'compiled_css' => $css,
                'seo_title' => $title.' | Rimpy',
            ]);
        }

        return $page;
    }

    private function contact(): Page
    {
        if ($page = $this->find('iletisim')) {
            return $page;
        }

        $page = Page::create(['status' => 'published', 'published_at' => now(), 'show_in_search' => true]);
        $form = Form::where('key', config('cms.quote.form_key'))->value('id');

        foreach (['tr' => ['İletişim', 'iletisim', 'Bize ulaşın'], 'en' => ['Contact', 'contact', 'Get in touch']] as $locale => [$title, $slug, $eyebrow]) {
            $contact = Setting::get('contact', []) ?: [];
            $items = array_values(array_filter([
                filled($contact['address'][$locale] ?? null) ? ['text' => $contact['address'][$locale], 'icon' => 'map-pin'] : null,
                filled($contact['phone'] ?? null) ? ['text' => $contact['phone'], 'icon' => 'phone', 'link' => ['type' => 'phone', 'phone' => $contact['phone']]] : null,
                filled($contact['email'] ?? null) ? ['text' => $contact['email'], 'icon' => 'mail', 'link' => ['type' => 'email', 'email' => $contact['email']]] : null,
                filled($contact['working_hours'][$locale] ?? null) ? ['text' => $contact['working_hours'][$locale], 'icon' => 'clock'] : null,
            ]));

            [$data, $css] = Blocks::compile([
                Blocks::section(['width' => 'boxed', '_padding' => Blocks::padY(72, 120, [48, 72]), 'gap' => Blocks::size(56), '_css_class' => 'cp'], [
                    Blocks::column(40, [
                        Blocks::widget('heading', ['tag' => 'div', 'text' => $eyebrow, '_css_class' => 'cp-eyebrow']),
                        Blocks::widget('heading', ['tag' => 'h1', 'text' => $title, '_css_class' => 'cp-title']),
                        Blocks::widget('list', ['items' => $items, 'icon_color' => '@accent', 'gap' => Blocks::size(14), '_css_class' => 'cp-contact']),
                    ], [], ['t' => 100, 'm' => 100]),
                    Blocks::column(60, [
                        Blocks::widget('form', ['form_id' => $form, '_css_class' => 'cp-form']),
                    ], [], ['t' => 100, 'm' => 100]),
                ]),
            ]);

            $page->fillTranslation($locale, [
                'title' => $title, 'slug' => $slug, 'builder_data' => $data, 'compiled_css' => $css, 'seo_title' => $title.' | Rimpy',
            ]);
        }

        return $page;
    }

    private function tree(string $eyebrow, string $title, ?string $lead, string $body, string $locale): array
    {
        return [
            Blocks::section(['width' => 'boxed', '_padding' => Blocks::padY(72, 120, [48, 72]), '_css_class' => 'cp'], [
                Blocks::column(100, [
                    Blocks::widget('heading', ['tag' => 'div', 'text' => $eyebrow, '_css_class' => 'cp-eyebrow']),
                    Blocks::widget('heading', ['tag' => 'h1', 'text' => $title, '_css_class' => 'cp-title']),
                    ...($lead ? [Blocks::widget('text', ['body' => '<p>'.e($lead).'</p>', '_css_class' => 'cp-lead'])] : []),
                    Blocks::widget('text', ['body' => $body, '_css_class' => 'cp-body']),
                    Blocks::widget('button', [
                        'text' => $locale === 'en' ? 'Request a quote' : 'Teklif iste',
                        'link' => ['type' => 'anchor', 'anchor' => 'teklif'],
                        'icon' => 'arrow-right',
                        'bg' => '@primary',
                        'color' => '#111111',
                        '_css_class' => 'cp-cta',
                    ]),
                ]),
            ]),
        ];
    }

    private function find(string $slug): ?Page
    {
        return Page::whereHas('translations', fn ($q) => $q->where('locale', 'tr')->where('slug', $slug))->first();
    }
}
