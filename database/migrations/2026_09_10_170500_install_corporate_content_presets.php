<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $presets = [
            'blog' => [['tr' => 'Makale', 'en' => 'Article'], ['tr' => 'Blog', 'en' => 'Blog'], 'newspaper', ['image', 'excerpt', 'body', 'seo', 'taxonomies'], []],
            'projeler' => [['tr' => 'Proje', 'en' => 'Project'], ['tr' => 'Projeler', 'en' => 'Projects'], 'blocks', ['image', 'gallery', 'excerpt', 'body', 'seo', 'taxonomies'], [['key' => 'musteri', 'type' => 'text', 'label' => ['tr' => 'Müşteri', 'en' => 'Client'], 'required' => false], ['key' => 'konum', 'type' => 'text', 'label' => ['tr' => 'Konum', 'en' => 'Location'], 'required' => false], ['key' => 'yil', 'type' => 'number', 'label' => ['tr' => 'Yıl', 'en' => 'Year'], 'required' => false]]],
            'referanslar' => [['tr' => 'Referans', 'en' => 'Reference'], ['tr' => 'Referanslar', 'en' => 'References'], 'users', ['image', 'excerpt', 'seo', 'taxonomies'], [['key' => 'web_adresi', 'type' => 'url', 'label' => ['tr' => 'Web adresi', 'en' => 'Website'], 'required' => false], ['key' => 'sektor', 'type' => 'text', 'label' => ['tr' => 'Sektör', 'en' => 'Industry'], 'required' => false]]],
            'ofisler' => [['tr' => 'Ofis', 'en' => 'Office'], ['tr' => 'Ofisler', 'en' => 'Offices'], 'blocks', ['image', 'body', 'seo'], [['key' => 'adres', 'type' => 'textarea', 'label' => ['tr' => 'Adres', 'en' => 'Address'], 'required' => false], ['key' => 'telefon', 'type' => 'text', 'label' => ['tr' => 'Telefon', 'en' => 'Phone'], 'required' => false], ['key' => 'eposta', 'type' => 'text', 'label' => ['tr' => 'E-posta', 'en' => 'Email'], 'required' => false], ['key' => 'harita', 'type' => 'url', 'label' => ['tr' => 'Harita bağlantısı', 'en' => 'Map link'], 'required' => false]]],
            'sss' => [['tr' => 'Soru', 'en' => 'Question'], ['tr' => 'Sık Sorulan Sorular', 'en' => 'FAQ'], 'file-text', ['body', 'taxonomies', 'seo'], []],
            'duyurular' => [['tr' => 'Duyuru', 'en' => 'Announcement'], ['tr' => 'Duyurular', 'en' => 'Announcements'], 'newspaper', ['image', 'excerpt', 'body', 'seo'], [['key' => 'bitis_tarihi', 'type' => 'date', 'label' => ['tr' => 'Bitiş tarihi', 'en' => 'End date'], 'required' => false], ['key' => 'dosya', 'type' => 'file', 'label' => ['tr' => 'Ek dosya', 'en' => 'Attachment'], 'required' => false]]],
            'galeriler' => [['tr' => 'Galeri', 'en' => 'Gallery'], ['tr' => 'Galeriler', 'en' => 'Galleries'], 'image', ['image', 'gallery', 'excerpt', 'seo', 'taxonomies'], []],
            'ekip' => [['tr' => 'Ekip üyesi', 'en' => 'Team member'], ['tr' => 'Ekip', 'en' => 'Team'], 'users', ['image', 'body', 'seo'], [['key' => 'gorev', 'type' => 'text', 'label' => ['tr' => 'Görev', 'en' => 'Role'], 'required' => false], ['key' => 'eposta', 'type' => 'text', 'label' => ['tr' => 'E-posta', 'en' => 'Email'], 'required' => false]]],
        ];

        $sort = (int) DB::table('content_types')->max('sort');

        foreach ($presets as $key => [$name, $plural, $icon, $supports, $fields]) {
            if (DB::table('content_types')->where('key', $key)->exists()) {
                continue;
            }

            $id = DB::table('content_types')->insertGetId([
                'key' => $key,
                'name' => json_encode($name, JSON_UNESCAPED_UNICODE),
                'plural' => json_encode($plural, JSON_UNESCAPED_UNICODE),
                'icon' => $icon,
                'has_archive' => true,
                'route_prefix' => json_encode(['tr' => $key, 'en' => $key === 'projeler' ? 'projects' : ($key === 'referanslar' ? 'references' : $key)], JSON_UNESCAPED_UNICODE),
                'supports' => json_encode($supports, JSON_UNESCAPED_UNICODE),
                'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE),
                'sort' => ++$sort,
                'is_active' => true,
                'is_system' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (in_array('taxonomies', $supports, true)) {
                DB::table('taxonomies')->insert([
                    'content_type_id' => $id,
                    'key' => 'kategori',
                    'name' => json_encode(['tr' => 'Kategori', 'en' => 'Category'], JSON_UNESCAPED_UNICODE),
                    'plural' => json_encode(['tr' => 'Kategoriler', 'en' => 'Categories'], JSON_UNESCAPED_UNICODE),
                    'is_hierarchical' => true,
                    'route_prefix' => json_encode(['tr' => 'kategori', 'en' => 'category'], JSON_UNESCAPED_UNICODE),
                    'sort' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // İçerik kaybına yol açmamak için kurulmuş modüller geri alınırken silinmez.
    }
};
