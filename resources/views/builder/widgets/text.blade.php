@php
/**
 * Zengin metin. İçerik yönetici tarafından girildiği için işaretleme
 * korunur; ancak betik ve olay öznitelikleri temizlenir — panelde bir hesap
 * ele geçse bile ziyaretçinin tarayıcısında kod çalışamaz.
 */
$html = \App\Support\Html::sanitize((string) ($settings['body'] ?? ''));
@endphp

<div class="tx">{!! $html !!}</div>
