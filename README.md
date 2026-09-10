# Mimar CMS

Kurumsal siteler için çok dilli içerik yönetim sistemi ve sayfa kurucusu.
Tek kod tabanı, her müşteride ayrı kurulum ve ayrı veritabanı.

---

## Neden WordPress değil

Bu sistem WordPress'i taklit etmez; onu ele geçirilebilir yapan şeyi
bilinçli olarak dışarıda bırakır.

WordPress sitelerinin büyük çoğunluğu çekirdek üzerinden değil, güncellenmemiş
bir **eklenti** üzerinden ele geçirilir: eklenti sistemi, çalışma anında
sunucuya kod yükleyip çalıştırabilir. Bir eklentide açık çıktığında saldırgan
sunucuda kendi PHP dosyasını oluşturur ve site artık onundur.

Mimar'da panelden kod yüklenemez. Yeni bir özellik yalnızca depoya kod
yazılarak, sizin elinizden geçerek eklenir. Bunun bedeli: müşteri "şu
eklentiyi kurayım" diyemez. Kazancı: kurulumdan iki yıl sonra kimsenin
dokunmadığı bir site hâlâ güvendedir.

Bunun üzerine eklenen katmanlar:

| Katman | Ne yapar |
|---|---|
| Panel adresi | `.env` ile belirlenir. Tahmin edilemez bir yol, otomatik tarayan botların panele hiç ulaşamaması demektir. |
| IP kısıtı | İsteğe bağlı; panele yalnızca belirtilen adreslerden girilir. |
| Kaba kuvvet kilidi | Art arda hatalı denemeden sonra hem hesap hem IP kilitlenir. |
| İki adımlı doğrulama | Şifresi ele geçen hesap bile panele giremez. |
| Yükleme denetimi | Uzantı, MIME ve **dosya imzası** ayrı ayrı doğrulanır; görseller yeniden kodlanır. Sunucuda çalıştırılabilir dosya oluşamaz. |
| İçerik temizliği | Zengin metinden betik, olay özniteliği ve `javascript:` adresleri ayıklanır. |
| Etkinlik kaydı | Kim, ne zaman, neyi değiştirdi — hepsi kayıtlı. |

---

## Kurulum

### Gereksinimler

- PHP 8.3+ (`gd`, `mbstring`, `pdo_mysql`, `openssl`, `fileinfo`)
- MySQL 5.7+ / MariaDB 10.3+
- Composer

Node.js yalnızca geliştirme sırasında gerekir. Paylaşımlı hostinglerde
derlenmiş varlıklar depoyla birlikte gelir.

### Yeni site

```bash
git clone <depo> site-adi && cd site-adi
bin/kur.sh site-adi
```

Betik `.env` dosyasını hazırlar, panel adresini rastgele belirler,
veritabanını kurar ve ilk yöneticiyi oluşturur.

`.env` içinde doldurulması gerekenler:

```dotenv
APP_NAME="Müşteri Adı"
APP_URL=https://musteri.com
CMS_ADMIN_PATH=panel-a7f3k2m9      # her sitede farklı olsun
DB_DATABASE=musteri_db
DB_USERNAME=...
DB_PASSWORD=...
MAIL_HOST=...                      # form bildirimleri için
```

### Sunucu ayarı

Alan adı **`public/`** klasörüne yönlendirilmelidir. cPanel'de bu genellikle
alan adının belge kökünü `.../site-adi/public` yapmak demektir.

`storage/` ve `bootstrap/cache/` klasörleri yazılabilir olmalıdır.

### Güncelleme

```bash
bin/guncelle.sh
```

Siteyi bakım kipine alır, kodu çeker, göçleri uygular, varlıkları derler ve
yayına döndürür. Bir adım hata verirse site otomatik olarak yayına alınır.

---

## Kavramlar

**Sayfa** — Sitenin ana bölümleri. Her sayfa kurucuda serbestçe tasarlanır.
Hiyerarşik olabilir (`/kurumsal/tarihce`).

**İçerik türü** — Tekrar eden içerik grupları: Ürünler, Haberler,
Referanslar… Panelden tanımlanır, kod yazılmaz. Tanımlandığı anda sol menüde
kendi bölümü açılır, ön yüzde kendi adresleri oluşur.

