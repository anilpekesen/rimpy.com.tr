<?php
/**
 * Rimpy Makine — hesaplama araçları kayıt alıcısı
 * Araçlardaki "PDF indir ve teklif iste" butonu buraya JSON gönderir.
 * Kurulum:
 *   1) Bu dosyayı sitenizin köküne yükleyin (örn. https://alanadiniz.com/kayit.php)
 *   2) Araç dosyalarında KAYIT_URL = '/kayit.php' yapın
 *   3) Aşağıdaki ayarları düzenleyin. KAYIT_DIZINI web'den erişilemeyen bir klasör olmalı.
 */
const BILDIRIM_EPOSTA = 'satis@ornek.com';          // talepler bu adrese bildirilir
const GONDEREN_EPOSTA = 'noreply@ornek.com';        // sitenizin alan adından bir adres
const KAYIT_DIZINI    = __DIR__ . '/../arac-kayitlari'; // public_html dışında
const IZINLI_KAYNAK   = '';                         // farklı alan adından çağrılacaksa: 'https://www.ornek.com'

header('Content-Type: application/json; charset=utf-8');
if (IZINLI_KAYNAK) { header('Access-Control-Allow-Origin: ' . IZINLI_KAYNAK); header('Access-Control-Allow-Headers: Content-Type'); }
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo '{"ok":false}'; exit; }

$ham = file_get_contents('php://input', false, null, 0, 200000);
$v = json_decode($ham, true);
if (!is_array($v)) { http_response_code(400); echo '{"ok":false,"hata":"gecersiz"}'; exit; }
if (!empty($v['hp'])) { echo '{"ok":true}'; exit; }   // bot tuzağı doluysa sessizce yok say

$c = $v['iletisim'] ?? [];
$temiz = fn($s, $n = 200) => mb_substr(trim(strip_tags((string)$s)), 0, $n);
$kayit = [
  'zaman'    => date('c'),
  'rapor_no' => $temiz($v['rapor_no'] ?? '', 40),
  'arac'     => $temiz($v['arac'] ?? '', 120),
  'ad'       => $temiz($c['ad'] ?? ''),
  'firma'    => $temiz($c['firma'] ?? ''),
  'eposta'   => filter_var($c['eposta'] ?? '', FILTER_VALIDATE_EMAIL) ?: '',
  'telefon'  => $temiz($c['telefon'] ?? '', 40),
  'not'      => $temiz($c['not'] ?? '', 1000),
  'sayfa'    => $temiz($v['sayfa'] ?? '', 300),
  'ip'       => $_SERVER['REMOTE_ADDR'] ?? '',
  'veri'     => $v['veri'] ?? null,
];
if (!$kayit['ad'] || !$kayit['firma'] || !$kayit['eposta']) { http_response_code(422); echo '{"ok":false,"hata":"eksik"}'; exit; }

// basit hız sınırı: aynı IP'den dakikada 5 kayıt
$sinir = sys_get_temp_dir() . '/arac_' . md5($kayit['ip']);
$z = @json_decode(@file_get_contents($sinir), true) ?: [];
$z = array_filter($z, fn($t) => $t > time() - 60);
if (count($z) >= 5) { http_response_code(429); echo '{"ok":false}'; exit; }
$z[] = time(); @file_put_contents($sinir, json_encode(array_values($z)));

if (!is_dir(KAYIT_DIZINI)) @mkdir(KAYIT_DIZINI, 0750, true);
// 1) tam kayıt (JSON satırları)
file_put_contents(KAYIT_DIZINI . '/kayitlar-' . date('Y-m') . '.jsonl', json_encode($kayit, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);
// 2) Excel'de açılabilen özet (CSV, UTF-8 BOM)
$csv = KAYIT_DIZINI . '/ozet-' . date('Y-m') . '.csv';
$yeni = !file_exists($csv);
$f = fopen($csv, 'a');
if ($yeni) { fwrite($f, "\xEF\xBB\xBF"); fputcsv($f, ['Zaman','Rapor no','Araç','Ad','Firma','E-posta','Telefon','Not'], ';'); }
fputcsv($f, [$kayit['zaman'],$kayit['rapor_no'],$kayit['arac'],$kayit['ad'],$kayit['firma'],$kayit['eposta'],$kayit['telefon'],$kayit['not']], ';');
fclose($f);

// 3) e-posta bildirimi
$konu = '=?UTF-8?B?' . base64_encode("Yeni talep: {$kayit['arac']} — {$kayit['firma']}") . '?=';
$govde = "Rapor no: {$kayit['rapor_no']}\nAraç: {$kayit['arac']}\n\nAd: {$kayit['ad']}\nFirma: {$kayit['firma']}\nE-posta: {$kayit['eposta']}\nTelefon: {$kayit['telefon']}\nNot: {$kayit['not']}\n\nAraç verisi:\n"
       . json_encode($kayit['veri'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
@mail(BILDIRIM_EPOSTA, $konu, $govde, "From: " . GONDEREN_EPOSTA . "\r\nReply-To: {$kayit['eposta']}\r\nContent-Type: text/plain; charset=UTF-8");

echo json_encode(['ok' => true, 'rapor_no' => $kayit['rapor_no']]);
