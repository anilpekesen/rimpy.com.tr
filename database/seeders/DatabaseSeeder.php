<?php

namespace Database\Seeders;

use App\Models\ContentType;
use App\Models\Form;
use App\Models\Language;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Taxonomy;
use App\Models\Translation;
use App\Models\User;
use App\Support\Locales;
use App\Support\Theme;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Yeni bir kurulumu çalışır hale getirir.
 *
 * Amaç, müşterinin panele ilk girdiğinde boş bir ekranla karşılaşmaması:
 * diller tanımlı, ana sayfa var, menüler kurulu ve bir iletişim formu hazır
 * gelir. Buradan sonrası tamamen kendi içeriğidir.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->languages();
        $this->settings();
        $this->owner();
        $this->contentTypes();
        $this->menus();
        $this->homePage();
        $this->contactForm();
        $this->uiStrings();

        Locales::forget();
    }

    private function languages(): void
    {
        foreach (config('cms.default_languages') as $index => $language) {
            Language::firstOrCreate(
                ['code' => $language['code']],
                $language + ['sort' => $index, 'is_active' => true]
            );
        }

        Locales::forget();
    }

    private function settings(): void
    {
        Setting::putGroup('general', [
            'site_name' => config('app.name'),
            'tagline' => Locales::map(''),
            'logo_id' => null,
            'logo_light_id' => null,
            'favicon_id' => null,
        ]);

        Setting::putGroup('contact', [
            'email' => config('mail.from.address'),
            'phone' => '',
            'whatsapp' => '',
            'address' => Locales::map(''),
            'map_embed' => '',
            'working_hours' => Locales::map(''),
        ]);

        Setting::putGroup('social', [
            'facebook' => '', 'instagram' => '', 'linkedin' => '',
            'x' => '', 'youtube' => '',
        ]);

        Setting::putGroup('seo', [
            'meta_title' => Locales::map(''),
            'meta_description' => Locales::map(''),
            'og_image_id' => null,
            'google_analytics' => '',
            'google_verification' => '',
            'noindex_site' => false,
        ]);

        Setting::putGroup('theme', Theme::DEFAULTS);
    }

    private function owner(): void
    {
        if (User::where('role', 'owner')->exists()) {
            return;
        }

        User::create([
            'name' => 'Site Sahibi',
            'email' => 'admin@'.parse_url(config('app.url'), PHP_URL_HOST) ?: 'admin@example.com',
            'password' => Hash::make('DegistirBeni!2026'),
            'role' => 'owner',
            'is_active' => true,
            'password_changed_at' => null,
        ]);
    }

    /**
     * Örnek iki içerik türü. Müşteri bunları silebilir, adını değiştirebilir
     * ya da yenilerini ekleyebilir — hiçbiri koda bağlı değil.
     */
    private function contentTypes(): void
    {
        $products = ContentType::firstOrCreate(['key' => 'urunler'], [
            'name' => ['tr' => 'Ürün', 'en' => 'Product'],
            'plural' => ['tr' => 'Ürünler', 'en' => 'Products'],
            'icon' => 'box',
            'has_archive' => true,
            'route_prefix' => ['tr' => 'urunler', 'en' => 'products'],
            'supports' => ['image', 'gallery', 'excerpt', 'builder', 'seo', 'taxonomies'],
            'fields' => [],
            'sort' => 1,
        ]);

        Taxonomy::firstOrCreate(
            ['content_type_id' => $products->id, 'key' => 'kategori'],
            [
                'name' => ['tr' => 'Kategori', 'en' => 'Category'],
                'plural' => ['tr' => 'Kategoriler', 'en' => 'Categories'],
                'is_hierarchical' => true,
                'route_prefix' => ['tr' => 'kategori', 'en' => 'category'],
            ]
        );

        $news = ContentType::firstOrCreate(['key' => 'haberler'], [
            'name' => ['tr' => 'Haber', 'en' => 'News'],
            'plural' => ['tr' => 'Haberler', 'en' => 'News'],
            'icon' => 'newspaper',
            'has_archive' => true,
            'route_prefix' => ['tr' => 'haberler', 'en' => 'news'],
            'supports' => ['image', 'excerpt', 'body', 'seo', 'taxonomies'],
            'fields' => [],
            'sort' => 2,
        ]);

        Taxonomy::firstOrCreate(
            ['content_type_id' => $news->id, 'key' => 'etiket'],
            [
                'name' => ['tr' => 'Etiket', 'en' => 'Tag'],
                'plural' => ['tr' => 'Etiketler', 'en' => 'Tags'],
                'is_hierarchical' => false,
                'route_prefix' => ['tr' => 'etiket', 'en' => 'tag'],
            ]
        );
    }

    private function menus(): void
    {
        Menu::firstOrCreate(['key' => 'header'], ['name' => 'Üst menü']);
        Menu::firstOrCreate(['key' => 'footer'], ['name' => 'Alt menü']);
    }

    private function homePage(): void
    {
        if (Page::where('is_home', true)->exists()) {
            return;
        }

        $page = Page::create([
            'status' => 'published',
            'published_at' => now(),
            'is_home' => true,
        ]);

        foreach (Locales::codes() as $code) {
            $page->fillTranslation($code, [
                'title' => $code === 'tr' ? 'Ana Sayfa' : 'Home',
                'slug' => $code === 'tr' ? 'ana-sayfa' : 'home',
                'builder_data' => null,
            ]);
        }
    }

    private function contactForm(): void
    {
        Form::firstOrCreate(['key' => 'iletisim'], [
            'name' => 'İletişim Formu',
            'fields' => [
                ['key' => 'ad', 'type' => 'text', 'label' => ['tr' => 'Adınız Soyadınız', 'en' => 'Full name'], 'required' => true, 'width' => 50],
                ['key' => 'eposta', 'type' => 'email', 'label' => ['tr' => 'E-posta', 'en' => 'Email'], 'required' => true, 'width' => 50],
                ['key' => 'telefon', 'type' => 'tel', 'label' => ['tr' => 'Telefon', 'en' => 'Phone'], 'required' => false, 'width' => 50],
                ['key' => 'konu', 'type' => 'text', 'label' => ['tr' => 'Konu', 'en' => 'Subject'], 'required' => false, 'width' => 50],
                ['key' => 'mesaj', 'type' => 'textarea', 'label' => ['tr' => 'Mesajınız', 'en' => 'Message'], 'required' => true, 'width' => 100, 'rows' => 5],
                ['key' => 'onay', 'type' => 'checkbox', 'label' => ['tr' => 'Kişisel verilerimin işlenmesini kabul ediyorum.', 'en' => 'I accept the processing of my personal data.'], 'required' => true, 'width' => 100],
            ],
            'submit_label' => ['tr' => 'Gönder', 'en' => 'Send'],
            'success_message' => [
                'tr' => 'Mesajınız bize ulaştı. En kısa sürede dönüş yapacağız.',
                'en' => 'Your message has been received. We will get back to you shortly.',
            ],
            'notify_emails' => config('mail.from.address'),
            'store_submissions' => true,
            'spam_protection' => true,
        ]);
    }

    /** Ön yüzdeki sabit ifadeler; müşteri panelden değiştirebilir. */
    private function uiStrings(): void
    {
        $strings = [
            'read_more' => ['tr' => 'Devamını oku', 'en' => 'Read more'],
            'back' => ['tr' => 'Geri dön', 'en' => 'Back'],
            'all' => ['tr' => 'Tümü', 'en' => 'All'],
            'search' => ['tr' => 'Ara', 'en' => 'Search'],
            'no_results' => ['tr' => 'Sonuç bulunamadı.', 'en' => 'No results found.'],
            'contact_us' => ['tr' => 'Bize ulaşın', 'en' => 'Contact us'],
            'menu' => ['tr' => 'Menü', 'en' => 'Menu'],
            'close' => ['tr' => 'Kapat', 'en' => 'Close'],
            'next' => ['tr' => 'Sonraki', 'en' => 'Next'],
            'previous' => ['tr' => 'Önceki', 'en' => 'Previous'],
            'page_not_found' => ['tr' => 'Aradığınız sayfa bulunamadı.', 'en' => 'The page you are looking for was not found.'],
            'required_field' => ['tr' => 'Bu alan zorunludur.', 'en' => 'This field is required.'],
        ];

        foreach ($strings as $key => $values) {
            Translation::firstOrCreate(['group' => 'site', 'key' => $key], ['values' => $values]);
        }
    }
}
