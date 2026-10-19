<?php

return [

    /*
    |---------------------------------------------------------------------------
    | Panel
    |---------------------------------------------------------------------------
    | Panelin adresi her kurulumda .env üzerinden değiştirilebilir. Tahmin
    | edilemez bir yol, otomatik saldırı botlarının panele hiç ulaşamaması
    | anlamına gelir.
    */

    'admin_path' => trim(env('CMS_ADMIN_PATH', 'panel'), '/'),

    'admin_ips' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('CMS_ADMIN_IPS', ''))
    ))),

    /*
    |---------------------------------------------------------------------------
    | Diller
    |---------------------------------------------------------------------------
    | Kurulumda oluşturulacak varsayılan diller. Kurulduktan sonra diller
    | panelden yönetilir; burası yalnızca ilk tohumlama içindir.
    */

    'default_languages' => [
        ['code' => 'tr', 'name' => 'Türkçe', 'native_name' => 'Türkçe', 'is_default' => true],
        ['code' => 'en', 'name' => 'İngilizce', 'native_name' => 'English', 'is_default' => false],
    ],

    /*
    |---------------------------------------------------------------------------
    | Medya
    |---------------------------------------------------------------------------
    */

    'media' => [
        'max_upload_mb' => (int) env('CMS_MAX_UPLOAD_MB', 12),

        // Yalnızca bu uzantılar kabul edilir. Listede olmayan hiçbir dosya
        // sunucuya yazılmaz; uzantı, MIME ve gerçek dosya imzası ayrı ayrı
        // doğrulanır.
        'allowed' => [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'avif'],
            'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv'],
            'video' => ['mp4', 'webm'],
            'archive' => ['zip'],
        ],

        // Görseller yüklenirken üretilecek boyutlar.
        'sizes' => [
            'thumb' => ['w' => 320, 'h' => 320, 'fit' => 'cover'],
            'small' => ['w' => 640, 'h' => null, 'fit' => 'contain'],
            'medium' => ['w' => 1024, 'h' => null, 'fit' => 'contain'],
            'large' => ['w' => 1920, 'h' => null, 'fit' => 'contain'],
        ],

        'webp' => (bool) env('CMS_WEBP', true),
        'driver' => env('CMS_IMAGE_DRIVER', 'gd'),

        // Orijinal görselin en uzun kenarı bu değeri aşarsa küçültülür.
        'max_dimension' => 2560,
    ],

    /*
    |---------------------------------------------------------------------------
    | Güvenlik
    |---------------------------------------------------------------------------
    */

    'security' => [
        // Aynı e-posta / IP için art arda başarısız giriş sayısı ve kilit süresi.
        'max_login_attempts' => 5,
        'lockout_minutes' => 15,

        // Parola en az bu kadar karakter olmalı ve harf + rakam içermeli.
        'min_password_length' => 10,

        // Yönetici hesapları için iki adımlı doğrulama zorunlu mu?
        'force_two_factor_for_owners' => false,

        // Etkinlik kaydı kaç gün saklansın?
        'activity_log_days' => 180,
    ],

    /*
    |---------------------------------------------------------------------------
    | Roller
    |---------------------------------------------------------------------------
    | Yetkiler koda gömülüdür; panelden eklenti kurulamaz. WordPress'i savunmasız
    | yapan şey tam olarak çalışma anında kod yükleyebilmesidir.
    */

    'roles' => [
        'owner' => ['label' => 'Site Sahibi', 'level' => 100],
        'admin' => ['label' => 'Yönetici', 'level' => 80],
        'editor' => ['label' => 'Editör', 'level' => 50],
        'author' => ['label' => 'Yazar', 'level' => 30],
    ],

    /*
    |---------------------------------------------------------------------------
    | Teklif sepeti
    |---------------------------------------------------------------------------
    | Fiyat göstermeyen kataloglarda ziyaretçi ürünleri sepete toplar ve tek
    | formla teklif ister. `form_key` ile belirtilen form gönderildiğinde
    | sepetteki ürünler mesaja eklenir. Form panelde yoksa sepet hiç görünmez.
    */

    'quote' => [
        'form_key' => env('CMS_QUOTE_FORM', 'teklif'),

        // Sepete eklenebilecek içerik türleri.
        'content_types' => ['urunler'],

        'max_lines' => 50,
        'max_quantity' => 999,
    ],

    /*
    |---------------------------------------------------------------------------
    | Kısa adresler
    |---------------------------------------------------------------------------
    | Bu türlerin kayıtları ve kategorileri dil önekinden hemen sonra yalnızca
    | kendi adresiyle açılır: /tr/urunler/kategori/x yerine /tr/x. Eski uzun
    | adresler kalıcı olarak kısasına yönlendirilir. Aynı adreste bir sayfa
    | varsa sayfa önceliklidir.
    */

    'flat_urls' => ['urunler'],
];
