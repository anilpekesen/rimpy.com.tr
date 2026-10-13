<?php

namespace App\Builder\Widgets\Showcase;

use App\Builder\Controls;
use App\Builder\Element;
use App\Builder\RenderContext;
use App\Builder\Widget;

/**
 * Teknik şartname hazırlayıcı: satın alma veya ihale dosyası için hidrolik
 * ekipman şartnamesi yazar. Maddeler burada, dile göre tutulur; tarayıcı
 * yalnızca seçilen ekipmana ve ayarlara göre {yer tutucu}ları doldurur.
 */
class SpecWriterWidget extends Widget
{
    use SectionHead;

    public function key(): string { return 'spec_writer'; }
    public function label(): string { return 'Teknik şartname hazırlayıcı'; }
    public function icon(): string { return 'clipboard-list'; }
    public function category(): string { return 'catalog'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => $this->headControls(
                'Mühendislik araçları', "Teknik şartname\nhazırlayıcı",
                'Satın alma veya ihale dosyanız için ekipmanı ve gereksinimleri seçin; maddeler otomatik yazılır, kopyalanır veya yazdırılır.'
            ) + [
                'note' => Controls::textarea('Alt not', ['default' => 'Maddeler genel saha uygulamasına ve yürürlükteki mevzuat atıflarına göre hazırlanır. İhale dosyasına koymadan önce kurumunuzun gereksinimleriyle karşılaştırın. Metin, rekabeti kısıtlayan marka veya model atfı içermez.']),
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        $settings = $this->resolved($element, $context);

        return ['settings' => $settings, 'spec' => $context->locale === 'tr' ? self::turkish() : self::english()];
    }

    /**
     * Yer tutucular: {q} adet, {cap} kapasite, {ext} ikinci ölçü, {drive}
     * tahrik, {war} garanti ayı, {del} teslim günü. Bir madde dizi ise
     * `multi` üçten fazla adet için, `single` diğer durumlar için kullanılır.
     */
    private static function turkish(): array
    {
        return [
            'equipment' => [
                'tw' => [
                    'label' => 'Hidrolik tork anahtarı seti', 'title' => 'Hidrolik Tork Anahtarı Seti',
                    'cap' => ['Maks. tork (N·m)', 10000], 'ext' => ['Somun ağzı aralığı (mm)', '46–105'], 'drive' => 'el',
                    'sections' => [
                        ['Genel', [
                            '{q} takım hidrolik tork anahtarı seti; her takım anahtar, pompa, hortum ve manometreden oluşacaktır.',
                            'Anahtar en az {cap} N·m tork üretebilmeli ve {ext} mm somun ağzı aralığında kullanılabilmelidir (değiştirilebilir altıgen kaset veya lokma ile).',
                            'Tork doğruluğu ±%3 veya daha iyi olmalı; basınç–tork dönüşüm tablosu teslimatla birlikte verilmelidir.',
                            'Anahtar 360° dönebilen hortum bağlantısına ve 360° ayarlanabilir reaksiyon kolu veya kaset tipi reaksiyon ayağına sahip olmalıdır.',
                            'Gövde yüksek dayanımlı alüminyum veya çelik alaşımdan, korozyona karşı kaplanmış olmalıdır.',
                        ]],
                        ['Pompa ve bağlantılar', [
                            'Pompa {drive}, iki kademeli, 700 bar maks. çalışma basınçlı olmalıdır.',
                            'Pompada 4/3 yön valfi, ayarlanabilir emniyet valfi ve en az 5 m kablolu uzaktan kumanda bulunmalıdır.',
                            'Manometre gliserinli, 0–1000 bar skalalı, tercihen tork skalası işaretli olmalıdır.',
                            'Her anahtar için en az 5 m uzunluğunda, 700 bar çalışma basınçlı, patlama emniyet katsayısı en az 4:1 olan hızlı kaplinli ikiz hortum verilmelidir.',
                        ]],
                    ],
                    'calibration' => 'Tork anahtarı ve manometre',
                ],
                'cyl' => [
                    'label' => 'Hidrolik silindir seti', 'title' => 'Hidrolik Silindir Seti',
                    'cap' => ['Kapasite (ton)', 50], 'ext' => ['Strok (mm)', '150'], 'drive' => 'hand',
                    'sections' => [
                        ['Genel', [
                            '{q} adet {cap} ton kapasiteli, en az {ext} mm stroklu hidrolik silindir.',
                            'Maks. çalışma basıncı 700 bar olmalı; silindir üzerinde kapasite, strok ve maks. basınç kalıcı olarak işaretlenmelidir.',
                            'Silindirde eğik başlık (tilt saddle), tercihen kilit somunlu mekanik emniyet bulunmalıdır.',
                            'Silindir aşırı strok durdurucusuna sahip olmalı; piston yüzeyi sert krom kaplı olmalıdır.',
                            'Tek etkili silindirlerde yay geri dönüşü, çift etkili silindirlerde hidrolik dönüş olmalıdır.',
                        ]],
                        ['Pompa ve bağlantılar', [
                            'Pompa {drive} olmalı; depo hacmi tüm silindirlerin tam stroku için gereken yağın en az 1,3 katı olmalıdır.',
                            ['multi' => '{q} silindiri aynı anda besleyecek dağıtım manifoldu ve her hatta ayrı vana verilmelidir.', 'single' => 'Her silindir için ayrı hortum ve hızlı kaplin verilmelidir.'],
                            'Hortumlar 700 bar çalışma basınçlı, patlama emniyet katsayısı en az 4:1 olmalıdır.',
                            'Sisteme 0–1000 bar gliserinli manometre ve manometre adaptörü dahil edilmelidir.',
                        ]],
                    ],
                    'calibration' => 'Manometre',
                ],
                'bt' => [
                    'label' => 'Hidrolik cıvata germe seti', 'title' => 'Hidrolik Cıvata Germe Seti',
                    'cap' => ['Maks. germe yükü (kN)', 1000], 'ext' => ['Cıvata çap aralığı', 'M30–M45'], 'drive' => 'el',
                    'sections' => [
                        ['Genel', [
                            '{q} adet hidrolik cıvata germe aleti; {ext} cıvata aralığı için uygun köprü ve çekme somunlarıyla.',
                            'Alet en az {cap} kN germe yükü üretebilmeli, maks. çalışma basıncı 1500 bar olmalıdır.',
                            'Aşırı strok göstergesi veya mekanik strok sınırlayıcı bulunmalıdır.',
                            'Çekme somunları ilgili cıvata dişine uygun ve değiştirilebilir olmalıdır.',
                        ]],
                        ['Pompa ve bağlantılar', [
                            '1500 bar germe pompası {drive} olmalı ve ayarlanabilir basınç sınırlayıcıya sahip olmalıdır.',
                            'Germe aletlerini zincirleme bağlamaya uygun, 1500 bar çalışma basınçlı hortum seti verilmelidir.',
                            '0–1600 bar gliserinli manometre dahil edilmelidir.',
                        ]],
                    ],
                    'calibration' => 'Germe aleti ve manometre',
                ],
                'pump' => [
                    'label' => 'Hidrolik pompa', 'title' => 'Hidrolik Pompa',
                    'cap' => ['Debi, 700 bar (L/dk)', 1], 'ext' => ['Depo (L)', '10'], 'drive' => 'el',
                    'sections' => [
                        ['Genel', [
                            '{q} adet {drive} hidrolik pompa.',
                            'Pompa 700 bar maks. çalışma basıncında en az {cap} L/dk debi sağlamalı; iki kademeli (düşük basınçta yüksek debi) olmalıdır.',
                            'Kullanılabilir depo hacmi en az {ext} L olmalıdır.',
                            'Ayarlanabilir emniyet valfi, manometre bağlantı portu ve yağ seviye göstergesi bulunmalıdır.',
                            'Pompa taşıma kolu veya tekerlekli şasi ile kolay taşınabilir olmalıdır.',
                        ]],
                    ],
                    'calibration' => 'Manometre',
                ],
            ],
            'drives' => [
                'el' => ['Elektrik 230 V', '230 V monofaze elektrik motorlu'],
                'el3' => ['Elektrik 400 V', '400 V trifaze elektrik motorlu'],
                'air' => ['Basınçlı hava', 'basınçlı hava (6–7 bar) tahrikli'],
                'hand' => ['El', 'el ile çalıştırılan'],
            ],
            'documents' => ['Belgeler ve uygunluk', [
                'Ürünler Makine Emniyeti Yönetmeliği (2006/42/AT) kapsamında CE işaretli olmalı; AB uygunluk beyanı teslimatla verilmelidir.',
                'Hidrolik devre ISO 4413 güvenlik gerekliliklerine uygun tasarlanmış olmalıdır.',
                'Türkçe kullanım ve bakım kılavuzu, yedek parça listesi ve patlatılmış resim verilmelidir.',
            ]],
            'atex' => 'Pompa ve tahrik ünitesi ATEX 2014/34/AB yönetmeliğine uygun olmalı, ilgili ATEX sertifikası sunulmalıdır.',
            'calibration' => '{items} için ISO/IEC 17025 akrediteli laboratuvardan alınmış kalibrasyon sertifikası verilmelidir.',
            'service' => ['Garanti, servis ve teslimat', [
                'Garanti süresi teslimden itibaren en az {war} ay olmalıdır.',
                'Yüklenici, ürünlerin yedek parça ve servis hizmetini en az 10 yıl süreyle Türkiye\'den sağlayacağını taahhüt etmelidir.',
                'Teslim süresi sipariş tarihinden itibaren en fazla {del} takvim günüdür.',
            ]],
            'training' => 'Teslimatta en az 4 saatlik uygulamalı kullanıcı ve güvenlik eğitimi verilmeli, katılım belgesi düzenlenmelidir.',
            'case' => 'Her set darbeye dayanıklı taşıma çantası veya ahşap/metal sandık içinde teslim edilmelidir.',
            'suffix' => 'Teknik Şartname',
        ];
    }

    private static function english(): array
    {
        return [
            'equipment' => [
                'tw' => [
                    'label' => 'Hydraulic torque wrench kit', 'title' => 'Hydraulic Torque Wrench Kit',
                    'cap' => ['Max. torque (N·m)', 10000], 'ext' => ['Nut across-flats range (mm)', '46–105'], 'drive' => 'el',
                    'sections' => [
                        ['General', [
                            '{q} hydraulic torque wrench kit(s); each kit shall consist of a wrench, pump, hoses and gauge.',
                            'The wrench shall deliver at least {cap} N·m and fit nuts of {ext} mm across flats (with interchangeable hex cassettes or sockets).',
                            'Torque accuracy shall be ±3% or better; a pressure–torque conversion table shall be supplied with the delivery.',
                            'The wrench shall have a 360° swivel hose connection and a 360° adjustable reaction arm or cassette-type reaction foot.',
                            'The body shall be made of high-strength aluminium or steel alloy with a corrosion-resistant finish.',
                        ]],
                        ['Pump and connections', [
                            'The pump shall be {drive}, two-stage, rated for 700 bar maximum working pressure.',
                            'The pump shall have a 4/3 directional valve, an adjustable relief valve and a corded remote control of at least 5 m.',
                            'The gauge shall be glycerine-filled, 0–1000 bar, preferably with a torque scale.',
                            'Each wrench shall be supplied with a twin hose of at least 5 m, rated 700 bar, burst safety factor at least 4:1, with quick couplers.',
                        ]],
                    ],
                    'calibration' => 'the torque wrench and gauge',
                ],
                'cyl' => [
                    'label' => 'Hydraulic cylinder kit', 'title' => 'Hydraulic Cylinder Kit',
                    'cap' => ['Capacity (t)', 50], 'ext' => ['Stroke (mm)', '150'], 'drive' => 'hand',
                    'sections' => [
                        ['General', [
                            '{q} hydraulic cylinder(s) of {cap} t capacity with at least {ext} mm stroke.',
                            'Maximum working pressure shall be 700 bar; capacity, stroke and maximum pressure shall be permanently marked on the cylinder.',
                            'The cylinder shall have a tilt saddle and preferably a lock-nut mechanical safety.',
                            'The cylinder shall have an overstroke stop; the plunger surface shall be hard chrome plated.',
                            'Single-acting cylinders shall have spring return; double-acting cylinders shall have hydraulic return.',
                        ]],
                        ['Pump and connections', [
                            'The pump shall be {drive}; reservoir capacity shall be at least 1.3 times the oil required for the full stroke of all cylinders.',
                            ['multi' => 'A manifold feeding all {q} cylinders simultaneously, with a valve on each line, shall be supplied.', 'single' => 'A separate hose and quick coupler shall be supplied for each cylinder.'],
                            'Hoses shall be rated 700 bar with a burst safety factor of at least 4:1.',
                            'A glycerine-filled 0–1000 bar gauge and gauge adaptor shall be included.',
                        ]],
                    ],
                    'calibration' => 'the gauge',
                ],
                'bt' => [
                    'label' => 'Hydraulic bolt tensioning kit', 'title' => 'Hydraulic Bolt Tensioning Kit',
                    'cap' => ['Max. tensioning load (kN)', 1000], 'ext' => ['Bolt size range', 'M30–M45'], 'drive' => 'el',
                    'sections' => [
                        ['General', [
                            '{q} hydraulic bolt tensioner(s) with bridges and puller nuts for the {ext} bolt range.',
                            'The tool shall deliver at least {cap} kN tensioning load at a maximum working pressure of 1500 bar.',
                            'An overstroke indicator or mechanical stroke limiter shall be provided.',
                            'Puller nuts shall match the bolt thread and be interchangeable.',
                        ]],
                        ['Pump and connections', [
                            'The 1500 bar tensioning pump shall be {drive} with an adjustable pressure limiter.',
                            'A 1500 bar hose set suitable for daisy-chaining the tensioners shall be supplied.',
                            'A glycerine-filled 0–1600 bar gauge shall be included.',
                        ]],
                    ],
                    'calibration' => 'the tensioner and gauge',
                ],
                'pump' => [
                    'label' => 'Hydraulic pump', 'title' => 'Hydraulic Pump',
                    'cap' => ['Flow at 700 bar (L/min)', 1], 'ext' => ['Reservoir (L)', '10'], 'drive' => 'el',
                    'sections' => [
                        ['General', [
                            '{q} {drive} hydraulic pump(s).',
                            'The pump shall deliver at least {cap} L/min at 700 bar maximum working pressure and be two-stage (high flow at low pressure).',
                            'Usable reservoir capacity shall be at least {ext} L.',
                            'An adjustable relief valve, gauge port and oil level indicator shall be provided.',
                            'The pump shall be easily portable with a carrying handle or wheeled frame.',
                        ]],
                    ],
                    'calibration' => 'the gauge',
                ],
            ],
            'drives' => [
                'el' => ['Electric 230 V', 'driven by a 230 V single-phase electric motor'],
                'el3' => ['Electric 400 V', 'driven by a 400 V three-phase electric motor'],
                'air' => ['Compressed air', 'air-driven (6–7 bar)'],
                'hand' => ['Hand', 'hand-operated'],
            ],
            'documents' => ['Documents and compliance', [
                'Products shall be CE marked under the Machinery Directive 2006/42/EC; the EU declaration of conformity shall be supplied with the delivery.',
                'The hydraulic circuit shall be designed to the safety requirements of ISO 4413.',
                'An operating and maintenance manual, spare parts list and exploded drawing shall be supplied in the purchaser\'s language.',
            ]],
            'atex' => 'The pump and drive unit shall comply with ATEX Directive 2014/34/EU; the relevant ATEX certificate shall be provided.',
            'calibration' => 'A calibration certificate for {items} from an ISO/IEC 17025 accredited laboratory shall be supplied.',
            'service' => ['Warranty, service and delivery', [
                'The warranty period shall be at least {war} months from delivery.',
                'The supplier shall commit to providing spare parts and service for at least 10 years.',
                'Delivery shall be no later than {del} calendar days from the order date.',
            ]],
            'training' => 'At least 4 hours of hands-on operator and safety training shall be given at delivery, with attendance certificates.',
            'case' => 'Each kit shall be delivered in an impact-resistant carry case or wooden/metal crate.',
            'suffix' => 'Technical Specification',
        ];
    }
}
