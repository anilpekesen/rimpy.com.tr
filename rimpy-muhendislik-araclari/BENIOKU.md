# Rimpy Makine — Mühendislik Araçları Paketi

Hidrolik ve tork uygulamaları için 11 hesaplama ve seçim aracı. Araçlar düz HTML + JavaScript'tir; derleme, veritabanı veya eklenti gerektirmez. Her hosting'de, WordPress'te ve Next.js gibi altyapılarda çalışır.

## Paket içeriği

```
araclar/                         → sitenizde /araclar/ klasörüne yüklenecek
  index.html                     Araçların listelendiği giriş sayfası
  tork-hesaplayici.html          Cıvata torku + tork anahtarı seçimi + pompa basıncı
  flans-sikma-siralamasi.html    Flanş sıkma sırası (görsel, animasyonlu)
  civata-germe.html              Hidrolik cıvata germe hesabı
  hidrolik-silindir-secimi.html  Silindir seti seçimi
  pompa-secimi.html              Pompa seçimi
  somun-kirici-cektirme.html     Somun kırıcı ve çektirme seçimi
  uyumluluk-kontrolu.html        Sistem uyumluluk kontrolü        (PDF + kayıt)
  set-listesi.html               Proje set listesi                (PDF + kayıt)
  teknik-sartname.html           Teknik şartname hazırlayıcı      (PDF + kayıt)
  kirala-satin-al.html           Kirala mı, satın al mı           (PDF + kayıt)
  birim-donusturucu.html         Birim dönüştürücü
  assets/
    ayarlar.js                   ← FİRMA BİLGİLERİ VE KAYIT ADRESİ (tek yerden)
    ortak.css                    Ortak tasarım
    ortak.js                     Ortak fonksiyonlar (PDF, kayıt formu, cıvata verileri)
    embed.js                     iframe ile gömmede otomatik yükseklik
sunucu/
  kayit.php                      Teklif/kayıt alıcısı → site köküne yüklenecek
```

## Kurulum (yaklaşık 15 dakika)

1. **`araclar` klasörünü** sitenizin kök dizinine yükleyin. Araçlar `https://alanadiniz.com/araclar/` adresinden açılır.
2. **`araclar/assets/ayarlar.js`** dosyasını açın:
   - `FIRMA_ILETISIM`: gerçek web adresi ve e-postanız (PDF raporların başlığında görünür)
   - `KAYIT_URL`: `'/kayit.php'`
   - `KVKK_URL`: KVKK aydınlatma metni sayfanızın adresi
3. **`sunucu/kayit.php`** dosyasını site köküne yükleyin ve üstteki ayarları düzenleyin:
   - `BILDIRIM_EPOSTA`: talepler hangi adrese gelsin
   - `GONDEREN_EPOSTA`: kendi alan adınızdan bir adres (örn. noreply@alanadiniz.com). Farklı alan adı kullanılırsa e-postalar spam'e düşer.
   - `KAYIT_DIZINI`: kayıtların tutulacağı klasör. **public_html dışında** olmalı ki dışarıdan okunamasın.
4. Test: Bir araçta formu doldurup "PDF indir ve teklif iste"ye basın. E-posta gelmeli ve kayıt klasöründe `ozet-YYYY-AA.csv` dosyası oluşmalı.

> Hosting'iniz PHP desteklemiyorsa (ör. Vercel'de Next.js) `kayit.php` yerine aynı işi yapan bir API route yazılması gerekir.

## Siteye yerleştirme

**A. Ayrı sayfa olarak (en kolay):** Menüye `/araclar/` bağlantısı ekleyin.

**B. Mevcut bir sayfanın içine gömme:** Ürün sayfası veya blog yazısı gibi bir sayfaya şu kodu ekleyin (WordPress'te "Özel HTML" bloğu):

```html
<iframe class="rimpy-arac" src="/araclar/tork-hesaplayici.html"
        title="Cıvata Tork Hesaplayıcı" style="width:100%;border:0" loading="lazy"></iframe>
<script src="/araclar/assets/embed.js" defer></script>
```

iframe yüksekliği içeriğe göre otomatik ayarlanır. Önerilen yerleşimler:

| Sayfa | Gömülecek araç |
|---|---|
| Hidrolik tork anahtarları kategorisi | tork-hesaplayici, flans-sikma-siralamasi |
| Cıvata germe ürünleri | civata-germe |
| Silindirler / kaldırma | hidrolik-silindir-secimi, pompa-secimi |
| Kiralama hizmeti | kirala-satin-al, set-listesi |
| Kurumsal satış / ihaleler | teknik-sartname, set-listesi |

**Next.js:** `araclar` klasörünü `public/` içine koyun; adresler aynı kalır.

## YAYINDAN ÖNCE YAPILMASI GEREKENLER

**1. Örnek kataloğu kendi ürünlerinizle değiştirin.** Araçlardaki model kodları (HS-, HD-, RS-, RL-, BT-, NS-, PL-, PE- …) ve teknik değerleri **örnektir**, gerçek ürün değildir. Kendi ürün verilerinizle değiştirilmeden yayınlanmamalıdır:

| Dosya | Düzenlenecek liste |
|---|---|
| hidrolik-silindir-secimi.html | `KATALOG` (ton, strok, kapalı yükseklik) |
| tork-hesaplayici.html | `ANAHTARLAR` (basınç–tork tablosu, ağız aralığı, burun ölçüsü) |
| pompa-secimi.html | `POMPALAR` |
| civata-germe.html | `GERMELER` (hidrolik alan, çap aralığı) |
| somun-kirici-cektirme.html | `KIRICILAR`, `CEKTIRMELER` |
| set-listesi.html | `KAT` (diğer listelerle aynı kodlar) |
| kirala-satin-al.html | `SETLER` (satış ve kira fiyatları), `KUR` |

Cıvata ölçüleri, malzeme dayanımları ve hesap formülleri standart değerlerdir; değiştirmeye gerek yoktur.

**2. KVKK.** Kayıt formları kişisel veri (ad, firma, e-posta, telefon) toplar. Sitenizde bir KVKK aydınlatma metni sayfası bulunmalı ve `KVKK_URL` bu sayfayı göstermelidir. Metni bir hukuk danışmanına kontrol ettirin.

**3. Sorumluluk notu.** Raporların altında "değerler bilgilendirme amaçlıdır" notu bulunur. Ayrıntılı bir kullanım şartları metni eklemeyi değerlendirin.

## PDF nasıl oluşuyor?

"PDF indir" butonu tarayıcının yazdırma penceresini açar. Kullanıcı "Hedef: PDF olarak kaydet"i seçer. Rapor A4 düzende, firma adınız ve rapor numarasıyla (örn. `SET-261012-9831`) oluşur. Rapor numarası, size gelen kayıtla aynıdır; müşteri aradığında bu numarayla kaydı bulabilirsiniz.

## Kayıtlar nerede?

`kayit.php` her talep için:
- `ozet-YYYY-AA.csv`: Excel'de açılabilen özet (tarih, rapor no, araç, kişi, firma, iletişim)
- `kayitlar-YYYY-AA.jsonl`: araçtaki tüm hesap verisi dahil tam kayıt
- Bildirim e-postası (Yanıtla → doğrudan müşteriye)

Bot koruması (gizli alan) ve hız sınırı (IP başına dakikada 5 kayıt) vardır.