**Şablon** — Kurucuda hazırlanan, tekrar kullanılabilir yapı. Üst başlık,
alt bilgi ya da kaydedilmiş bir bölüm. "Varsayılan" işaretlenen üst
başlık/alt bilgi tüm sayfalarda görünür.

**Tema jetonu** — Renkler ve yazı tipleri kurucuda isimle seçilir
(`@primary`), sabit kod olarak değil. Müşteri paleti değiştirdiğinde site
bütün olarak güncellenir.

---

## Mimari

```
app/
  Builder/            Sayfa kurucusunun motoru
    Element.php         Ağaçtaki tek düğüm (bölüm / sütun / widget)
    Tree.php            Bir sayfanın ağacının tamamı
    TreeEditor.php      Ekleme, taşıma, kopyalama, silme
    Controls.php        Denetim tanımlarının fabrikası
    StyleCompiler.php   Ayarları kapsanmış CSS'e çevirir
    Renderer.php        Ağacı HTML'e çevirir (ön yüz + tuval)
    RenderContext.php   Çizim sırasındaki ortak bilgi
    Widget.php          Widget'ların temeli
    WidgetRegistry.php  Kayıtlı widget listesi
    Widgets/            24 widget

  Livewire/
    Auth/               Giriş, iki adımlı doğrulama, şifre sıfırlama
    Admin/              Panel ekranları
    Builder/Editor.php  Kurucunun ana bileşeni

  Services/           MediaUploader, LoginGuard, TwoFactor, ActivityLogger
  Support/            Locales, Theme, Html, Urls, Breadcrumbs
```

### Kurucu ağacı nasıl saklanır

Bir sayfanın tasarımı, çeviri satırında JSON olarak durur:

```json
{
  "version": 1,
  "elements": [{
    "id": "a1b2c3d4",
    "type": "section",
    "settings": { "_padding": { "d": { "t": 80, "b": 80, "unit": "px" } } },
    "elements": [{
      "id": "e5f6g7h8",
      "type": "column",
      "settings": { "width": { "d": 50, "m": 100 } },
      "elements": [{
        "id": "i9j0k1l2",
        "type": "widget",
        "widget": "heading",
        "settings": { "text": "Başlık", "color": "@primary" }
      }]
    }]
  }]
}
```

Kaydedilirken bu ağaçtan CSS derlenir ve yanına yazılır:

```css
.e-a1b2c3d4{padding-top:80px;padding-bottom:80px;}
.e-i9j0k1l2 .hd{color:var(--c-primary);}
```

Ziyaretçi sayfayı açtığında hiçbir hesaplama yapılmaz; hazır CSS basılır.
Cihaza bağlı ayarlar (`"m"`, `"t"`) medya sorgusuna dönüşür.

### Yeni widget eklemek

`app/Builder/Widgets/` altına bir sınıf, `resources/views/builder/widgets/`
altına bir Blade dosyası yazıp kayıt defterine bir satır eklemek yeterlidir.
Ayar paneli ve CSS üretimi, denetim tanımlarından otomatik doğar:

```php
class RibbonWidget extends Widget
{
    public function key(): string { return 'ribbon'; }
    public function label(): string { return 'Şerit'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => [
                'text' => Controls::text('Metin', ['default' => 'Yeni']),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                // `selector` ve `property`, CSS'in nasıl üretileceğini söyler.
                'bg' => Controls::color('Zemin', [
                    'selector' => '{{WRAPPER}} .ribbon',
                    'property' => 'background-color',
                    'default' => '@accent',
                ]),
            ]],
        ];
    }
}
```

---

## Geliştirme

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
npm run dev            # ayrı terminalde
php artisan serve
```

### Testler

```bash
php artisan test
```

| Dosya | Kapsam |
|---|---|
| `AdminSmokeTest` | Panelin her ekranı açılıyor mu, yetkiler doğru mu |
| `BuilderTest` | Bölüm/widget ekleme, cihaz ayarları, geri alma, kaydetme, dil kopyalama |
| `SecurityTest` | Kilitleme, iki adımlı doğrulama, yükleme denetimi, XSS, form tuzağı |
| `StyleCompilerTest` | Ayarların CSS'e çevrimi |

Yeni siteye deploy etmeden önce çalıştırın.
