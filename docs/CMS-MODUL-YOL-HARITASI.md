# Mimar CMS modül yol haritası

Bu belge, aynı çekirdekten üretilecek kurumsal web sitelerinde hangi yönetim alanlarının bulunacağını ve bunların Mimar içinde nasıl modellenmesi gerektiğini tanımlar.

## Temel mimari kararı

Her müşteri sitesi aynı kod sürümünü kullanır, fakat ayrı kurulum ve ayrı veritabanında çalışır. Böylece bir müşterinin içeriği, kullanıcıları veya hatası başka müşteriyi etkilemez. Çekirdek güncellemeleri sürümlenerek bütün kurulumlara dağıtılır.

Birden fazla alan adı tek müşterinin aynı içeriğini gösterecekse alan adları site ayarına bağlanabilir. Alan adları farklı içerik, dil, tema veya kullanıcı yetkisi gerektiriyorsa bunlar ayrı site kurulumu olmalıdır.

## İki modül türü

### İçerik türü olarak açılacaklar

Bunların ortak davranışı aynıdır: liste, detay, kapak görseli, galeri, kategori, özel alan, SEO, yayın durumu ve çoklu dil. Bu nedenle çekirdekte ayrı kod yazmak yerine hazır içerik türü şablonları sunulmalıdır.

| Hazır tür | Önerilen alanlar |
|---|---|
| Haberler | Kapak, özet, metin, kategori, yayın tarihi, SEO |
| Blog / Makaleler | Kapak, yazar, okuma süresi, kategori, etiket, SEO |
| Duyurular | Başlangıç ve bitiş tarihi, önem seviyesi, dosya |
| Projeler | Müşteri, konum, yıl, durum, hizmetler, galeri |
| Referanslar | Logo, web adresi, sektör, sıralama |
| İş ortakları | Logo, açıklama, web adresi, ortaklık türü |
| Hizmetler / Çözümler | Kapak, özet, içerik, ilgili projeler |
| Ürünler | Kod, marka, teknik alanlar, belgeler, galeri, kategoriler |
| Ofisler / Şubeler | Adres, telefon, e-posta, koordinat, çalışma saatleri |
| Ekip | Fotoğraf, görev, özgeçmiş, iletişim, sıralama |
| SSS | Soru, cevap, kategori, sıralama |
| Galeriler | Kapak, görseller, açıklamalar, tarih, kategori |
| Belgeler / Kataloglar | Dosya, kapak, sürüm, tarih, kategori |
| Etkinlikler | Başlangıç/bitiş, konum, kayıt adresi, kontenjan |
| Kariyer ilanları | Birim, konum, çalışma türü, son başvuru, form |
| Sertifikalar | Belge görseli/dosyası, kurum, geçerlilik tarihi |

“Çalıştığımız kurumlar” tek başına belirsiz bir addır. Müşteri logoları için **Referanslar**, birlikte ürün veya hizmet geliştirilen firmalar için **İş ortakları**, projeye katılan kuruluşlar için **Proje paydaşları** kullanılmalıdır.

### Bağımsız çekirdek modül olacaklar

Bu alanların kendine özgü iş akışı vardır ve yalnızca içerik türüyle çözülmemelidir.

| Modül | Olmazsa olmaz özellikler | Durum |
|---|---|---|
| Sayfalar | Hiyerarşi, taslak/yayın, zamanlama, SEO, sürüm geçmişi | Var |
| Sayfa kurucu | Bölüm/sütun/widget, cihaz önizleme, geri alma, şablon | Var |
| Medya | Güvenli yükleme, klasör, arama, alt metin, türev boyutlar | Var |
| Menü | Birden çok menü, iç içe yapı, sürükle-bırak, dil etiketleri | Var |
| Formlar | Alan kurucu, bildirim, otomatik cevap, spam koruması, kayıtlar | Var |
| Slider | Birden çok slider, slayt sırası, çoklu dil, görsel ve CTA | Var; zamanlama ve ayrı mobil görsel geliştirilebilir |
| Arama | İçerik indeksi, sonuç ağırlığı, filtreler, boş sonuç ekranı | Planlanmalı |
| SEO | Varsayılanlar, içerik alanları, sitemap, robots, canonical, OG | Büyük ölçüde var |
| Yönlendirmeler | 301/302, isabet sayısı, toplu içe aktarma | Var |
| Kullanıcı/yetki | Rol, modül ve işlem bazlı izin, 2FA | Temel sürüm var |
| İş akışı | Taslak, inceleme, onay, zamanlı yayın, içerik kilidi | Geliştirilmeli |
| Sürümleme | Sayfa ve içerik geçmişi, karşılaştırma, geri yükleme | Sayfada var |
| Etkinlik kaydı | Kim, neyi, ne zaman değiştirdi; filtre ve dışa aktarma | Temel sürüm var |
| Site ayarları | Kimlik, iletişim, sosyal medya, kodlar, bakım modu | Temel sürüm var |
| Çoklu dil | Dil bazlı adres, içerik, menü etiketi, eksik çeviri görünümü | Var |
| İçeri aktarma | CSV/Excel ile ürün, referans, proje ve yönlendirme aktarımı | Planlanmalı |
| Yedekleme | Veritabanı ve medya yedeği, geri yükleme, saklama politikası | Planlanmalı |

## Uygulama sırası

### Aşama 1: Kurumsal site çekirdeği

Menü sürükle-bırak, çalışan medya seçici, merkezi slider yönetimi, içerik türü hazır şablonları, ofis/şube alanları, SSS ve duyurular tamamlandı.

### Aşama 2: Editör güveni

İçerik sürümleri, önizleme bağlantısı, zamanlı yayın, onay akışı, eksik çeviri ekranı, kırık bağlantı taraması ve medya kullanım bilgisi.

### Aşama 3: Ajans işletimi

Kurulumları sürüm ve sağlık durumuyla izleyen ayrı bir “site filosu” paneli, otomatik yedek, toplu çekirdek güncellemesi ve hata raporları. Müşteri içerikleri bu merkez panele taşınmamalıdır.

### Aşama 4: İhtiyaca bağlı modüller

E-ticaret bağlantıları, bayi alanı, teklif talebi, doküman merkezi, etkinlik kaydı, iş ilanı başvuruları, bülten entegrasyonu ve harici CRM/ERP bağlantıları.

## Hidrokom benzeri bir site için başlangıç paketi

Sayfalar, ürünler ve ürün kategorileri, hizmetler, sliderlar, hakkımızda, ofis/iletişim noktaları, kataloglar, haberler, referanslar veya iş ortakları, SSS, teklif/iletişim formları, çoklu dil, SEO ve yönlendirmeler yeterli çekirdek pakettir. Projeler yalnızca gerçekten vaka çalışması veya tamamlanan uygulama yayınlanacaksa açılmalıdır.
